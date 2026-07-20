<?php

namespace Database\Factories;

use App\Models\InventoryAlert;
use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryAlert>
 */
class InventoryAlertFactory extends Factory
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
            'message' => fake()->sentence(),
            'status' => fake()->randomElement(['active', 'resolved']),
        ];
    }
}
