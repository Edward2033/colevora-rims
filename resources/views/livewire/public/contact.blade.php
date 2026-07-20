<?php

use App\Models\SiteSetting;
use function Livewire\Volt\{state, rules, layout};

layout('components.layouts.public');
state(['name' => '']);
state(['email' => '']);
state(['phone' => '']);
state(['subject' => '']);
state(['message' => '']);

rules([
    'name' => 'required|string|max:255',
    'email' => 'required|email',
    'phone' => 'nullable|string|max:20',
    'subject' => 'required|string|max:255',
    'message' => 'required|string|min:10',
]);

$submit = function () {
    $this->validate();
    session()->flash('success', 'Thank you for contacting us! We will get back to you soon.');
    $this->reset(['name', 'email', 'phone', 'subject', 'message']);
};

?>

<div class="min-h-screen bg-gray-50">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-orange-600 to-red-600 text-white py-20">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-5xl font-bold mb-4">Contact Us</h1>
                <p class="text-xl">We'd love to hear from you</p>
            </div>
        </div>

        <div class="container mx-auto px-4 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Contact Information -->
                <div class="space-y-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">Get in Touch</h2>
                        <p class="text-gray-600 mb-8">
                            Have a question or feedback? Fill out the form and we'll get back to you as soon as possible.
                        </p>
                    </div>

                    <!-- Contact Cards -->
                    <div class="space-y-4">
                        @php
                            $contactItems = [
                                ['M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z', 'Address', SiteSetting::get('address', '123 Restaurant Street, City')],
                                ['M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'Phone', SiteSetting::get('phone', '+1 (234) 567-8900') . (SiteSetting::get('phone_secondary') ? "\n" . SiteSetting::get('phone_secondary') : '')],
                                ['M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'Email', SiteSetting::get('email', 'info@colevora.com')],
                                ['M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'Hours', 'Mon–Fri: ' . SiteSetting::get('opening_hours_mon_fri','11AM–10PM') . "\nSat: " . SiteSetting::get('opening_hours_sat','10AM–11PM') . "\nSun: " . SiteSetting::get('opening_hours_sun','10AM–9PM')],
                            ];
                        @endphp
                        @foreach($contactItems as [$path, $title, $value])
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-1">{{ $title }}</h3>
                                    <p class="text-gray-600 whitespace-pre-line text-sm">{{ $value }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @if($mapUrl = SiteSetting::get('map_embed_url'))
                        <div class="rounded-lg overflow-hidden shadow-md">
                            <iframe src="{{ $mapUrl }}" width="100%" height="200" style="border:0" allowfullscreen loading="lazy"></iframe>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8">Send us a Message</h2>

                        @if (session()->has('success'))
                        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                            {{ session('success') }}
                        </div>
                        @endif

                        <form wire:submit="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Name *</label>
                                    <input type="text" wire:model="name" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" 
                                           placeholder="Your full name">
                                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                                    <input type="email" wire:model="email" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" 
                                           placeholder="your@email.com">
                                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                                    <input type="tel" wire:model="phone" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" 
                                           placeholder="+1 (234) 567-8900">
                                    @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Subject *</label>
                                    <input type="text" wire:model="subject" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" 
                                           placeholder="How can we help?">
                                    @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Message *</label>
                                <textarea wire:model="message" rows="6" 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" 
                                          placeholder="Tell us more about your inquiry..."></textarea>
                                @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <button type="submit" 
                                    class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-lg transition">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
