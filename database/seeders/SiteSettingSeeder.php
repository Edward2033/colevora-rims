<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'restaurant_name', 'value' => 'Colevora Restaurant', 'type' => 'text'],
            ['key' => 'restaurant_tagline', 'value' => 'Experience the Finest Dining', 'type' => 'text'],
            ['key' => 'logo', 'value' => null, 'type' => 'image'],
            ['key' => 'favicon', 'value' => null, 'type' => 'image'],
            ['key' => 'phone', 'value' => '+1 (555) 123-4567', 'type' => 'text'],
            ['key' => 'phone_secondary', 'value' => '+1 (555) 123-4568', 'type' => 'text'],
            ['key' => 'email', 'value' => 'info@colevora.com', 'type' => 'text'],
            ['key' => 'address', 'value' => '123 Main Street, City, State 12345', 'type' => 'textarea'],
            ['key' => 'map_embed_url', 'value' => '', 'type' => 'text'],
            ['key' => 'whatsapp', 'value' => '', 'type' => 'text'],
            ['key' => 'facebook', 'value' => 'https://facebook.com/colevora', 'type' => 'url'],
            ['key' => 'twitter', 'value' => 'https://twitter.com/colevora', 'type' => 'url'],
            ['key' => 'instagram', 'value' => 'https://instagram.com/colevora', 'type' => 'url'],
            ['key' => 'youtube', 'value' => '', 'type' => 'url'],
            ['key' => 'tiktok', 'value' => '', 'type' => 'url'],
            ['key' => 'footer_about', 'value' => 'Experience the finest dining with our exquisite menu and exceptional service.', 'type' => 'textarea'],
            ['key' => 'footer_content', 'value' => '© 2026 Colevora Restaurant. All rights reserved.', 'type' => 'textarea'],
            ['key' => 'opening_hours', 'value' => 'Mon-Fri: 11am-10pm, Sat-Sun: 10am-11pm', 'type' => 'textarea'],
            ['key' => 'opening_hours_mon_fri', 'value' => '11:00 AM – 10:00 PM', 'type' => 'text'],
            ['key' => 'opening_hours_sat', 'value' => '10:00 AM – 11:00 PM', 'type' => 'text'],
            ['key' => 'opening_hours_sun', 'value' => '10:00 AM – 9:00 PM', 'type' => 'text'],
            ['key' => 'meta_description', 'value' => 'Colevora Restaurant – Experience the finest dining with our exquisite menu, professional chefs, and exceptional service.', 'type' => 'textarea'],
            ['key' => 'meta_keywords', 'value' => 'restaurant, dining, food, colevora, menu, reservation', 'type' => 'text'],
            ['key' => 'og_image', 'value' => null, 'type' => 'image'],
            ['key' => 'cookie_consent_text', 'value' => 'We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.', 'type' => 'textarea'],
            ['key' => 'privacy_policy_url', 'value' => '/privacy', 'type' => 'text'],
            ['key' => 'terms_url', 'value' => '/terms', 'type' => 'text'],
            ['key' => 'tax_rate', 'value' => '10', 'type' => 'text'],
            ['key' => 'currency_symbol', 'value' => '$', 'type' => 'text'],
            ['key' => 'delivery_fee', 'value' => '3.99', 'type' => 'text'],
            ['key' => 'min_order_amount', 'value' => '15.00', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }
    }
}
