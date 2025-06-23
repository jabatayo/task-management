<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(
            ['name' => 'Administrator'],
            ['description' => 'Full access to all features and data']
        );

        Role::updateOrCreate(
            ['name' => 'Regular User'],
            ['description' => 'Standard user with limited access']
        );
    }
}
