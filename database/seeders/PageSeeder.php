<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about',
                'title' => 'About Us',
                'content' => '<h1>About Colevora Restaurant</h1><p>Welcome to Colevora, where culinary excellence meets exceptional service. Our story began with a passion for creating memorable dining experiences.</p>',
                'meta_data' => [
                    'meta_title' => 'About Us - Colevora Restaurant',
                    'meta_description' => 'Learn about Colevora Restaurant, our history, and our commitment to excellence.',
                ],
                'status' => 'active',
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'content' => '<h1>Get in Touch</h1><p>We would love to hear from you. Visit us or reach out through any of our contact channels.</p>',
                'meta_data' => [
                    'meta_title' => 'Contact Us - Colevora Restaurant',
                    'meta_description' => 'Contact Colevora Restaurant for reservations, inquiries, or feedback.',
                ],
                'status' => 'active',
            ],
            [
                'slug' => 'gallery',
                'title' => 'Gallery',
                'content' => '<h1>Our Gallery</h1><p>Explore our restaurant ambiance, signature dishes, and special moments captured in our gallery.</p>',
                'meta_data' => [
                    'meta_title' => 'Gallery - Colevora Restaurant',
                    'meta_description' => 'Browse through our photo gallery showcasing our restaurant, dishes, and events.',
                ],
                'status' => 'active',
            ],
            [
                'slug' => 'testimonials',
                'title' => 'Testimonials',
                'content' => '<h1>What Our Guests Say</h1><p>Read testimonials from our valued customers and discover why they love dining at Colevora.</p>',
                'meta_data' => [
                    'meta_title' => 'Testimonials - Colevora Restaurant',
                    'meta_description' => 'Customer reviews and testimonials about their dining experience at Colevora.',
                ],
                'status' => 'active',
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
