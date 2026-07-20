<?php

use App\Models\Testimonial;
use function Livewire\Volt\{state, mount, layout};

layout('components.layouts.public');

state(['testimonials' => collect([])]);

mount(function () {
    $this->testimonials = Testimonial::where('status', 'active')->orderBy('order')->get();
});

?>

<div class="min-h-screen bg-gray-50">
    {{-- Hero --}}
    <div class="bg-gradient-to-r from-orange-600 to-red-600 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-3">What Our Guests Say</h1>
            <p class="text-xl text-orange-100">Real experiences from real people</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="bg-white border-b border-gray-100 py-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                @foreach([['10k+','Happy Customers'],['4.9','Average Rating'],['500+','5-Star Reviews'],['10+','Years Serving']] as [$num,$label])
                <div>
                    <div class="text-3xl font-extrabold text-orange-600 mb-1">{{ $num }}</div>
                    <div class="text-gray-500 text-sm">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Testimonials Grid --}}
    <div class="container mx-auto px-4 py-16">
        @if($testimonials->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($testimonials as $t)
            <div class="bg-white rounded-2xl shadow-sm p-7 hover:shadow-md transition border border-gray-100">
                <div class="flex text-yellow-400 mb-4">
                    @for($s = 1; $s <= 5; $s++)
                    <svg class="w-5 h-5 {{ $s <= $t->rating ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                <p class="text-gray-600 leading-relaxed mb-5">"{{ $t->content }}"</p>
                <div class="flex items-center space-x-3">
                    @if($t->customer_photo)
                    <img src="{{ asset('storage/' . $t->customer_photo) }}" alt="{{ $t->customer_name }}" class="w-11 h-11 rounded-full object-cover">
                    @else
                    <div class="w-11 h-11 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold">
                        {{ strtoupper(substr($t->customer_name, 0, 1)) }}
                    </div>
                    @endif
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $t->customer_name }}</p>
                        @if($t->customer_title)
                        <p class="text-gray-400 text-xs">{{ $t->customer_title }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-16">
            <p class="text-gray-500 text-lg">No reviews yet. Be the first to share your experience!</p>
            <a href="{{ route('contact') }}" class="inline-block mt-4 bg-orange-600 hover:bg-orange-700 text-white font-medium px-6 py-3 rounded-lg transition">
                Leave a Review
            </a>
        </div>
        @endif
    </div>

    {{-- CTA --}}
    <div class="bg-gradient-to-r from-orange-600 to-red-600 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-3">Want to Share Your Experience?</h2>
            <p class="text-orange-100 mb-7 max-w-xl mx-auto">We'd love to hear about your dining experience. Your feedback helps us improve!</p>
            <a href="{{ route('contact') }}" class="inline-block bg-white hover:bg-gray-100 text-orange-600 font-bold px-8 py-4 rounded-xl transition">
                Leave a Review
            </a>
        </div>
    </div>
</div>
