<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'purchase_number' => 'PO-'.strtoupper(uniqid()),
            'total_amount' => fake()->randomFloat(2, 100, 1000),
            'status' => fake()->randomElement(['pending', 'received', 'completed', 'cancelled']),
            'created_by' => User::factory(),
        ];
    }
}
