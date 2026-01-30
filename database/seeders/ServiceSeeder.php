<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::create(['service_name' => 'Photography', 'service_price' => 5000.00]);
        Service::create(['service_name' => 'Catering', 'service_price' => 10000.00]);
        Service::create(['service_name' => 'Sound System & Lights', 'service_price' => 7500.00]);
    }
}
