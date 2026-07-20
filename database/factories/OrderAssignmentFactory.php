<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderAssignment>
 */
class OrderAssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'employee_id' => Employee::factory(),
            'assigned_by' => User::factory(),
            'status' => fake()->randomElement(['active', 'completed', 'cancelled']),
        ];
    }
}
