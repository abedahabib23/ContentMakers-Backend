<?php

namespace App\Services\Rbac;

use App\Exceptions\Rbac\RoleInUseException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    private const GUARD = 'api';

    /**
     * @return Collection<int, Role>
     */
    public function list(): Collection
    {
        return Role::with('permissions')->withCount('users')->get();
    }

    /**
     * @param  array{name: string, description?: string|null, permission_ids?: array<int, int>}  $data
     */
    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'guard_name' => self::GUARD,
            ]);

            if (! empty($data['permission_ids'])) {
                $role->syncPermissions(Permission::whereIn('id', $data['permission_ids'])->get());
            }

            return $role->load('permissions');
        });
    }

    /**
     * @param  array{name?: string, description?: string|null, permission_ids?: array<int, int>}  $data
     */
    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            $role->update(array_intersect_key($data, array_flip(['name', 'description'])));

            if (array_key_exists('permission_ids', $data)) {
                $role->syncPermissions(Permission::whereIn('id', $data['permission_ids'])->get());
            }

            return $role->load('permissions');
        });
    }

    public function delete(Role $role): void
    {
        if ($role->users()->exists()) {
            throw new RoleInUseException;
        }

        $role->delete();
    }
}
