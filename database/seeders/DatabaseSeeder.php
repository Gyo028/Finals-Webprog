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
         * These must run first because they provide the foreign keys 
         * for Users and Bookings.
         */
        $this->call([
            RoleSeeder::class,    // role_id 1: Manager, 2: Client
            VenueSeeder::class,   // venue_id
            EventSeeder::class,   // event_id
            PaxSeeder::class,     // pax_id
            ServiceSeeder::class, // service_id
        ]);

        /**
         * 2. SEED MANAGER USER & PROFILE
         * Creates the login credentials AND the associated profile.
         */
        $adminUser = User::create([
            'username'      => 'admin_manager',
            'email'         => 'admin@test.com',
            'password'      => Hash::make('password'), // Always hash passwords
            'role_id'       => 1, // Points to 'Manager'
            'mobile_number' => '09123456789',
            'IsActive'      => true,
        ]);

        Manager::create([
            'user_id'     => $adminUser->user_id,
            'first_name'  => 'System',
            'middle_name' => 'The',
            'last_name'   => 'Admin',
            'bday'        => '1990-01-01',
            'IsActive'    => true,
        ]);

        /**
         * 3. SEED CLIENT USER & PROFILE
         * This allows you to log in as a client immediately.
         */
        $clientUser = User::create([
            'username'      => 'john_doe',
            'email'         => 'john@test.com',
            'password'      => Hash::make('password'),
            'role_id'       => 2, // Points to 'Client'
            'mobile_number' => '09987654321',
            'IsActive'      => true,
        ]);

        Client::create([
            'user_id'     => $clientUser->user_id,
            'first_name'  => 'John',
            'middle_name' => 'Quincy',
            'last_name'   => 'Doe',
            'bday'        => '1995-05-20',
            'IsActive'    => true,
        ]);
        
        $this->command->info('Database fully seeded with Roles, Venues, Events, and default Users!');
    }
}