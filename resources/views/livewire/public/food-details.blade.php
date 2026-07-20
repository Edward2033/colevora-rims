<?php

use App\Models\Food;
use function Livewire\Volt\{state, mount, layout};

layout('components.layouts.public');
state(['food' => null]);
state(['quantity' => 1]);
state(['relatedFoods' => collect([])]);

mount(function ($id) {
    $this->food = Food::with(['category', 'foodIngredients.inventoryItem'])->findOrFail($id);
    $this->relatedFoods = Food::where('category_id', $this->food->category_id)
        ->where('id', '!=', $this->food->id)
        ->where('status', 'active')
        ->where('availability', true)
        ->inRandomOrder()
        ->limit(4)
        ->get();
});

$addToCart = function () {
    $food = $this->food;
    $price = $food->discount_price ?? $food->price;

    if (!auth()->check()) {
        $sessionCart = session()->get('guest_cart', []);
        $foodId = $food->id;
        if (isset($sessionCart[$foodId])) {
            $sessionCart[$foodId]['quantity'] += $this->quantity;
        } else {
            $sessionCart[$foodId] = ['quantity' => $this->quantity, 'price' => $price];
        }
        session()->put('guest_cart', $sessionCart);
        $this->dispatch('cart-updated');
        $this->dispatch('notify', ['message' => "{$this->quantity} item(s) added to cart!", 'type' => 'success']);
        $this->quantity = 1;
        return;
    }

    $cart = \App\Models\Cart::firstOrCreate([
        'user_id' => auth()->id(),
        'status' => 'active',
    ]);
    $cart->addItem($this->food, $this->quantity);
    $this->dispatch('cart-updated');
    $this->dispatch('notify', ['message' => "{$this->quantity} item(s) added to cart!", 'type' => 'success']);
    $this->quantity = 1;
};

$increment = function () {
    $this->quantity++;
};

$decrement = function () {
    if ($this->quantity > 1) {
        $this->quantity--;
    }
};

?>

<div class="min-h-screen bg-gray-50 py-12">
        <div class="container mx-auto px-4">
            <!-- Breadcrumb -->
            <nav class="mb-8 text-sm">
                <ol class="flex items-center space-x-2 text-gray-600">
                    <li><a href="{{ route('home') }}" class="hover:text-orange-600">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li><a href="{{ route('menu') }}" class="hover:text-orange-600">Menu</a></li>
                    @if($food->category)
                    <li><span class="mx-2">/</span></li>
                    <li><a href="{{ route('menu', ['category' => $food->category->slug]) }}" class="hover:text-orange-600">{{ $food->category->name }}</a></li>
                    @endif
                    <li><span class="mx-2">/</span></li>
                    <li class="text-gray-900 font-medium">{{ $food->name }}</li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
                <!-- Image Gallery -->
                <div>
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">
                        @if($food->image)
                        <img src="{{ asset('storage/' . $food->image) }}" alt="{{ $food->name }}" class="w-full h-96 object-cover">
                        @else
                        <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                            <svg class="w-32 h-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Product Info -->
                <div>
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        @if($food->category)
                        <span class="inline-block bg-orange-100 text-orange-600 text-sm px-4 py-2 rounded-full mb-4">
                            {{ $food->category->name }}
                        </span>
                        @endif

                        <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $food->name }}</h1>

                        <div class="mb-6">
                            @if($food->discount_price)
                            <div class="flex items-baseline space-x-4">
                                <span class="text-4xl font-bold text-orange-600">${{ number_format($food->discount_price, 2) }}</span>
                                <span class="text-2xl text-gray-500 line-through">${{ number_format($food->price, 2) }}</span>
                                <span class="bg-red-600 text-white text-sm px-3 py-1 rounded-full">
                                    {{ round((($food->price - $food->discount_price) / $food->price) * 100) }}% OFF
                                </span>
                            </div>
                            @else
                            <span class="text-4xl font-bold text-orange-600">${{ number_format($food->price, 2) }}</span>
                            @endif
                        </div>

                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-2">Description</h3>
                            <p class="text-gray-600 leading-relaxed">{{ $food->description }}</p>
                        </div>

                        @if($food->foodIngredients->count() > 0)
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-2">Ingredients</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($food->foodIngredients as $ingredient)
                                <span class="bg-gray-100 text-gray-700 text-sm px-3 py-1 rounded-full">
                                    {{ $ingredient->inventoryItem->name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Quantity Selector -->
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">Quantity</h3>
                            <div class="flex items-center space-x-4">
                                <button wire:click="decrement" class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <span class="text-2xl font-bold w-16 text-center">{{ $quantity }}</span>
                                <button wire:click="increment" class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        <button wire:click="addToCart" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-lg transition flex items-center justify-center space-x-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Add to Cart</span>
                        </button>

                        <!-- Availability Status -->
                        <div class="mt-6 flex items-center space-x-2">
                            @if($food->availability)
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-green-600 font-medium">In Stock</span>
                            @else
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-red-600 font-medium">Out of Stock</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            @if($relatedFoods->count() > 0)
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-8">You May Also Like</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedFoods as $relatedFood)
                    <a href="{{ route('food.show', $relatedFood->id) }}" class="bg-white rounded-xl shadow-md hover:shadow-xl transition overflow-hidden group">
                        <div class="relative h-48 overflow-hidden">
                            @if($relatedFood->image)
                            <img src="{{ asset('storage/' . $relatedFood->image) }}" alt="{{ $relatedFood->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                            @else
                            <div class="w-full h-full bg-gray-200"></div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2">{{ $relatedFood->name }}</h3>
                            <span class="text-xl font-bold text-orange-600">${{ number_format($relatedFood->discount_price ?? $relatedFood->price, 2) }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
