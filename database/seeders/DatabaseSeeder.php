<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        // Seed users after roles exist
        $this->call([
            UserSeeder::class,
        ]);

        // Seed other data
        $this->call([
            PageSeeder::class,
            SiteSettingSeeder::class,
            CategorySeeder::class,
            RestaurantTableSeeder::class,
            InventoryCategorySeeder::class,
            TestimonialSeeder::class,
        ]);
    }
}
