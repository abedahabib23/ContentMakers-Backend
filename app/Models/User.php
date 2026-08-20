<?php

namespace App\Models;

use App\Enums\UserType;
use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, MustVerifyEmailTrait, Notifiable;

    protected string $guard_name = 'api';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'type' => UserType::class,
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
     * Structural, not behavioral — says what kind of account this is, never
     * what it may do. Only super_admin feeds into authorization, as a
     * global bypass (see Gate::before in AppServiceProvider); every other
     * access decision belongs to RBAC (roles/permissions).
     */
    public function isSuperAdmin(): bool
    {
        return $this->type === UserType::SuperAdmin;
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Only `type` goes in the token — roles/permissions are deliberately
     * excluded and re-checked from the database on every request, never
     * trusted from a claim that outlives a role change until the token
     * expires.
     *
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return ['type' => $this->type->value];
    }
}
