<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['role_name' => 'Manager', 'role_description' => 'Staff who verifies bookings']);
        Role::create(['role_name' => 'Client', 'role_description' => 'Customers who book events']);
    }
}
