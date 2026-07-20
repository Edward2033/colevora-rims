<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Food;
use App\Models\FoodAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FoodAssignment>
 */
class FoodAssignmentFactory extends Factory
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
            'employee_id' => Employee::factory(),
            'assigned_by' => User::factory(),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}
