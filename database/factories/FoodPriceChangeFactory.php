<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\FoodPriceChange;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FoodPriceChange>
 */
class FoodPriceChangeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $oldPrice = fake()->randomFloat(2, 10, 100);
        $newPrice = fake()->randomFloat(2, 10, 100);

        return [
            'food_id' => Food::factory(),
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'requested_by' => User::factory(),
            'approved_by' => null,
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'approved_at' => null,
            'rejection_reason' => null,
        ];
    }
}
