<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['customer_name' => 'Sarah Johnson', 'customer_title' => 'Food Blogger', 'content' => 'Absolutely incredible dining experience! The flavors were outstanding and the service was impeccable. Colevora has become my go-to restaurant for special occasions.', 'rating' => 5, 'order' => 1],
            ['customer_name' => 'Michael Chen', 'customer_title' => 'Regular Customer', 'content' => 'The online ordering system is so convenient. Food always arrives fresh and exactly as described. The chef\'s specials are always a delight!', 'rating' => 5, 'order' => 2],
            ['customer_name' => 'Emily Rodriguez', 'customer_title' => 'Food Enthusiast', 'content' => 'Best restaurant in town! The ambiance is perfect, the food is divine, and the staff are incredibly friendly. Highly recommend the signature dishes.', 'rating' => 5, 'order' => 3],
            ['customer_name' => 'David Thompson', 'customer_title' => 'Business Executive', 'content' => 'We host all our business dinners at Colevora. The private dining experience is exceptional and the menu never disappoints our guests.', 'rating' => 4, 'order' => 4],
            ['customer_name' => 'Lisa Park', 'customer_title' => 'Local Resident', 'content' => 'The reservation system is seamless and the table was ready exactly on time. The food quality is consistently excellent every single visit.', 'rating' => 5, 'order' => 5],
        ];

        foreach ($testimonials as $t) {
            Testimonial::firstOrCreate(['customer_name' => $t['customer_name']], array_merge($t, ['status' => 'active']));
        }
    }
}
