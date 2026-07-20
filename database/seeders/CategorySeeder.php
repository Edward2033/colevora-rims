<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();

        $categories = [
            ['name' => 'Appetizers', 'slug' => 'appetizers', 'description' => 'Start your meal with our delicious appetizers', 'status' => 'active'],
            ['name' => 'Main Course', 'slug' => 'main-course', 'description' => 'Our signature main dishes', 'status' => 'active'],
            ['name' => 'Desserts', 'slug' => 'desserts', 'description' => 'Sweet treats to end your meal', 'status' => 'active'],
            ['name' => 'Beverages', 'slug' => 'beverages', 'description' => 'Refreshing drinks and beverages', 'status' => 'active'],
            ['name' => 'Hot Drinks', 'slug' => 'hot-drinks', 'description' => 'Coffee, tea, and hot beverages', 'status' => 'active'],
            ['name' => 'Cold Drinks', 'slug' => 'cold-drinks', 'description' => 'Juices, smoothies, and cold beverages', 'status' => 'active'],
            ['name' => 'Alcoholic Drinks', 'slug' => 'alcoholic-drinks', 'description' => 'Beer, wine, and cocktails', 'status' => 'active'],
            ['name' => 'Salads', 'slug' => 'salads', 'description' => 'Fresh and healthy salad options', 'status' => 'active'],
            ['name' => 'Soups', 'slug' => 'soups', 'description' => 'Warm and comforting soups', 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, ['created_by' => $adminUser?->id])
            );
        }
    }
}
