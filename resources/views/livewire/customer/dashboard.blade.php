<?php

use App\Models\Order;
use function Livewire\Volt\{state, mount, layout};

layout('components.layouts.customer');

state(['recentOrders' => collect([])]);
state(['stats' => []]);

mount(function () {
    $this->loadData();
});

$loadData = function () {
    $userId = auth()->id();

    $this->recentOrders = Order::where('customer_id', $userId)
        ->with(['items.food', 'payment'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    $this->stats = [
        'total_orders'     => Order::where('customer_id', $userId)->count(),
        'pending_orders'   => Order::where('customer_id', $userId)->where('status', 'pending')->count(),
        'completed_orders' => Order::where('customer_id', $userId)->where('status', 'completed')->count(),
        'total_spent'      => Order::where('customer_id', $userId)->sum('total_amount'),
    ];
};

?>

<div class="space-y-6" wire:poll.10s="loadData">
        <!-- Welcome Section -->
        <div class="glass-card rounded-2xl p-8 mb-2 border border-gold-500/20 hover-glow">
            <h1 class="text-3xl font-bold text-white mb-1">Welcome back, <span class="bg-gradient-to-r from-gold-400 to-gold-600 bg-clip-text text-transparent">{{ auth()->user()->name }}</span>! 👋</h1>
            <p class="text-gray-300 opacity-90">Here's what's happening with your account</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="glass-card rounded-2xl p-6 border border-gold-500/20">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-300 text-sm font-semibold">Total Orders</h3>
                    <div class="w-12 h-12 bg-blue-500/20 border border-blue-500/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ $stats['total_orders'] }}</p>
            </div>

            <div class="glass-card rounded-2xl p-6 border border-gold-500/20">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-300 text-sm font-semibold">Pending</h3>
                    <div class="w-12 h-12 bg-yellow-500/20 border border-yellow-500/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ $stats['pending_orders'] }}</p>
            </div>

            <div class="glass-card rounded-2xl p-6 border border-gold-500/20">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-300 text-sm font-semibold">Completed</h3>
                    <div class="w-12 h-12 bg-green-500/20 border border-green-500/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ $stats['completed_orders'] }}</p>
            </div>

            <div class="glass-card rounded-2xl p-6 border border-gold-500/20">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-300 text-sm font-semibold">Total Spent</h3>
                    <div class="w-12 h-12 bg-purple-500/20 border border-purple-500/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">${{ number_format($stats['total_spent'], 2) }}</p>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="glass-card rounded-2xl border border-gold-500/20 overflow-hidden">
            <div class="p-6 border-b border-gold-500/10">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-white">Recent Orders</h2>
                    <a href="{{ route('customer.orders') }}" class="text-gold-400 hover:text-gold-300 font-medium">View All</a>
                </div>
            </div>

            @if($recentOrders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($recentOrders as $order)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-white">{{ $order->order_number }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                {{ $order->items->count() }} item(s)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-white">${{ number_format($order->total_amount, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending'   => 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30',
                                        'preparing' => 'bg-blue-500/20 text-blue-300 border border-blue-500/30',
                                        'ready'     => 'bg-purple-500/20 text-purple-300 border border-purple-500/30',
                                        'served'    => 'bg-teal-500/20 text-teal-300 border border-teal-500/30',
                                        'completed' => 'bg-green-500/20 text-green-300 border border-green-500/30',
                                        'cancelled' => 'bg-red-500/20 text-red-300 border border-red-500/30',
                                    ];
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-500/20 text-gray-300 border border-gray-500/30' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('customer.order.track', $order->id) }}" class="text-gold-400 hover:text-gold-300 font-medium">
                                    Track
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-12 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="text-lg font-semibold text-white mb-2">No orders yet</h3>
                <p class="text-gray-400 mb-4">Start ordering from our delicious menu</p>
                <a href="{{ route('menu') }}" class="inline-block bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-bold px-6 py-3 rounded-xl transition">
                    Browse Menu
                </a>
            </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('menu') }}" class="glass-card rounded-2xl p-6 border border-gold-500/20 hover:border-gold-500/40 hover-glow transition group">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gold-500/20 border border-gold-500/30 rounded-xl flex items-center justify-center group-hover:bg-gold-500/30 transition">
                        <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white">Browse Menu</h3>
                        <p class="text-sm text-gray-400">View our dishes</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('customer.orders') }}" class="glass-card rounded-2xl p-6 border border-gold-500/20 hover:border-gold-500/40 hover-glow transition group">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-500/20 border border-blue-500/30 rounded-xl flex items-center justify-center group-hover:bg-blue-500/30 transition">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white">Order History</h3>
                        <p class="text-sm text-gray-400">View all orders</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('settings.profile') }}" class="glass-card rounded-2xl p-6 border border-gold-500/20 hover:border-gold-500/40 hover-glow transition group">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-purple-500/20 border border-purple-500/30 rounded-xl flex items-center justify-center group-hover:bg-purple-500/30 transition">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white">My Profile</h3>
                        <p class="text-sm text-gray-400">Update info</p>
                    </div>
                </div>
            </a>
        </div>
</div>
