<?php

use App\Models\Order;
use function Livewire\Volt\{state, computed, layout};

layout('components.layouts.customer');

state(['search' => '']);
state(['status' => 'all']);

$orders = computed(function () {
    $query = Order::where('customer_id', auth()->id())
        ->with(['items.food', 'payment', 'table'])
        ->orderBy('created_at', 'desc');

    if ($this->search) {
        $query->where('order_number', 'like', '%' . $this->search . '%');
    }

    if ($this->status !== 'all') {
        $query->where('status', $this->status);
    }

    return $query->paginate(10);
});

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-white">My Orders</h1>
        <a href="{{ route('menu') }}" class="bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-bold px-6 py-3 rounded-xl transition">
            New Order
        </a>
    </div>

    <!-- Filters -->
    <div class="glass-card rounded-2xl p-6 border border-gold-500/20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by order number..."
                       class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-gold-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Status</label>
                <select wire:model.live="status" class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    <option value="all" class="bg-slate-800">All Orders</option>
                    <option value="pending" class="bg-slate-800">Pending</option>
                    <option value="preparing" class="bg-slate-800">Preparing</option>
                    <option value="ready" class="bg-slate-800">Ready</option>
                    <option value="served" class="bg-slate-800">Served</option>
                    <option value="completed" class="bg-slate-800">Completed</option>
                    <option value="cancelled" class="bg-slate-800">Cancelled</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    @if($this->orders->count() > 0)
    <div class="space-y-4">
        @foreach($this->orders as $order)
        <div class="glass-card rounded-2xl border border-gold-500/20 overflow-hidden hover-glow transition">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-1">{{ $order->order_number }}</h3>
                        <p class="text-sm text-gray-400">{{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
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
                    <span class="px-4 py-1.5 text-sm font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-400/20 text-gray-400' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div class="border-t border-white/5 py-4 my-2 space-y-3">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            @if($item->food->image)
                            <img src="{{ asset('storage/' . $item->food->image) }}" alt="{{ $item->food->name }}" class="w-12 h-12 rounded-lg object-cover">
                            @else
                            <div class="w-12 h-12 bg-white/5 rounded-lg"></div>
                            @endif
                            <div>
                                <p class="font-medium text-white">{{ $item->food->name }}</p>
                                <p class="text-sm text-gray-400">Qty: {{ $item->quantity }}</p>
                            </div>
                        </div>
                        <span class="font-semibold text-white">${{ number_format($item->subtotal, 2) }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between pt-2">
                    <div class="space-y-1">
                        <p class="text-sm text-gray-400">
                            <span class="font-semibold text-gray-300">Type:</span> {{ ucwords(str_replace('_', ' ', $order->order_type)) }}
                            @if($order->table)
                            · <span class="font-semibold text-gray-300">Table:</span> {{ $order->table->table_number }}
                            @endif
                        </p>
                        <p class="text-sm text-gray-400">
                            <span class="font-semibold text-gray-300">Payment:</span> {{ ucfirst(str_replace('-', ' ', $order->payment?->payment_method ?? 'Pending')) }}
                        </p>
                        <p class="text-lg font-bold text-white">
                            Total: ${{ number_format($order->total_amount, 2) }}
                        </p>
                    </div>
                    <a href="{{ route('customer.order.track', $order->id) }}" class="bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-bold px-6 py-3 rounded-xl transition">
                        Track Order
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $this->orders->links() }}</div>

    @else
    <div class="glass-card rounded-2xl border border-gold-500/20 p-12 text-center">
        <svg class="w-20 h-20 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <h3 class="text-lg font-semibold text-white mb-2">No orders found</h3>
        <p class="text-gray-400 mb-4">{{ $search || $status !== 'all' ? 'Try adjusting your filters' : 'Start ordering from our delicious menu' }}</p>
        @if($search || $status !== 'all')
        <button wire:click="$set('search', ''); $set('status', 'all');" class="text-gold-400 hover:text-gold-300 font-medium">
            Clear Filters
        </button>
        @else
        <a href="{{ route('menu') }}" class="inline-block bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-bold px-6 py-3 rounded-xl transition">
            Browse Menu
        </a>
        @endif
    </div>
    @endif
</div>
