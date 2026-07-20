<?php

namespace Database\Factories;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => InventoryCategory::factory(),
            'supplier_id' => Supplier::factory(),
            'name' => fake()->words(2, true),
            'unit' => fake()->randomElement(['kg', 'g', 'l', 'ml', 'pcs', 'box']),
            'quantity' => fake()->randomFloat(2, 10, 100),
            'minimum_quantity' => fake()->randomFloat(2, 5, 20),
            'cost_price' => fake()->randomFloat(2, 5, 50),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}
