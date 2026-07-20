<?php

use App\Models\Food;
use App\Models\Category;
use function Livewire\Volt\{state, computed, mount, layout, uses};
use Livewire\WithPagination;

layout('components.layouts.public');
uses([WithPagination::class]);
state(['search' => '']);
state(['selectedCategory' => null]);
state(['sortBy' => 'name']);
state(['viewMode' => 'grid']);
state(['categories' => collect([])]);

mount(function () {
    $this->categories = Category::where('status', 'active')->withCount('foods')->get();
});

$foods = computed(function () {
    $query = Food::where('status', 'active')
        ->where('availability', true)
        ->with('category');
    
    if ($this->search) {
        $query->where(function($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhere('description', 'like', '%' . $this->search . '%');
        });
    }
    
    if ($this->selectedCategory) {
        $query->whereHas('category', function($q) {
            $q->where('slug', $this->selectedCategory);
        });
    }
    
    switch ($this->sortBy) {
        case 'price_low':
            $query->orderBy('price', 'asc');
            break;
        case 'price_high':
            $query->orderBy('price', 'desc');
            break;
        case 'name':
        default:
            $query->orderBy('name', 'asc');
            break;
    }
    
    return $query->paginate(12);
});

$addToCart = function ($foodId) {
    $food = Food::findOrFail($foodId);
    $price = $food->discount_price ?? $food->price;

    if (!auth()->check()) {
        $sessionCart = session()->get('guest_cart', []);
        if (isset($sessionCart[$foodId])) {
            $sessionCart[$foodId]['quantity']++;
        } else {
            $sessionCart[$foodId] = ['quantity' => 1, 'price' => $price];
        }
        session()->put('guest_cart', $sessionCart);
        $this->dispatch('cart-updated');
        $this->dispatch('notify', ['message' => 'Item added to cart!', 'type' => 'success']);
        return;
    }

    $cart = \App\Models\Cart::firstOrCreate([
        'user_id' => auth()->id(),
        'status' => 'active',
    ]);
    $cart->addItem($food, 1);
    $this->dispatch('cart-updated');
    $this->dispatch('notify', ['message' => 'Item added to cart!', 'type' => 'success']);
};

$filterCategory = function ($slug) {
    $this->selectedCategory = $this->selectedCategory === $slug ? null : $slug;
    $this->resetPage();
};

$updatedSearch = function () { $this->resetPage(); };
$updatedSortBy = function () { $this->resetPage(); };

?>

<div class="min-h-screen bg-gray-50">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-orange-600 to-red-600 text-white py-16">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Our Menu</h1>
                <p class="text-xl">Discover our delicious selection of dishes and drinks</p>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar Filters -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                        <!-- Search -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search dishes..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>

                        <!-- Categories -->
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Categories</h3>
                            <div class="space-y-2">
                                <button wire:click="filterCategory(null)" 
                                        class="w-full text-left px-4 py-2 rounded-lg transition {{ !$selectedCategory ? 'bg-orange-100 text-orange-600 font-medium' : 'hover:bg-gray-100' }}">
                                    All Items
                                </button>
                                @foreach($categories as $category)
                                <button wire:click="filterCategory('{{ $category->slug }}')" 
                                        class="w-full text-left px-4 py-2 rounded-lg transition flex justify-between items-center {{ $selectedCategory === $category->slug ? 'bg-orange-100 text-orange-600 font-medium' : 'hover:bg-gray-100' }}">
                                    <span>{{ $category->name }}</span>
                                    <span class="text-xs bg-gray-200 px-2 py-1 rounded-full">{{ $category->foods_count }}</span>
                                </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Sort -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Sort By</label>
                            <select wire:model.live="sortBy" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="name">Name (A-Z)</option>
                                <option value="price_low">Price (Low to High)</option>
                                <option value="price_high">Price (High to Low)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Food Grid -->
                <div class="lg:col-span-3">
                    <!-- Results Info -->
                    <div class="mb-6 flex justify-between items-center">
                        <p class="text-gray-600">
                            <span class="font-semibold">{{ $this->foods->total() }}</span> items found
                        </p>
                        <div class="flex items-center space-x-2">
                            <button wire:click="$set('viewMode','grid')" class="p-2 rounded-lg transition {{ $viewMode === 'grid' ? 'bg-orange-100 text-orange-600' : 'text-gray-400 hover:bg-gray-100' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            </button>
                            <button wire:click="$set('viewMode','list')" class="p-2 rounded-lg transition {{ $viewMode === 'list' ? 'bg-orange-100 text-orange-600' : 'text-gray-400 hover:bg-gray-100' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                        </div>
                    </div>

                    @if($this->foods->count() > 0)
                    <div class="{{ $viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6' : 'flex flex-col space-y-4' }}">
                        @foreach($this->foods as $food)
                        <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition overflow-hidden group {{ $viewMode === 'list' ? 'flex' : '' }}">
                            <div class="relative {{ $viewMode === 'list' ? 'w-40 flex-shrink-0' : 'h-48' }} overflow-hidden">
                                @if($food->image)
                                <img src="{{ asset('storage/' . $food->image) }}" alt="{{ $food->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                @endif
                                @if($food->discount_price)
                                <div class="absolute top-4 right-4 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                    {{ round((($food->price - $food->discount_price) / $food->price) * 100) }}% OFF
                                </div>
                                @endif
                            </div>
                            
                            <div class="p-6">
                                <div class="mb-2">
                                    <span class="inline-block bg-orange-100 text-orange-600 text-xs px-3 py-1 rounded-full">
                                        {{ $food->category->name ?? 'Uncategorized' }}
                                    </span>
                                </div>
                                
                                <h3 class="font-bold text-xl mb-2 text-gray-800">{{ $food->name }}</h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $food->description }}</p>
                                
                                <div class="flex items-center justify-between">
                                    <div>
                                        @if($food->discount_price)
                                        <span class="text-sm text-gray-500 line-through mr-2">${{ number_format($food->price, 2) }}</span>
                                        <span class="text-2xl font-bold text-orange-600">${{ number_format($food->discount_price, 2) }}</span>
                                        @else
                                        <span class="text-2xl font-bold text-orange-600">${{ number_format($food->price, 2) }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <a href="{{ route('food.show', $food->id) }}" class="text-gray-600 hover:text-orange-600 transition">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <button wire:click="addToCart({{ $food->id }})" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <span class="hidden md:inline">Add</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12">
                        <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No items found</h3>
                        <p class="text-gray-600 mb-4">Try adjusting your search or filters</p>
                        <button wire:click="$set('search', ''); $set('selectedCategory', null);" class="text-orange-600 hover:text-orange-700 font-medium">
                            Clear Filters
                        </button>
                    </div>
                    @endif

                    @if($this->foods->hasPages())
                    <div class="mt-8">{{ $this->foods->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
