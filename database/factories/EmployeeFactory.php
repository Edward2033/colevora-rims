<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'employee_number' => 'EMP'.fake()->unique()->numberBetween(1000, 9999),
            'job_title' => fake()->randomElement([
                'Manager',
                'Chef',
                'Sous Chef',
                'Waiter',
                'Waitress',
                'Cashier',
                'Receptionist',
                'Inventory Officer',
                'Kitchen Staff',
            ]),
            'department' => fake()->randomElement([
                'Management',
                'Kitchen',
                'Service',
                'Finance',
                'Front Desk',
                'Inventory',
            ]),
            'hire_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'employment_status' => fake()->randomElement(['active', 'on_leave', 'terminated']),
            'approval_status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'created_by' => null,
        ];
    }
}
