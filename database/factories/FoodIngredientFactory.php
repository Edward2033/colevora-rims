<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\FoodIngredient;
use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FoodIngredient>
 */
class FoodIngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'food_id' => Food::factory(),
            'inventory_item_id' => InventoryItem::factory(),
            'quantity_required' => fake()->randomFloat(2, 0.1, 5),
            'unit' => fake()->randomElement(['kg', 'g', 'l', 'ml', 'pcs']),
        ];
    }
}
