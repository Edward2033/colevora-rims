<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Food;
use function Livewire\Volt\{state, mount, computed, layout};

layout('components.layouts.public');
state(['cart' => null]);
state(['sessionItems' => []]);

mount(function () {
    if (auth()->check()) {
        $this->cart = Cart::where('user_id', auth()->id())
            ->where('status', 'active')
            ->with(['items.food'])
            ->first();
    } else {
        $this->sessionItems = session()->get('guest_cart', []);
    }
});

$cartItems = computed(function () {
    if (auth()->check()) {
        return $this->cart?->items ?? collect([]);
    }
    $items = [];
    foreach ($this->sessionItems as $foodId => $item) {
        $food = Food::find($foodId);
        if ($food) {
            $items[] = (object)[
                'id' => $foodId,
                'food' => $food,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ];
        }
    }
    return collect($items);
});

$subtotal = computed(function () {
    return $this->cartItems->sum(fn($item) => $item->price * $item->quantity);
});

$updateQuantity = function ($itemId, $quantity) {
    if ($quantity < 1) return;

    if (auth()->check()) {
        $item = CartItem::findOrFail($itemId);
        $item->update(['quantity' => $quantity, 'price' => $item->food->discount_price ?? $item->food->price]);
        $this->cart = $this->cart->fresh(['items.food']);
    } else {
        $sessionCart = session()->get('guest_cart', []);
        if (isset($sessionCart[$itemId])) {
            $sessionCart[$itemId]['quantity'] = $quantity;
            session()->put('guest_cart', $sessionCart);
        }
        $this->sessionItems = session()->get('guest_cart', []);
    }
    $this->dispatch('cart-updated');
};

$removeItem = function ($itemId) {
    if (auth()->check()) {
        CartItem::findOrFail($itemId)->delete();
        $this->cart = $this->cart->fresh(['items.food']);
    } else {
        $sessionCart = session()->get('guest_cart', []);
        unset($sessionCart[$itemId]);
        session()->put('guest_cart', $sessionCart);
        $this->sessionItems = session()->get('guest_cart', []);
    }
    $this->dispatch('cart-updated');
    $this->dispatch('notify', ['message' => 'Item removed from cart', 'type' => 'success']);
};

$clearCart = function () {
    if (auth()->check()) {
        $this->cart?->items()->delete();
        $this->cart = null;
    } else {
        session()->forget('guest_cart');
        $this->sessionItems = [];
    }
    $this->dispatch('cart-updated');
    $this->dispatch('notify', ['message' => 'Cart cleared', 'type' => 'success']);
};

?>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

        @if($this->cartItems->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    @foreach($this->cartItems as $item)
                    <div class="p-6 border-b last:border-b-0" wire:key="cart-item-{{ $item->id }}">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($item->food->image)
                                <img src="{{ asset('storage/' . $item->food->image) }}" alt="{{ $item->food->name }}" class="w-24 h-24 object-cover rounded-lg">
                                @else
                                <div class="w-24 h-24 bg-gray-200 rounded-lg"></div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-lg text-gray-900 mb-1">{{ $item->food->name }}</h3>
                                <p class="text-gray-600 text-sm mb-2">{{ $item->food->category->name ?? 'Uncategorized' }}</p>
                                <p class="text-orange-600 font-bold text-xl">${{ number_format($item->price, 2) }}</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button wire:click="updateQuantity('{{ $item->id }}', {{ $item->quantity - 1 }})"
                                        class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <span class="text-lg font-semibold w-12 text-center">{{ $item->quantity }}</span>
                                <button wire:click="updateQuantity('{{ $item->id }}', {{ $item->quantity + 1 }})"
                                        class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                            <button wire:click="removeItem('{{ $item->id }}')" class="text-red-600 hover:text-red-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <span class="text-gray-600">Subtotal: <span class="font-bold text-gray-900">${{ number_format($item->price * $item->quantity, 2) }}</span></span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <button wire:click="clearCart" wire:confirm="Are you sure you want to clear your cart?" class="text-red-600 hover:text-red-700 font-medium">
                        Clear Cart
                    </button>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Order Summary</h2>
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-semibold">${{ number_format($this->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax (10%)</span>
                            <span class="font-semibold">${{ number_format($this->subtotal * 0.1, 2) }}</span>
                        </div>
                        <div class="border-t pt-4">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span class="text-orange-600">${{ number_format($this->subtotal * 1.1, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    @auth
                        <a href="{{ route('checkout') }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center font-bold py-4 rounded-lg transition">
                            Proceed to Checkout
                        </a>
                    @else
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4 text-sm text-orange-800">
                            Please login or register to proceed to checkout.
                        </div>
                        <a href="{{ route('login') }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center font-bold py-4 rounded-lg transition mb-3">
                            Login to Checkout
                        </a>
                        <a href="{{ route('register') }}" class="block w-full border-2 border-orange-600 text-orange-600 hover:bg-orange-50 text-center font-bold py-3 rounded-lg transition">
                            Create Account
                        </a>
                    @endauth

                    <a href="{{ route('menu') }}" class="block w-full text-center text-orange-600 hover:text-orange-700 font-medium mt-4">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-16">
            <svg class="w-32 h-32 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Your cart is empty</h2>
            <p class="text-gray-600 mb-8">Start adding items to your cart</p>
            <a href="{{ route('menu') }}" class="inline-block bg-orange-600 hover:bg-orange-700 text-white font-bold px-8 py-4 rounded-lg transition">
                Browse Menu
            </a>
        </div>
        @endif
    </div>
</div>
