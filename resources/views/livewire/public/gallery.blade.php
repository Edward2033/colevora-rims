<?php

use App\Models\Food;
use function Livewire\Volt\{state, mount, layout};

layout('components.layouts.public');
state(['images' => collect([])]);
state(['categories' => collect([])]);

mount(function () {
    // Get food images
    $this->images = Food::whereNotNull('image')
        ->where('status', 'active')
        ->with('category')
        ->get()
        ->map(fn($food) => [
            'url' => asset('storage/' . $food->image),
            'title' => $food->name,
            'category' => $food->category->name ?? 'Uncategorized',
        ]);
});

?>

<div class="min-h-screen bg-gray-50">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-orange-600 to-red-600 text-white py-20">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-5xl font-bold mb-4">Gallery</h1>
                <p class="text-xl">Feast your eyes on our delicious creations</p>
            </div>
        </div>

        <div class="container mx-auto px-4 py-16">
            @if($images->count() > 0)
            <!-- Masonry Grid -->
            <div class="columns-1 md:columns-2 lg:columns-3 xl:columns-4 gap-6 space-y-6">
                @foreach($images as $image)
                <div class="break-inside-avoid">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden group cursor-pointer hover:shadow-xl transition">
                        <div class="relative overflow-hidden">
                            <img src="{{ $image['url'] }}" alt="{{ $image['title'] }}" 
                                 class="w-full h-auto group-hover:scale-110 transition duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-4">
                                <div class="text-white">
                                    <p class="font-bold text-lg">{{ $image['title'] }}</p>
                                    <p class="text-sm">{{ $image['category'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <svg class="w-32 h-32 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">No Images Yet</h2>
                <p class="text-gray-600 mb-8">Check back soon for our gallery</p>
                <a href="{{ route('menu') }}" class="inline-block bg-orange-600 hover:bg-orange-700 text-white font-bold px-8 py-4 rounded-lg transition">
                    Browse Menu
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
