<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    // Hardcoded rather than config('auth.defaults.guard') — the app has
    // multiple guards, and Permission/Role rows must always land on the
    // one User::hasRoles actually checks against (see User::$guard_name).
    private const GUARD = 'api';

    /**
     * @var list<string>
     */
    private const PERMISSIONS = [
        'users.view',
        'users.create',
        'users.update',
        'users.delete',
        'roles.view',
        'roles.create',
        'roles.update',
        'roles.delete',
        'roles.assign',
        'permissions.view',
        'trainers.view',
        'trainers.update',
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::findOrCreate($permission, self::GUARD);
        }

        // DatabaseSeeder runs this with WithoutModelEvents, which mutes
        // Spatie's own cache-invalidation observers.
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
