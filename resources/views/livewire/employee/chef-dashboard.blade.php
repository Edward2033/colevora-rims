<?php

use App\Models\Order;
use function Livewire\Volt\{state, mount, layout};

layout('components.layouts.employee');

state([
    'pendingOrders'   => [],
    'preparingOrders' => [],
    'readyOrders'     => [],
    'completedToday'  => 0,
]);

mount(function () { $this->loadOrders(); });

$loadOrders = function () {
    $with = ['customer', 'items.food', 'restaurantTable'];
    $this->pendingOrders   = Order::with($with)->where('status', 'pending')->latest()->get();
    $this->preparingOrders = Order::with($with)->where('status', 'preparing')->latest()->get();
    $this->readyOrders     = Order::with($with)->where('status', 'ready')->latest()->get();
    $this->completedToday  = Order::where('status', 'completed')->whereDate('updated_at', today())->count();
};

$acceptOrder = function (int $orderId) {
    $order = Order::find($orderId);
    if ($order && $order->status === 'pending') {
        $order->update(['status' => 'preparing']);
        $this->loadOrders();
    }
};

$markReady = function (int $orderId) {
    $order = Order::find($orderId);
    if ($order && $order->status === 'preparing') {
        $order->update(['status' => 'ready']);
        $this->loadOrders();
    }
};

?>

