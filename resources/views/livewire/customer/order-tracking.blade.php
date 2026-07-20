<?php

use App\Models\Order;
use function Livewire\Volt\{state, mount, layout};

layout('components.layouts.customer');

state(['order' => null]);

mount(function ($id) {
    $this->order = Order::with(['items.food', 'payment', 'table', 'customer'])
        ->where('customer_id', auth()->id())
        ->findOrFail($id);
});

$refreshOrder = function () {
    $this->order = Order::with(['items.food', 'payment', 'table', 'customer'])
        ->where('customer_id', auth()->id())
        ->findOrFail($this->order->id);
};

$getProgress = function () {
    return ['pending' => 20, 'preparing' => 40, 'ready' => 60, 'served' => 80, 'completed' => 100, 'cancelled' => 0][$this->order->status] ?? 0;
};

?>

<div class="space-y-6" wire:poll.5s="refreshOrder">
    <!-- Back -->
    <a href="{{ route('customer.orders') }}" class="inline-flex items-center text-gold-400 hover:text-gold-300 font-medium">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Orders
    </a>

    <!-- Order Header -->
    <div class="glass-card rounded-2xl border border-gold-500/20 p-8 hover-glow">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Order {{ $order->order_number }}</h1>
                <p class="text-gray-400">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
            </div>
            @php
                $statusColors = [
                    'pending'   => 'bg-yellow-400/20 text-yellow-400 border border-yellow-400/30',
                    'preparing' => 'bg-blue-400/20 text-blue-400 border border-blue-400/30',
                    'ready'     => 'bg-purple-400/20 text-purple-400 border border-purple-400/30',
                    'served'    => 'bg-green-400/20 text-green-400 border border-green-400/30',
                    'completed' => 'bg-green-400/20 text-green-400 border border-green-400/30',
                    'cancelled' => 'bg-red-400/20 text-red-400 border border-red-400/30',
                ];
            @endphp
            <span class="px-5 py-2 text-sm font-bold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-400/20 text-gray-400' }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
    </div>

    <!-- Progress Tracker -->
    @if($order->status !== 'cancelled')
    <div class="glass-card rounded-2xl border border-gold-500/20 p-8">
        <h2 class="text-xl font-bold text-white mb-8">Order Progress</h2>
        <div class="relative">
            <div class="absolute top-5 left-0 right-0 h-1.5 bg-white/10 rounded-full">
                <div class="h-full bg-gradient-to-r from-gold-500 to-gold-400 rounded-full transition-all duration-500" style="width: {{ $this->getProgress() }}%"></div>
            </div>
            @php
                $steps = [
                    'pending'   => 'Order Placed',
                    'preparing' => 'Preparing',
                    'ready'     => 'Ready',
                    'served'    => 'Served',
                    'completed' => 'Completed',
                ];
                $statusOrder = array_keys($steps);
                $currentIndex = array_search($order->status, $statusOrder);
            @endphp
            <div class="relative grid grid-cols-5 gap-4">
                @foreach($steps as $key => $label)
                @php $stepIndex = array_search($key, $statusOrder); $isActive = $stepIndex <= $currentIndex; @endphp
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-11 h-11 rounded-full mb-2 {{ $isActive ? 'bg-gradient-to-br from-gold-500 to-gold-600' : 'bg-white/10' }}">
                        <svg class="w-5 h-5 {{ $isActive ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-xs font-medium {{ $isActive ? 'text-white' : 'text-gray-500' }}">{{ $label }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="glass-card rounded-2xl border border-red-500/20 p-6">
        <div class="flex items-center space-x-3">
            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-lg font-bold text-white">Order Cancelled</h3>
                <p class="text-gray-400">This order has been cancelled</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Items -->
        <div class="lg:col-span-2">
            <div class="glass-card rounded-2xl border border-gold-500/20 p-6">
                <h2 class="text-xl font-bold text-white mb-6">Order Items</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center space-x-4 p-4 bg-white/5 rounded-xl">
                        @if($item->food->image)
                        <img src="{{ asset('storage/' . $item->food->image) }}" alt="{{ $item->food->name }}" class="w-20 h-20 rounded-xl object-cover">
                        @else
                        <div class="w-20 h-20 bg-white/5 rounded-xl"></div>
                        @endif
                        <div class="flex-1">
                            <h3 class="font-bold text-white">{{ $item->food->name }}</h3>
                            <p class="text-sm text-gray-400">{{ $item->food->category->name ?? '' }}</p>
                            <p class="text-sm text-gray-400">Qty: {{ $item->quantity }}</p>
                            @if($item->special_notes)
                            <p class="text-xs text-gold-400 italic mt-1">Note: {{ $item->special_notes }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-400">${{ number_format($item->price, 2) }} each</p>
                            <p class="text-lg font-bold text-white">${{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Summary + Details -->
        <div class="space-y-6">
            <div class="glass-card rounded-2xl border border-gold-500/20 p-6">
                <h2 class="text-xl font-bold text-white mb-6">Summary</h2>
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between text-gray-400">
                        <span>Subtotal</span>
                        <span class="font-semibold text-white">${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Tax</span>
                        <span class="font-semibold text-white">${{ number_format($order->tax, 2) }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between text-green-400">
                        <span>Discount</span>
                        <span class="font-semibold">-${{ number_format($order->discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="border-t border-white/10 pt-3">
                        <div class="flex justify-between text-xl font-bold">
                            <span class="text-white">Total</span>
                            <span class="text-gold-400">${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card rounded-2xl border border-gold-500/20 p-6">
                <h3 class="font-bold text-white mb-4">Order Details</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Order Type</span>
                        <span class="font-semibold text-white">{{ ucwords(str_replace('_', ' ', $order->order_type)) }}</span>
                    </div>
                    @if($order->table)
                    <div class="flex justify-between">
                        <span class="text-gray-400">Table</span>
                        <span class="font-semibold text-white">{{ $order->table->table_number }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-400">Payment Method</span>
                        <span class="font-semibold text-white">{{ ucfirst(str_replace('-', ' ', $order->payment?->payment_method ?? 'Pending')) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Payment Status</span>
                        <span class="font-semibold {{ $order->payment?->status === 'completed' ? 'text-green-400' : 'text-yellow-400' }}">
                            {{ ucfirst($order->payment?->status ?? 'Pending') }}
                        </span>
                    </div>
                    @if($order->notes)
                    <div class="pt-2 border-t border-white/10">
                        <p class="text-gray-400 mb-1">Special Instructions</p>
                        <p class="text-white">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
