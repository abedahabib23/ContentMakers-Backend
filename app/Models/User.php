<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return HasMany<RefreshToken, $this>
     */
    public function refreshTokens(): HasMany
    {
        return $this->hasMany(RefreshToken::class);
    }

    /**
     * @return BelongsToMany<Role, $this>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->authorizationData()['roles'], true);
    }

    public function hasPermissionTo(string $permission): bool
    {
        if ($this->hasRole(Role::SUPER_ADMIN)) {
            return true;
        }

        return in_array($permission, $this->authorizationData()['permissions'], true);
    }

    /**
     * Role and permission names for this user, cached across requests since
     * this backs every authorization check (see Gate::before in
     * AppServiceProvider). Invalidated by RoleService whenever a role's
     * permissions or a user's role assignments change.
     *
     * @return array{roles: array<int, string>, permissions: array<int, string>}
     */
    public function authorizationData(): array
    {
        return once(fn () => Cache::remember(
            "rbac:user:{$this->id}",
            now()->addHours(6),
            function () {
                $roles = $this->roles()->with('permissions')->get();

                return [
                    'roles' => $roles->pluck('name')->all(),
                    'permissions' => $roles->pluck('permissions')->flatten()
                        ->pluck('name')->unique()->values()->all(),
                ];
            },
        ));
    }

    public static function forgetAuthorizationCache(int $userId): void
    {
        Cache::forget("rbac:user:{$userId}");
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
