<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $food = Food::factory()->create();
        $quantity = fake()->numberBetween(1, 5);
        $price = $food->effective_price;
        $subtotal = $price * $quantity;

        return [
            'order_id' => Order::factory(),
            'food_id' => $food->id,
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $subtotal,
            'special_notes' => fake()->optional()->sentence(),
        ];
    }
}
