<?php

use App\Models\Food;
use App\Models\Category;
use function Livewire\Volt\{state, computed, layout};

layout('components.layouts.public');

state(['query' => '', 'category_id' => '', 'minPrice' => '', 'maxPrice' => '']);

$results = computed(function () {
    return Food::query()
        ->where('status', 'active')
        ->where('availability', true)
        ->when($this->query, fn($q) => $q->where(fn($sq) =>
            $sq->where('name', 'like', "%{$this->query}%")
               ->orWhere('description', 'like', "%{$this->query}%")
        ))
        ->when($this->category_id, fn($q) => $q->where('category_id', $this->category_id))
        ->when($this->minPrice !== '', fn($q) => $q->where('price', '>=', $this->minPrice))
        ->when($this->maxPrice !== '', fn($q) => $q->where('price', '<=', $this->maxPrice))
        ->with('category')
        ->paginate(12);
});

$categories = computed(fn() => Category::where('status', 'active')->get());

?>

<div class="min-h-screen bg-gray-50">
    {{-- Search Header --}}
    <div class="bg-gradient-to-r from-orange-600 to-red-600 text-white py-16">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold mb-6">Search Menu</h1>
            <div class="max-w-2xl relative">
                <input type="text" wire:model.live.debounce.300ms="query"
                       placeholder="Search for dishes, ingredients..."
                       class="w-full px-5 py-4 pr-14 rounded-xl text-gray-900 text-lg focus:outline-none focus:ring-4 focus:ring-orange-300">
                <svg class="absolute right-5 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Filters --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-20">
                    <h3 class="font-bold text-gray-900 mb-5">Filters</h3>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select wire:model.live="category_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option value="">All Categories</option>
                            @foreach($this->categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                        <div class="flex items-center gap-2">
                            <input type="number" wire:model.live="minPrice" placeholder="Min"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm">
                            <span class="text-gray-400">–</span>
                            <input type="number" wire:model.live="maxPrice" placeholder="Max"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm">
                        </div>
                    </div>

                    <button wire:click="$set('category_id',''); $set('minPrice',''); $set('maxPrice',''); $set('query','')"
                            class="w-full text-sm text-orange-600 hover:text-orange-700 font-medium py-2 border border-orange-200 rounded-lg hover:bg-orange-50 transition">
                        Clear All Filters
                    </button>
                </div>
            </div>

            {{-- Results --}}
            <div class="lg:col-span-3">
                <div class="mb-5 flex justify-between items-center">
                    <p class="text-gray-600 text-sm">
                        Found <span class="font-bold text-gray-900">{{ $this->results->total() }}</span> result(s)
                        @if($query) for "<span class="font-semibold text-orange-600">{{ $query }}</span>" @endif
                    </p>
                </div>

                @if($this->results->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($this->results as $food)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition group">
                        <div class="relative h-44 overflow-hidden">
                            @if($food->image)
                            <img src="{{ asset('storage/' . $food->image) }}" alt="{{ $food->name }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            @else
                            <div class="w-full h-full bg-gradient-to-br from-orange-100 to-red-100 flex items-center justify-center">
                                <svg class="w-14 h-14 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            @endif
                            @if($food->category)
                            <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-full text-xs font-semibold text-gray-700">
                                {{ $food->category->name }}
                            </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 mb-1 line-clamp-1">{{ $food->name }}</h3>
                            <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ $food->description }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-bold text-orange-600">${{ number_format($food->effective_price, 2) }}</span>
                                <a href="{{ route('food.show', $food->id) }}"
                                   class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-8">{{ $this->results->links() }}</div>
                @else
                <div class="bg-white rounded-xl shadow-sm p-16 text-center">
                    <svg class="w-20 h-20 mx-auto text-gray-300 mb-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No results found</h3>
                    <p class="text-gray-500 mb-6">Try different keywords or clear your filters</p>
                    <a href="{{ route('menu') }}" class="inline-block bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-medium transition">
                        Browse Full Menu
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
