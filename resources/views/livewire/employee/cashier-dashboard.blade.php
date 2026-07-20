<?php

use App\Models\Order;
use App\Models\Payment;
use function Livewire\Volt\{state, mount, layout};

layout('components.layouts.employee');

state([
    'servedOrders'   => [],
    'selectedOrder'  => null,
    'paymentMethod'  => 'cash',
    'todayRevenue'   => 0,
    'completedToday' => 0,
]);

mount(function () { $this->loadData(); });

$loadData = function () {
    $this->servedOrders = Order::with(['customer', 'items.food', 'payment', 'restaurantTable'])
        ->where('status', 'served')->latest()->get();
    $this->todayRevenue   = Payment::completed()->whereDate('paid_at', today())->sum('amount');
    $this->completedToday = Order::where('status', 'completed')->whereDate('updated_at', today())->count();
};

$selectOrder = function (int $orderId) {
    $this->selectedOrder = Order::with(['customer', 'items.food', 'payment', 'restaurantTable'])->find($orderId);
    $this->paymentMethod = $this->selectedOrder?->payment?->payment_method ?? 'cash';
};

$processPayment = function () {
    if (! $this->selectedOrder) return;
    $payment = $this->selectedOrder->payment;
    if (! $payment) {
        session()->flash('error', 'No payment record found for this order. Please contact an administrator.');
        return;
    }
    if ($payment->status === 'completed') {
        session()->flash('error', 'This order has already been paid.');
        return;
    }
    $payment->update([
        'status'         => 'completed',
        'payment_method' => $this->paymentMethod,
        'paid_at'        => now(),
        'paid_by'        => auth()->id(),
    ]);
    $this->selectedOrder->update(['status' => 'completed']);
    $this->selectedOrder = null;
    $this->loadData();
    session()->flash('success', 'Payment processed successfully.');
};

$clearSelection = function () { $this->selectedOrder = null; };

?>

<div class="space-y-6" wire:poll.10s="loadData">

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/20 text-green-400 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white">Cashier Dashboard</h1>
            <p class="text-sm text-gray-400 mt-0.5">Process payments and manage transactions</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-green-400 bg-green-400/10 border border-green-400/20 rounded-full px-4 py-2 w-fit">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            Live · refreshes every 10s
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl p-5 bg-yellow-500/10 border border-yellow-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-yellow-400 uppercase tracking-wider">Awaiting Payment</p>
                <div class="h-8 w-8 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ count($servedOrders) }}</p>
            <p class="text-xs text-yellow-400/70 mt-1">Orders pending</p>
        </div>

        <div class="rounded-2xl p-5 bg-green-500/10 border border-green-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-green-400 uppercase tracking-wider">Today's Revenue</p>
                <div class="h-8 w-8 rounded-lg bg-green-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">${{ number_format($todayRevenue, 2) }}</p>
            <p class="text-xs text-green-400/70 mt-1">Collected today</p>
        </div>

        <div class="rounded-2xl p-5 bg-blue-500/10 border border-blue-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Completed Today</p>
                <div class="h-8 w-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $completedToday }}</p>
            <p class="text-xs text-blue-400/70 mt-1">Transactions done</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Orders List --}}
        <div class="lg:col-span-2 space-y-3">
            <h2 class="text-base font-bold text-white">Orders Awaiting Payment</h2>
            @if(count($servedOrders) > 0)
                @foreach($servedOrders as $order)
                    <div wire:click="selectOrder({{ $order->id }})" wire:key="served-{{ $order->id }}"
                        class="rounded-2xl p-4 cursor-pointer transition-all hover:scale-[1.01]
                            {{ $selectedOrder && $selectedOrder->id === $order->id
                                ? 'border-2 border-amber-500 bg-amber-500/10'
                                : 'border border-white/[0.07] bg-white/[0.04] hover:border-amber-500/40' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-bold text-white text-sm">#{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-400 mt-0.5">{{ $order->customer->name ?? 'Guest' }}</p>
                                <div class="flex flex-wrap gap-2 mt-1.5 text-xs text-gray-500">
                                    <span class="capitalize">{{ str_replace('_', ' ', $order->order_type) }}</span>
                                    @if($order->restaurantTable)<span>· Table {{ $order->restaurantTable->table_number }}</span>@endif
                                    <span>· {{ $order->items->count() }} item(s)</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-amber-400">${{ number_format($order->total_amount, 2) }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $order->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="rounded-2xl p-10 text-center bg-white/[0.03] border border-white/[0.07]">
                    <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-gray-500 text-sm">No orders awaiting payment</p>
                </div>
            @endif
        </div>

        {{-- Payment Panel --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl sticky top-6 overflow-hidden bg-white/[0.04] border border-white/[0.08]">
                @if($selectedOrder)
                    <div class="p-5 border-b border-white/5">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-white">Order #{{ $selectedOrder->order_number }}</h3>
                            <button wire:click="clearSelection" class="text-gray-500 hover:text-gray-300 text-xs transition">✕ Clear</button>
                        </div>
                        <div class="space-y-2 mb-4">
                            @foreach($selectedOrder->items as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-300">{{ $item->quantity }}× {{ $item->food->name ?? 'Item' }}</span>
                                    <span class="font-medium text-white">${{ number_format($item->subtotal, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="border-t border-white/5 pt-3 space-y-1.5">
                            <div class="flex justify-between text-sm text-gray-400">
                                <span>Subtotal</span>
                                <span>${{ number_format($selectedOrder->subtotal ?? $selectedOrder->total_amount, 2) }}</span>
                            </div>
                            @if(($selectedOrder->tax ?? 0) > 0)
                                <div class="flex justify-between text-sm text-gray-400">
                                    <span>Tax</span>
                                    <span>${{ number_format($selectedOrder->tax, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-base font-bold border-t border-white/5 pt-2 mt-1">
                                <span class="text-white">Total</span>
                                <span class="text-amber-400">${{ number_format($selectedOrder->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <p class="text-sm font-semibold text-gray-300 mb-3">Payment Method</p>
                        <div class="space-y-2 mb-5">
                            @foreach(['cash' => '💵 Cash', 'card' => '💳 Card / POS', 'mobile' => '📱 Mobile Payment'] as $val => $label)
                                <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition
                                    {{ $paymentMethod === $val ? 'border-2 border-amber-500 bg-amber-500/10' : 'border border-white/[0.07] hover:border-white/[0.15] bg-white/[0.03]' }}">
                                    <input type="radio" wire:model.live="paymentMethod" value="{{ $val }}" class="text-amber-500">
                                    <span class="text-sm font-medium text-gray-200">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <button wire:click="processPayment" wire:loading.attr="disabled"
                            class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-xl transition disabled:opacity-50">
                            <span wire:loading.remove>Complete Payment</span>
                            <span wire:loading>Processing…</span>
                        </button>
                    </div>
                @else
                    <div class="p-10 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <p class="text-gray-500 text-sm">Select an order to process payment</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
