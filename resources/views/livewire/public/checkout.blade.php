<?php

use App\Models\Cart;
use App\Models\Order;
use App\Models\RestaurantTable;
use function Livewire\Volt\{state, mount, rules, layout};

layout('components.layouts.public');
state(['cart' => null]);
state(['tables' => collect([])]);
state(['orderType' => 'dine_in']);
state(['tableId' => null]);
state(['notes' => '']);
state(['paymentMethod' => 'cash']);

mount(function () {
    $this->cart = Cart::where('user_id', auth()->id())
        ->where('status', 'active')
        ->with(['items.food'])
        ->first();
    
    if (!$this->cart || $this->cart->items->count() === 0) {
        return redirect()->route('cart.index');
    }
    
    $this->tables = RestaurantTable::where('status', 'available')->get();
});

rules([
    'orderType' => 'required|in:dine_in,takeout,delivery',
    'tableId' => 'required_if:orderType,dine_in|exists:restaurant_tables,id',
    'paymentMethod' => 'required|in:cash,card,mobile',
]);

$placeOrder = function () {
    $this->validate();
    
    $subtotal = $this->cart->calculateSubtotal();
    $tax = $subtotal * 0.1;
    $total = $subtotal + $tax;
    
    $order = Order::create([
        'order_number' => 'ORD-' . strtoupper(uniqid()),
        'customer_id' => auth()->id(),
        'table_id' => $this->orderType === 'dine_in' ? $this->tableId : null,
        'order_type' => $this->orderType,
        'status' => 'pending',
        'subtotal' => $subtotal,
        'tax' => $tax,
        'total_amount' => $total,
        'notes' => $this->notes,
    ]);
    
    foreach ($this->cart->items as $item) {
        $order->items()->create([
            'food_id' => $item->food_id,
            'quantity' => $item->quantity,
            'price' => $item->price,
            'subtotal' => $item->price * $item->quantity,
        ]);
    }
    
    \App\Models\Payment::create([
        'order_id' => $order->id,
        'payment_method' => $this->paymentMethod,
        'amount' => $total,
        'status' => 'pending',
    ]);
    
    if ($this->orderType === 'dine_in' && $this->tableId) {
        $table = RestaurantTable::find($this->tableId);
        $table->update(['status' => 'occupied']);
    }
    
    $this->cart->update(['status' => 'completed']);
    $this->cart->items()->delete();
    
    session()->flash('success', 'Order placed successfully!');
    return redirect()->route('customer.order.track', $order->id);
};

?>

<div class="min-h-screen bg-gray-50 py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold text-gray-900 mb-8">Checkout</h1>

            <form wire:submit="placeOrder">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Checkout Form -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Order Type -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Order Type</h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="relative">
                                    <input type="radio" wire:model.live="orderType" value="dine_in" class="peer sr-only">
                                    <div class="p-6 border-2 rounded-lg cursor-pointer transition peer-checked:border-orange-600 peer-checked:bg-orange-50">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 peer-checked:text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-center font-semibold">Dine In</p>
                                    </div>
                                </label>
                                
                                <label class="relative">
                                    <input type="radio" wire:model.live="orderType" value="takeout" class="peer sr-only">
                                    <div class="p-6 border-2 rounded-lg cursor-pointer transition peer-checked:border-orange-600 peer-checked:bg-orange-50">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 peer-checked:text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        <p class="text-center font-semibold">Takeaway</p>
                                    </div>
                                </label>
                                
                                <label class="relative">
                                    <input type="radio" wire:model.live="orderType" value="delivery" class="peer sr-only">
                                    <div class="p-6 border-2 rounded-lg cursor-pointer transition peer-checked:border-orange-600 peer-checked:bg-orange-50">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 peer-checked:text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                        </svg>
                                        <p class="text-center font-semibold">Delivery</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Table Selection (for dine-in) -->
                        @if($orderType === 'dine_in')
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Select Table</h2>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($tables as $table)
                                <label class="relative">
                                    <input type="radio" wire:model="tableId" value="{{ $table->id }}" class="peer sr-only">
                                    <div class="p-4 border-2 rounded-lg cursor-pointer text-center transition peer-checked:border-orange-600 peer-checked:bg-orange-50">
                                        <p class="font-bold text-lg">Table {{ $table->table_number }}</p>
                                        <p class="text-sm text-gray-600">{{ $table->capacity }} seats</p>
                                        <p class="text-xs text-gray-500">{{ $table->location }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('tableId') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        @endif

                        <!-- Special Instructions -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Special Instructions</h2>
                            <textarea wire:model="notes" rows="4" placeholder="Any special requests? (e.g., allergies, preferences)" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"></textarea>
                        </div>

                        <!-- Payment Method -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Method</h2>
                            <div class="space-y-3">
                                <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50">
                                    <input type="radio" wire:model="paymentMethod" value="cash" class="w-5 h-5 text-orange-600">
                                    <div class="ml-4">
                                        <p class="font-semibold">Cash</p>
                                        <p class="text-sm text-gray-600">Pay with cash at the restaurant</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50">
                                    <input type="radio" wire:model="paymentMethod" value="card" class="w-5 h-5 text-orange-600">
                                    <div class="ml-4">
                                        <p class="font-semibold">Card</p>
                                        <p class="text-sm text-gray-600">Credit or debit card</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50">
                                    <input type="radio" wire:model="paymentMethod" value="mobile" class="w-5 h-5 text-orange-600">
                                    <div class="ml-4">
                                        <p class="font-semibold">Mobile Payment</p>
                                        <p class="text-sm text-gray-600">Pay via mobile app</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Order Summary</h2>

                            <div class="space-y-3 mb-6">
                                @if($cart)
                                    @foreach($cart->items as $item)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">{{ $item->quantity }}x {{ $item->food->name }}</span>
                                        <span class="font-semibold">${{ number_format($item->price * $item->quantity, 2) }}</span>
                                    </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="border-t pt-4 space-y-3 mb-6">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal</span>
                                    <span class="font-semibold">${{ number_format($cart->calculateSubtotal(), 2) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Tax (10%)</span>
                                    <span class="font-semibold">${{ number_format($cart->calculateSubtotal() * 0.1, 2) }}</span>
                                </div>
                                <div class="border-t pt-3">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Total</span>
                                        <span class="text-orange-600">${{ number_format($cart->calculateTotal(), 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-lg transition">
                                Place Order
                            </button>

                            <a href="{{ route('cart.index') }}" class="block w-full text-center text-orange-600 hover:text-orange-700 font-medium mt-4">
                                Back to Cart
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
