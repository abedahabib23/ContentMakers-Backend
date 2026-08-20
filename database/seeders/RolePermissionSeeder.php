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
        'projects.view',
        'projects.create',
        'projects.update',
        'projects.delete',
        'outputs.view',
        'outputs.create',
        'outputs.update',
        'outputs.review',
        'outputs.delete',
        'center_settings.view',
        'center_settings.update',
        'certificates.view',
        'certificates.create',
        'certificates.update',
        'certificates.delete',
        'sessions.view',
        'sessions.create',
        'sessions.update',
        'sessions.delete',
        'registration_forms.view',
        'registration_forms.create',
        'registration_forms.review',
        'attendance.view',
        'attendance.create',
        'attendance.update',
        'attendance.delete',
        'tasks.view',
        'tasks.create',
        'tasks.update',
        'tasks.delete',
        'task_evaluations.view',
        'task_evaluations.create',
        'task_evaluations.update',
        'task_evaluations.delete',
        'trainees.view',
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
