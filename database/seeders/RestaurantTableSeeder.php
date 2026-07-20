<?php

namespace Database\Seeders;

use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;

class RestaurantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [
            // Main Hall
            ['table_number' => 'T-1', 'capacity' => 2, 'location' => 'Main Hall', 'status' => 'available'],
            ['table_number' => 'T-2', 'capacity' => 4, 'location' => 'Main Hall', 'status' => 'available'],
            ['table_number' => 'T-3', 'capacity' => 4, 'location' => 'Main Hall', 'status' => 'available'],
            ['table_number' => 'T-4', 'capacity' => 6, 'location' => 'Main Hall', 'status' => 'available'],
            ['table_number' => 'T-5', 'capacity' => 6, 'location' => 'Main Hall', 'status' => 'available'],

            // Patio
            ['table_number' => 'P-1', 'capacity' => 2, 'location' => 'Patio', 'status' => 'available'],
            ['table_number' => 'P-2', 'capacity' => 4, 'location' => 'Patio', 'status' => 'available'],
            ['table_number' => 'P-3', 'capacity' => 4, 'location' => 'Patio', 'status' => 'available'],

            // VIP Room
            ['table_number' => 'V-1', 'capacity' => 8, 'location' => 'VIP Room', 'status' => 'available'],
            ['table_number' => 'V-2', 'capacity' => 6, 'location' => 'VIP Room', 'status' => 'available'],
        ];

        foreach ($tables as $table) {
            RestaurantTable::firstOrCreate(
                ['table_number' => $table['table_number']],
                $table
            );
        }
    }
}
