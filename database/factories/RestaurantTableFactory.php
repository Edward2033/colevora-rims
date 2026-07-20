<?php

namespace Database\Factories;

use App\Models\RestaurantTable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<RestaurantTable>
 */
class RestaurantTableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'table_number' => 'T-'.fake()->unique()->numberBetween(1, 100),
            'capacity' => fake()->randomElement([2, 4, 6, 8]),
            'location' => fake()->randomElement(['Main Hall', 'Patio', 'VIP Room', 'Garden', 'Balcony']),
            'status' => 'available',
            'qr_code' => Str::uuid(),
        ];
    }
}
