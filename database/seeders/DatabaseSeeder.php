<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        // Guarded rather than firstOrCreate: the factory must not even build
        // its attributes on re-runs, and re-seeding must never rotate the
        // existing admin's password.
        $email = 'test@example.com';

        if (User::query()->where('email', $email)->doesntExist()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => $email,
                'type' => UserType::SuperAdmin,
            ]);
        }
    }
}
