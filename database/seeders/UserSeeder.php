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
        // 1. Existing System Administrator
        $admin = User::create([
            'username'      => 'admin_manager',
            'email'         => 'admin@test.com',
            'password'      => Hash::make('password123'),
            'role_id'       => 1, 
            'mobile_number' => '09123456789',
            'IsActive'      => true,
        ]);

        Manager::create([
            'user_id'     => $admin->user_id,
            'first_name'  => 'System',
            'last_name'   => 'Administrator',
            'bday'        => '1990-01-01',
        ]);

        // 2. NEW MANAGER: Vincent Sola
        $vincent = User::create([
            'username'      => 'admin_vincent',
            'email'         => 'vincent@test.com', // Added a unique email
            'password'      => Hash::make('password123'),
            'role_id'       => 1, // Manager Role
            'mobile_number' => '09111111111',
            'IsActive'      => true,
        ]);

        Manager::create([
            'user_id'     => $vincent->user_id,
            'first_name'  => 'Vincent',
            'last_name'   => 'Sola',
            'bday'        => '1992-01-01',
        ]);

        // 3. Existing Client Account
        $customer = User::create([
            'username'      => 'john_doe',
            'email'         => 'client@test.com',
            'password'      => Hash::make('password123'),
            'role_id'       => 2,
            'mobile_number' => '09987654321',
            'IsActive'      => true,
        ]);

        Client::create([
            'user_id'     => $customer->user_id,
            'first_name'  => 'John',
            'last_name'   => 'Doe',
            'bday'        => '1995-05-20',
        ]);
    }
}