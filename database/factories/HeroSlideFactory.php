<?php

namespace Database\Factories;

use App\Models\HeroSlide;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HeroSlide>
 */
class HeroSlideFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'subtitle' => fake()->sentence(6),
            'image' => 'slides/'.fake()->uuid().'.jpg',
            'button_text' => fake()->randomElement(['Order Now', 'View Menu', 'Book Table', 'Learn More']),
            'button_link' => fake()->randomElement(['/menu', '/order', '/book', '/about']),
            'status' => fake()->randomElement(['active', 'inactive']),
            'ordering' => fake()->numberBetween(1, 10),
        ];
    }
}
