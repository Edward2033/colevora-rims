<?php

namespace Database\Factories;

use App\Models\InventoryCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryCategory>
 */
class InventoryCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Vegetables',
                'Meat & Poultry',
                'Seafood',
                'Dairy',
                'Spices & Herbs',
                'Grains',
                'Beverages',
                'Condiments',
            ]),
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}
