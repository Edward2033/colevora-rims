<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Food;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $food = Food::factory()->create();

        return [
            'cart_id' => Cart::factory(),
            'food_id' => $food->id,
            'quantity' => fake()->numberBetween(1, 5),
            'price' => $food->effective_price,
        ];
    }
}