<div class="space-y-6" wire:poll.8s="loadOrders">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white">Kitchen Dashboard</h1>
            <p class="text-sm text-gray-400 mt-0.5">Manage incoming orders and kitchen workflow</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-green-400 bg-green-400/10 border border-green-400/20 rounded-full px-4 py-2 w-fit">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            Live · refreshes every 8s
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 bg-yellow-500/10 border border-yellow-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-yellow-400 uppercase tracking-wider">Pending</p>
                <div class="h-8 w-8 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ count($pendingOrders) }}</p>
            <p class="text-xs text-yellow-400/70 mt-1">Awaiting kitchen</p>
        </div>

        <div class="rounded-2xl p-5 bg-blue-500/10 border border-blue-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Preparing</p>
                <div class="h-8 w-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ count($preparingOrders) }}</p>
            <p class="text-xs text-blue-400/70 mt-1">In progress</p>
        </div>

        <div class="rounded-2xl p-5 bg-purple-500/10 border border-purple-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider">Ready</p>
                <div class="h-8 w-8 rounded-lg bg-purple-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ count($readyOrders) }}</p>
            <p class="text-xs text-purple-400/70 mt-1">Waiting waiter</p>
        </div>

        <div class="rounded-2xl p-5 bg-green-500/10 border border-green-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-green-400 uppercase tracking-wider">Done Today</p>
                <div class="h-8 w-8 rounded-lg bg-green-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $completedToday }}</p>
            <p class="text-xs text-green-400/70 mt-1">Completed orders</p>
        </div>
    </div>

    {{-- Pending Orders --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="w-3 h-3 rounded-full bg-yellow-400 flex-shrink-0"></span>
            <h2 class="text-base font-bold text-white">Pending Orders</h2>
            @if(count($pendingOrders) > 0)
                <span class="bg-yellow-400/20 text-yellow-400 text-xs font-bold px-2.5 py-0.5 rounded-full border border-yellow-400/30">{{ count($pendingOrders) }}</span>
            @endif
        </div>

        @if(count($pendingOrders) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($pendingOrders as $order)
                    <div class="rounded-2xl p-5 bg-white/[0.04] border border-white/[0.07] border-l-4 border-l-yellow-400" wire:key="p-{{ $order->id }}">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-bold text-white text-sm">#{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="bg-yellow-400/20 text-yellow-400 text-xs font-semibold px-2.5 py-1 rounded-full border border-yellow-400/30">PENDING</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="bg-white/5 text-gray-300 text-xs px-2 py-1 rounded-lg">{{ $order->customer->name ?? 'Guest' }}</span>
                            <span class="bg-white/5 text-gray-300 text-xs px-2 py-1 rounded-lg capitalize">{{ str_replace('_', ' ', $order->order_type) }}</span>
                            @if($order->restaurantTable)
                                <span class="bg-amber-500/20 text-amber-400 text-xs px-2 py-1 rounded-lg">Table {{ $order->restaurantTable->table_number }}</span>
                            @endif
                        </div>
                        <ul class="space-y-1.5 mb-4">
                            @foreach($order->items as $item)
                                <li class="flex justify-between items-center text-sm bg-white/5 rounded-lg px-3 py-1.5">
                                    <span class="text-gray-200">{{ $item->food->name ?? 'Unknown' }}</span>
                                    <span class="font-bold text-amber-400">×{{ $item->quantity }}</span>
                                </li>
                            @endforeach
                        </ul>
                        @if($order->notes)
                            <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-2.5 mb-3 text-xs text-amber-300">
                                <strong>Note:</strong> {{ $order->notes }}
                            </div>
                        @endif
                        <button wire:click="acceptOrder({{ $order->id }})" wire:loading.attr="disabled"
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold py-2.5 rounded-xl transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="acceptOrder({{ $order->id }})">Accept & Start Preparing</span>
                            <span wire:loading wire:target="acceptOrder({{ $order->id }})">Processing…</span>
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl p-10 text-center bg-white/[0.03] border border-white/[0.07]">
                <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-gray-500 text-sm">No pending orders — kitchen is clear</p>
            </div>
        @endif
    </div>

    {{-- Preparing Orders --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="w-3 h-3 rounded-full bg-blue-400 animate-pulse flex-shrink-0"></span>
            <h2 class="text-base font-bold text-white">Currently Preparing</h2>
            @if(count($preparingOrders) > 0)
                <span class="bg-blue-400/20 text-blue-400 text-xs font-bold px-2.5 py-0.5 rounded-full border border-blue-400/30">{{ count($preparingOrders) }}</span>
            @endif
        </div>

        @if(count($preparingOrders) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($preparingOrders as $order)
                    <div class="rounded-2xl p-5 bg-white/[0.04] border border-white/[0.07] border-l-4 border-l-blue-500" wire:key="pr-{{ $order->id }}">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-bold text-white text-sm">#{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Started {{ $order->updated_at->diffForHumans() }}</p>
                            </div>
                            <span class="bg-blue-400/20 text-blue-400 text-xs font-semibold px-2.5 py-1 rounded-full border border-blue-400/30">PREPARING</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="bg-white/5 text-gray-300 text-xs px-2 py-1 rounded-lg">{{ $order->customer->name ?? 'Guest' }}</span>
                            @if($order->restaurantTable)
                                <span class="bg-amber-500/20 text-amber-400 text-xs px-2 py-1 rounded-lg">Table {{ $order->restaurantTable->table_number }}</span>
                            @endif
                        </div>
                        <ul class="space-y-1.5 mb-4">
                            @foreach($order->items as $item)
                                <li class="flex justify-between items-center text-sm bg-white/5 rounded-lg px-3 py-1.5">
                                    <span class="text-gray-200">{{ $item->food->name ?? 'Unknown' }}</span>
                                    <span class="font-bold text-amber-400">×{{ $item->quantity }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <button wire:click="markReady({{ $order->id }})" wire:loading.attr="disabled"
                            class="w-full bg-green-600 hover:bg-green-500 text-white text-sm font-semibold py-2.5 rounded-xl transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="markReady({{ $order->id }})">Mark as Ready</span>
                            <span wire:loading wire:target="markReady({{ $order->id }})">Updating…</span>
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl p-8 text-center bg-white/[0.03] border border-white/[0.07]">
                <p class="text-gray-500 text-sm">No orders currently being prepared</p>
            </div>
        @endif
    </div>

    {{-- Ready Orders --}}
    @if(count($readyOrders) > 0)
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="w-3 h-3 rounded-full bg-purple-400 flex-shrink-0"></span>
            <h2 class="text-base font-bold text-white">Ready for Pickup</h2>
            <span class="bg-purple-400/20 text-purple-400 text-xs font-bold px-2.5 py-0.5 rounded-full border border-purple-400/30">{{ count($readyOrders) }}</span>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($readyOrders as $order)
                <div class="rounded-2xl p-5 bg-white/[0.03] border border-white/[0.06] border-l-4 border-l-purple-500 opacity-80" wire:key="r-{{ $order->id }}">
                    <div class="flex justify-between items-start mb-2">
                        <p class="font-bold text-white text-sm">#{{ $order->order_number }}</p>
                        <span class="bg-purple-400/20 text-purple-400 text-xs font-semibold px-2.5 py-1 rounded-full border border-purple-400/30">READY</span>
                    </div>
                    <p class="text-xs text-gray-400 mb-1">{{ $order->customer->name ?? 'Guest' }}@if($order->restaurantTable) · Table {{ $order->restaurantTable->table_number }}@endif</p>
                    <p class="text-xs text-gray-500">Waiting for waiter · {{ $order->updated_at->diffForHumans() }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
