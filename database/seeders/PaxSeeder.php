<?php

namespace Database\Seeders;

use App\Models\Pax;
use Illuminate\Database\Seeder;

class PaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 50 Pax is the minimum (included in base price)
        Pax::create([
            'pax_count' => 50,
            'pax_price' => 0.00, 
        ]);

        // 75 Pax adds 5,000
        Pax::create([
            'pax_count' => 75,
            'pax_price' => 5000.00,
        ]);

        // 100 Pax adds 10,000
        Pax::create([
            'pax_count' => 100,
            'pax_price' => 10000.00,
        ]);

        // 125 Pax adds 15,000
        Pax::create([
            'pax_count' => 125,
            'pax_price' => 15000.00,
        ]);

        // 150 Pax adds 20,000
        Pax::create([
            'pax_count' => 150,
            'pax_price' => 20000.00,
        ]);

        // 175 Pax adds 25,000
        Pax::create([
            'pax_count' => 175,
            'pax_price' => 25000.00,
        ]);

        // 200 Pax adds 30,000
        Pax::create([
            'pax_count' => 200,
            'pax_price' => 30000.00,
        ]);
    }
}