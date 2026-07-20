<?php

use App\Models\Order;
use App\Models\RestaurantTable;
use function Livewire\Volt\{state, mount, layout};

layout('components.layouts.employee');

state(['readyOrders' => [], 'tables' => []]);

mount(function () { $this->loadData(); });

$loadData = function () {
    $this->readyOrders = Order::with(['customer', 'items.food', 'restaurantTable'])
        ->where('status', 'ready')->latest()->get();

    $this->tables = RestaurantTable::with(['currentOrder' => fn($q) => $q->with(['customer', 'items'])])
        ->orderBy('table_number')->get();
};

$markServed = function (int $orderId) {
    $order = Order::find($orderId);
    if ($order && $order->status === 'ready') {
        $order->update(['status' => 'served']);
        $this->loadData();
    }
};

?>

<div class="space-y-6" wire:poll.8s="loadData">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white">Waiter Dashboard</h1>
            <p class="text-sm text-gray-400 mt-0.5">Serve orders and manage table status</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-green-400 bg-green-400/10 border border-green-400/20 rounded-full px-4 py-2 w-fit">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            Live · refreshes every 8s
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 bg-purple-500/10 border border-purple-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider">Ready to Serve</p>
                <div class="h-8 w-8 rounded-lg bg-purple-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ count($readyOrders) }}</p>
            <p class="text-xs text-purple-400/70 mt-1">Awaiting delivery</p>
        </div>

        <div class="rounded-2xl p-5 bg-orange-500/10 border border-orange-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-orange-400 uppercase tracking-wider">Occupied</p>
                <div class="h-8 w-8 rounded-lg bg-orange-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ collect($tables)->where('status', 'occupied')->count() }}</p>
            <p class="text-xs text-orange-400/70 mt-1">Tables occupied</p>
        </div>

        <div class="rounded-2xl p-5 bg-green-500/10 border border-green-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-green-400 uppercase tracking-wider">Available</p>
                <div class="h-8 w-8 rounded-lg bg-green-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ collect($tables)->where('status', 'available')->count() }}</p>
            <p class="text-xs text-green-400/70 mt-1">Tables free</p>
        </div>

        <div class="rounded-2xl p-5 bg-blue-500/10 border border-blue-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Reserved</p>
                <div class="h-8 w-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ collect($tables)->where('status', 'reserved')->count() }}</p>
            <p class="text-xs text-blue-400/70 mt-1">Tables reserved</p>
        </div>
    </div>

    {{-- Ready Orders --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="w-3 h-3 rounded-full bg-purple-400 animate-pulse flex-shrink-0"></span>
            <h2 class="text-base font-bold text-white">Orders Ready to Serve</h2>
            @if(count($readyOrders) > 0)
                <span class="bg-purple-400/20 text-purple-400 text-xs font-bold px-2.5 py-0.5 rounded-full border border-purple-400/30">{{ count($readyOrders) }}</span>
            @endif
        </div>

        @if(count($readyOrders) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($readyOrders as $order)
                    <div class="rounded-2xl p-5 bg-white/[0.04] border border-white/[0.07] border-l-4 border-l-purple-500" wire:key="ready-{{ $order->id }}">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-bold text-white text-sm">#{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Ready {{ $order->updated_at->diffForHumans() }}</p>
                            </div>
                            <span class="bg-purple-400/20 text-purple-400 text-xs font-semibold px-2.5 py-1 rounded-full border border-purple-400/30">READY</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="bg-white/5 text-gray-300 text-xs px-2 py-1 rounded-lg">{{ $order->customer->name ?? 'Guest' }}</span>
                            <span class="bg-white/5 text-gray-300 text-xs px-2 py-1 rounded-lg capitalize">{{ str_replace('_', ' ', $order->order_type) }}</span>
                            @if($order->restaurantTable)
                                <span class="bg-amber-500/20 text-amber-400 text-xs px-2 py-1 rounded-lg font-semibold">Table {{ $order->restaurantTable->table_number }}</span>
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
                        <button wire:click="markServed({{ $order->id }})" wire:loading.attr="disabled"
                            class="w-full bg-green-600 hover:bg-green-500 text-white text-sm font-semibold py-2.5 rounded-xl transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="markServed({{ $order->id }})">Mark as Served</span>
                            <span wire:loading wire:target="markServed({{ $order->id }})">Updating…</span>
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl p-10 text-center bg-white/[0.03] border border-white/[0.07]">
                <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-gray-500 text-sm">No orders ready for delivery</p>
            </div>
        @endif
    </div>

    {{-- Tables Overview --}}
    <div>
        <h2 class="text-base font-bold text-white mb-4">Tables Overview</h2>
        @if(count($tables) > 0)
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                @foreach($tables as $table)
                    @php
                        [$bg, $border, $dot, $label] = match($table->status) {
                            'occupied' => ['bg-orange-500/10', 'border-orange-500/30', 'bg-orange-400', 'text-orange-400'],
                            'reserved' => ['bg-blue-500/10',   'border-blue-500/30',   'bg-blue-400',   'text-blue-400'],
                            default    => ['bg-green-500/5',   'border-green-500/20',  'bg-green-400',  'text-green-400'],
                        };
                    @endphp
                    <div class="rounded-xl p-3 text-center {{ $bg }} border {{ $border }}">
                        <p class="text-xl font-bold text-white">{{ $table->table_number }}</p>
                        <div class="flex items-center justify-center gap-1 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                            <p class="text-xs font-semibold {{ $label }} capitalize">{{ $table->status }}</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $table->capacity }}p</p>
                        @if($table->currentOrder)
                            <p class="text-xs bg-amber-500/20 text-amber-400 rounded-lg mt-1.5 px-1 py-0.5 truncate">#{{ $table->currentOrder->order_number }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl p-8 text-center bg-white/[0.03] border border-white/[0.07]">
                <p class="text-gray-500 text-sm">No tables configured</p>
            </div>
        @endif
    </div>

</div>
