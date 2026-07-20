<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(3, true);

        return [
            'slug' => Str::slug($title),
            'title' => $title,
            'content' => fake()->paragraphs(5, true),
            'meta_data' => [
                'meta_title' => $title,
                'meta_description' => fake()->sentence(),
                'meta_keywords' => implode(', ', fake()->words(5)),
            ],
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}
