<?php

namespace App\Services\Rbac;

use App\Exceptions\Rbac\CannotModifyProtectedRoleException;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RoleService
{
    /**
     * @return Collection<int, Role>
     */
    public function list(): Collection
    {
        return Role::with('permissions')->get();
    }

    /**
     * @param  array{name: string, permission_ids?: array<int, int>}  $data
     */
    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create(['name' => $data['name']]);

            if (! empty($data['permission_ids'])) {
                $role->permissions()->sync($data['permission_ids']);
            }

            return $role->load('permissions');
        });
    }

    /**
     * @param  array{name?: string, permission_ids?: array<int, int>}  $data
     */
    public function update(Role $role, array $data): Role
    {
        if ($role->name === Role::SUPER_ADMIN) {
            throw new CannotModifyProtectedRoleException;
        }

        return DB::transaction(function () use ($role, $data) {
            if (array_key_exists('name', $data)) {
                $role->update(['name' => $data['name']]);
            }

            if (array_key_exists('permission_ids', $data)) {
                $role->permissions()->sync($data['permission_ids']);
                $this->forgetCacheForRoleMembers($role);
            }

            return $role->load('permissions');
        });
    }

    public function delete(Role $role): void
    {
        if ($role->name === Role::SUPER_ADMIN) {
            throw new CannotModifyProtectedRoleException;
        }

        DB::transaction(function () use ($role) {
            $this->forgetCacheForRoleMembers($role);
            $role->delete();
        });
    }

    /**
     * @param  array<int, int>  $roleIds
     */
    public function syncUserRoles(User $user, array $roleIds): User
    {
        $user->roles()->sync($roleIds);
        User::forgetAuthorizationCache($user->id);

        return $user->load('roles.permissions');
    }

    private function forgetCacheForRoleMembers(Role $role): void
    {
        $role->users()->pluck('users.id')
            ->each(fn (int $userId) => User::forgetAuthorizationCache($userId));
    }
}
