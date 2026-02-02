<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Manager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create a Manager Account
        $admin = User::create([
            'username'      => 'admin_manager',
            'email'         => 'admin@test.com',
            'password'      => Hash::make('password123'),
            'role_id'       => 1, // Assumes 1 is Manager from your RoleSeeder
            'mobile_number' => '09123456789',
            'IsActive'      => true,
        ]);

        // Create the Manager Profile/Details
        Manager::create([
            'user_id'     => $admin->user_id,
            'first_name'  => 'System',
            'last_name'   => 'Administrator',
            'bday'        => '1990-01-01',
        ]);

        // 2. Create a Client Account
        $customer = User::create([
            'username'      => 'john_doe',
            'email'         => 'client@test.com',
            'password'      => Hash::make('password123'),
            'role_id'       => 2, // Assumes 2 is Client from your RoleSeeder
            'mobile_number' => '09987654321',
            'IsActive'      => true,
        ]);

        // Create the Client Profile/Details
        Client::create([
            'user_id'     => $customer->user_id,
            'first_name'  => 'John',
            'last_name'   => 'Doe',
            'bday'        => '1995-05-20',
        ]);
    }
}