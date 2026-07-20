<?php

namespace Database\Seeders;

use App\Models\InventoryCategory;
use Illuminate\Database\Seeder;

class InventoryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Vegetables', 'description' => 'Fresh vegetables and produce', 'status' => 'active'],
            ['name' => 'Meat & Poultry', 'description' => 'Fresh and frozen meat products', 'status' => 'active'],
            ['name' => 'Seafood', 'description' => 'Fresh and frozen seafood', 'status' => 'active'],
            ['name' => 'Dairy', 'description' => 'Milk, cheese, butter, and dairy products', 'status' => 'active'],
            ['name' => 'Spices & Herbs', 'description' => 'Spices, herbs, and seasonings', 'status' => 'active'],
            ['name' => 'Grains', 'description' => 'Rice, flour, pasta, and grain products', 'status' => 'active'],
            ['name' => 'Beverages', 'description' => 'Drinks and beverage ingredients', 'status' => 'active'],
            ['name' => 'Condiments', 'description' => 'Sauces, oils, and condiments', 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            InventoryCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
