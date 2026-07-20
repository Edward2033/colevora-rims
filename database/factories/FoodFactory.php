<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Food;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Food>
 */
class FoodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 5, 100);
        $hasDiscount = fake()->boolean(30);

        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'image' => 'food/'.fake()->uuid().'.jpg',
            'price' => $price,
            'discount_price' => null,
            'availability' => fake()->boolean(90),
            'status' => fake()->randomElement(['active', 'inactive']),
            'created_by' => User::factory(),
        ];
    }
}
