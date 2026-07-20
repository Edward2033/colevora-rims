<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 20, 200);
        $tax = $subtotal * 0.1;
        $discount = fake()->boolean(30) ? fake()->randomFloat(2, 5, 20) : 0;
        $totalAmount = $subtotal + $tax - $discount;

        return [
            'order_number' => 'ORD-'.strtoupper(uniqid()),
            'customer_id' => User::factory(),
            'table_id' => RestaurantTable::factory(),
            'order_type' => fake()->randomElement(['dine_in', 'takeout', 'delivery']),
            'status' => fake()->randomElement(['pending', 'preparing', 'ready', 'served', 'completed', 'cancelled']),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total_amount' => $totalAmount,
            'notes' => fake()->optional()->sentence(),
            'assigned_waiter_id' => fake()->boolean(70) ? Employee::factory() : null,
        ];
    }
}
