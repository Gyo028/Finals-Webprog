<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Manager;
use App\Models\Role;
use App\Models\Venue;
use App\Models\Event;
use App\Models\Pax;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /**
         * 1. CALL COMPONENT SEEDERS
         */
        $this->call([
            RoleSeeder::class,    // role_id 1: Manager, 2: Client
            VenueSeeder::class,   
            EventSeeder::class,   
            PaxSeeder::class,     
            ServiceSeeder::class, 
        ]);

        /**
         * 2. SEED SYSTEM ADMIN MANAGER
         */
        $adminUser = User::firstOrCreate(
            ['username' => 'admin_manager'],
            [
                'email'         => 'admin@test.com',
                'password'      => Hash::make('password'),
                'role_id'       => 1,
                'mobile_number' => '09123456789',
                'IsActive'      => true,
            ]
        );

        Manager::firstOrCreate(
            ['user_id' => $adminUser->user_id],
            [
                'first_name'  => 'System',
                'middle_name' => 'The',
                'last_name'   => 'Admin',
                'bday'        => '1990-01-01',
                'IsActive'    => true,
            ]
        );

        /**
         * 3. NEW MANAGER: VINCENT SOLA
         */
        $vincentUser = User::firstOrCreate(
            ['username' => 'admin_vincent'],
            [
                'email'         => 'vincent@test.com',
                'password'      => Hash::make('password'),
                'role_id'       => 1,
                'mobile_number' => '09111111111',
                'IsActive'      => true,
            ]
        );

        Manager::firstOrCreate(
            ['user_id' => $vincentUser->user_id],
            [
                'first_name'  => 'Vincent',
                'middle_name' => '',
                'last_name'   => 'Sola',
                'bday'        => '1992-01-01',
                'IsActive'    => true,
            ]
        );

        /**
         * 4. SEED CLIENT USER
         */
        $clientUser = User::firstOrCreate(
            ['username' => 'john_doe'],
            [
                'email'         => 'john@test.com',
                'password'      => Hash::make('password'),
                'role_id'       => 2,
                'mobile_number' => '09987654321',
                'IsActive'      => true,
            ]
        );

        Client::firstOrCreate(
            ['user_id' => $clientUser->user_id],
            [
                'first_name'  => 'John',
                'middle_name' => 'Quincy',
                'last_name'   => 'Doe',
                'bday'        => '1995-05-20',
                'IsActive'    => true,
            ]
        );
        
        $this->command->info('Database fully seeded! Added admin_vincent and handled duplicates.');
    }
}