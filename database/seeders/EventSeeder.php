<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::create(['event_name' => 'Wedding', 'event_base_price' => 50000.00]);
        Event::create(['event_name' => 'Birthday', 'event_base_price' => 20000.00]);
        Event::create(['event_name' => 'Corporate Seminar', 'event_base_price' => 35000.00]);
    }
}
