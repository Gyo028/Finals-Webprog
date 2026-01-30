<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Venue;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        Venue::create(['venue_name' => 'Grand Ballroom', 'venue_address' => '123 Luxury St.', 'isActive' => true]);
        Venue::create(['venue_name' => 'Garden Pavilion', 'venue_address' => '456 Nature Ave.', 'isActive' => true]);
        Venue::create(['venue_name' => 'Roof Deck', 'venue_address' => '789 Skyline Blvd.', 'isActive' => true]);
    }
}