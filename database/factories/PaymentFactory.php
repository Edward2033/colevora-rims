<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'completed', 'failed', 'refunded']);
        $isCompleted = $status === 'completed';

        return [
            'order_id' => Order::factory(),
            'payment_method' => fake()->randomElement(['cash', 'card', 'mobile', 'bank_transfer']),
            'amount' => fake()->randomFloat(2, 20, 300),
            'transaction_reference' => fake()->optional()->uuid(),
            'status' => $status,
            'paid_by' => $isCompleted ? User::factory() : null,
            'paid_at' => $isCompleted ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }
}
