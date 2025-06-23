<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call(RoleSeeder::class);

        // Create or update default admin user
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@task.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
            ]
        );

        // Assign admin role if not already assigned
        $adminRole = Role::where('name', 'Administrator')->first();
        if (!$adminUser->roles()->where('role_id', $adminRole->id)->exists()) {
            $adminUser->roles()->attach($adminRole->id);
        }

        // Create or update a regular user for testing
        $regularUser = User::updateOrCreate(
            ['email' => 'user@task.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password123'),
            ]
        );

        // Assign regular user role if not already assigned
        $userRole = Role::where('name', 'Regular User')->first();
        if (!$regularUser->roles()->where('role_id', $userRole->id)->exists()) {
            $regularUser->roles()->attach($userRole->id);
        }

        // Seed tasks for user_id = 1 (admin user)
        $this->call(TaskSeeder::class);
    }
}
