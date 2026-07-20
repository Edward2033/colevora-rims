<?php

use App\Models\Page;
use function Livewire\Volt\{state, mount, layout};

layout('components.layouts.public');
state(['page' => null]);

mount(function () {
    $this->page = Page::where('slug', 'about')->where('status', 'published')->first();
});

?>

<div class="min-h-screen">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-orange-600 to-red-600 text-white py-20">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-5xl font-bold mb-4">{{ $page?->title ?? 'About Us' }}</h1>
                <p class="text-xl">{{ $page?->meta_description ?? 'Learn about our story and passion for great food' }}</p>
            </div>
        </div>

        <div class="container mx-auto px-4 py-16">
            @if($page)
            <!-- Page Content -->
            <div class="prose prose-lg max-w-none">
                {!! $page->content !!}
            </div>
            @else
            <!-- Default Content -->
            <div class="max-w-4xl mx-auto">
                <div class="mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">Our Story</h2>
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        Welcome to Colevora Restaurant, where culinary excellence meets warm hospitality. 
                        Since our establishment, we have been dedicated to serving the finest dishes crafted 
                        with passion and the freshest ingredients.
                    </p>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        Our journey began with a simple vision: to create a dining experience that brings 
                        people together over exceptional food. Today, we continue to honor that vision by 
                        combining traditional recipes with modern techniques.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                    <div class="text-center p-6 bg-white rounded-lg shadow-md">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">10+ Years</h3>
                        <p class="text-gray-600">Of culinary excellence</p>
                    </div>

                    <div class="text-center p-6 bg-white rounded-lg shadow-md">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">50+ Staff</h3>
                        <p class="text-gray-600">Dedicated team members</p>
                    </div>

                    <div class="text-center p-6 bg-white rounded-lg shadow-md">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">10k+ Happy</h3>
                        <p class="text-gray-600">Satisfied customers</p>
                    </div>
                </div>

                <div class="mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">Our Values</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="flex space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold mb-2">Quality First</h3>
                                <p class="text-gray-600">We source only the finest ingredients for our dishes.</p>
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold mb-2">Fresh Daily</h3>
                                <p class="text-gray-600">All our meals are prepared fresh every day.</p>
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold mb-2">Customer Focused</h3>
                                <p class="text-gray-600">Your satisfaction is our top priority.</p>
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold mb-2">Sustainability</h3>
                                <p class="text-gray-600">We're committed to eco-friendly practices.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
