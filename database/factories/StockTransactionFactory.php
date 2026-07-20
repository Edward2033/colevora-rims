<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockTransaction>
 */
class StockTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'inventory_item_id' => InventoryItem::factory(),
            'type' => fake()->randomElement(['purchase', 'usage', 'adjustment', 'return']),
            'quantity' => fake()->randomFloat(2, -50, 50),
            'reference_type' => null,
            'reference_id' => null,
            'created_by' => User::factory(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
