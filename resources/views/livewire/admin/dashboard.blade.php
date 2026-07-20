<?php

use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Services\DashboardService;
use App\Services\OrderAnalyticsService;
use App\Services\SalesAnalyticsService;

use function Livewire\Volt\layout;
use function Livewire\Volt\mount;
use function Livewire\Volt\state;

layout('components.layouts.admin');

state([
    'statistics' => [],
    'dailySalesData' => [],
    'monthlyRevenueData' => [],
    'orderStatusData' => [],
    'recentOrders' => [],
    'lowStockItems' => [],
    'upcomingReservations' => [],
    'availableTables' => 0,
]);

mount(function () {
    $dashboardService = app(DashboardService::class);
    $salesService = app(SalesAnalyticsService::class);
    $orderService = app(OrderAnalyticsService::class);

    $this->statistics = $dashboardService->getStatistics();
    $this->dailySalesData = $salesService->getDailySales();
    $this->monthlyRevenueData = $salesService->getMonthlyRevenue();
    $this->orderStatusData = $orderService->getOrderStatusDistribution();
    $this->recentOrders = Order::with(['customer', 'payment'])->latest()->limit(5)->get();
    $this->lowStockItems = InventoryItem::lowStock()->limit(5)->get();
    $this->upcomingReservations = Reservation::where('status', 'confirmed')
        ->where('date', '>=', today())
        ->orderBy('date')
        ->orderBy('time')
        ->limit(5)
        ->get();
    $this->availableTables = RestaurantTable::where('status', 'available')->count();
});

?>

<div>
    <!-- Welcome Header -->
    <div class="glass-card rounded-2xl p-8 mb-8 border border-gold-500/20 hover-glow">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-white mb-2">
                    Welcome back, <span class="bg-gradient-to-r from-gold-400 to-gold-600 bg-clip-text text-transparent">{{ auth()->user()->name }}</span>! 👋
                </h2>
                <p class="text-gray-300">Here's what's happening with your restaurant today.</p>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('admin.orders.create') }}" class="px-6 py-3 rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-semibold shadow-lg hover:shadow-gold-500/50 transition-all transform hover:scale-105">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Order
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Sales Card -->
        <div class="glass-card rounded-2xl p-6 border border-gold-500/20 hover-glow transition-all transform hover:-translate-y-1 stat-card">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 border border-emerald-500/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-emerald-400 text-xs font-medium">+12.5%</p>
                    <span class="inline-flex items-center text-xs text-emerald-400">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div>
                <p class="text-sm text-gray-300 mb-1">Total Sales</p>
                <p class="text-3xl font-bold text-white mb-2">
                    ${{ number_format($statistics['sales']['total_sales'] ?? 0, 2) }}
                </p>
                <p class="text-xs text-gray-400">
                    Today: <span class="text-gray-200 font-medium">${{ number_format($statistics['sales']['today_sales'] ?? 0, 2) }}</span>
                </p>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="glass-card rounded-2xl p-6 border border-gold-500/20 hover-glow transition-all transform hover:-translate-y-1 stat-card" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500/20 to-blue-600/20 border border-blue-500/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-blue-400 text-xs font-medium">+8.2%</p>
                    <span class="inline-flex items-center text-xs text-blue-400">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div>
                <p class="text-sm text-gray-300 mb-1">Total Orders</p>
                <p class="text-3xl font-bold text-white mb-2">
                    {{ number_format($statistics['orders']['total_orders'] ?? 0) }}
                </p>
                <p class="text-xs text-gray-400">
                    Pending: <span class="text-yellow-300 font-medium">{{ $statistics['orders']['pending_orders'] ?? 0 }}</span>
                </p>
            </div>
        </div>

        <!-- Customers Card -->
        <div class="glass-card rounded-2xl p-6 border border-gold-500/20 hover-glow transition-all transform hover:-translate-y-1 stat-card" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-500/20 to-purple-600/20 border border-purple-500/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-purple-400 text-xs font-medium">+15.3%</p>
                    <span class="inline-flex items-center text-xs text-purple-400">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div>
                <p class="text-sm text-gray-300 mb-1">Total Customers</p>
                <p class="text-3xl font-bold text-white mb-2">
                    {{ number_format($statistics['customers'] ?? 0) }}
                </p>
                <p class="text-xs text-gray-400">
                    Active: <span class="text-gray-200 font-medium">{{ $statistics['customers'] ?? 0 }}</span>
                </p>
            </div>
        </div>

        <!-- Tables Available Card -->
        <div class="glass-card rounded-2xl p-6 border border-gold-500/20 hover-glow transition-all transform hover:-translate-y-1 stat-card" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-gold-500/20 to-gold-600/20 border border-gold-500/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-gold-400 text-xs font-medium">Available</p>
                </div>
            </div>
            <div>
                <p class="text-sm text-gray-300 mb-1">Tables Available</p>
                <p class="text-3xl font-bold text-white mb-2">
                    {{ $availableTables }}
                </p>
                <p class="text-xs text-gray-400">
                    Reservations: <span class="text-gray-200 font-medium">{{ $upcomingReservations->count() }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Daily Sales Chart -->
        <div class="glass-card rounded-2xl p-6 border border-gold-500/20 hover-glow">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-white">Daily Sales</h3>
                    <p class="text-sm text-gray-300">Revenue trends this month</p>
                </div>
                <div class="px-3 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/30">
                    <span class="text-xs font-medium text-emerald-300">This Month</span>
                </div>
            </div>
            <div class="relative h-64">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>

        <!-- Order Status Distribution -->
        <div class="glass-card rounded-2xl p-6 border border-gold-500/20 hover-glow">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-white">Order Status</h3>
                    <p class="text-sm text-gray-300">Current order distribution</p>
                </div>
            </div>
            <div class="relative h-64">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Orders -->
        <div class="glass-card rounded-2xl border border-gold-500/20 hover-glow overflow-hidden">
            <div class="p-6 border-b border-gold-500/10">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Recent Orders</h3>
                        <p class="text-sm text-gray-300">Latest customer orders</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm text-gold-400 hover:text-gold-300 font-medium transition-colors">
                        View All →
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gold-500/10">
                @forelse($recentOrders as $order)
                    <div class="p-4 hover:bg-amber-500/5 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500/20 to-blue-600/20 border border-blue-500/30 flex items-center justify-center">
                                    <span class="text-sm font-bold text-blue-400">#{{ $order->id }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">{{ $order->customer->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-gray-300">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-white">${{ number_format($order->total_amount ?? 0, 2) }}</p>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30',
                                        'preparing' => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
                                        'ready' => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                                        'completed' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                        'cancelled' => 'bg-red-500/10 text-red-400 border-red-500/30',
                                    ];
                                    $color = $statusColors[$order->status] ?? 'bg-gray-500/10 text-gray-400 border-gray-500/30';
                                @endphp
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-lg border {{ $color }} mt-1">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm text-gray-400">No orders yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="glass-card rounded-2xl border border-gold-500/20 hover-glow overflow-hidden">
            <div class="p-6 border-b border-gold-500/10">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Low Stock Alerts</h3>
                        <p class="text-sm text-gray-300">Items needing restock</p>
                    </div>
                    <a href="{{ route('admin.inventory.items.index') }}" class="text-sm text-gold-400 hover:text-gold-300 font-medium transition-colors">
                        View All →
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gold-500/10">
                @forelse($lowStockItems as $item)
                    <div class="p-4 hover:bg-amber-500/5 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-red-500/20 to-red-600/20 border border-red-500/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">{{ $item->name }}</p>
                                    <p class="text-xs text-gray-300">{{ $item->category->name ?? 'Uncategorized' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-red-400">{{ $item->quantity }} {{ $item->unit }}</p>
                                <p class="text-xs text-gray-300">Min: {{ $item->minimum_quantity }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-gray-400">All inventory levels are good!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="glass-card rounded-2xl p-6 border border-gold-500/20 hover-glow">
        <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <a href="{{ route('admin.foods.create') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-white/5 hover:bg-gold-500/10 border border-transparent hover:border-gold-500/30 transition-all group">
                <svg class="w-8 h-8 text-gray-400 group-hover:text-gold-400 transition-colors mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-xs text-gray-400 group-hover:text-white transition-colors">Add Food</span>
            </a>
            
            <a href="{{ route('admin.categories.create') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-white/5 hover:bg-gold-500/10 border border-transparent hover:border-gold-500/30 transition-all group">
                <svg class="w-8 h-8 text-gray-400 group-hover:text-gold-400 transition-colors mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span class="text-xs text-gray-400 group-hover:text-white transition-colors">Add Category</span>
            </a>
            
            <a href="{{ route('admin.employees.create') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-white/5 hover:bg-gold-500/10 border border-transparent hover:border-gold-500/30 transition-all group">
                <svg class="w-8 h-8 text-gray-400 group-hover:text-gold-400 transition-colors mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span class="text-xs text-gray-400 group-hover:text-white transition-colors">Add Employee</span>
            </a>
            
            <a href="{{ route('admin.purchases.create') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-white/5 hover:bg-gold-500/10 border border-transparent hover:border-gold-500/30 transition-all group">
                <svg class="w-8 h-8 text-gray-400 group-hover:text-gold-400 transition-colors mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span class="text-xs text-gray-400 group-hover:text-white transition-colors">New Purchase</span>
            </a>
            
            <a href="{{ route('admin.tables.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-white/5 hover:bg-gold-500/10 border border-transparent hover:border-gold-500/30 transition-all group">
                <svg class="w-8 h-8 text-gray-400 group-hover:text-gold-400 transition-colors mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs text-gray-400 group-hover:text-white transition-colors">Manage Tables</span>
            </a>
            
            <a href="{{ route('admin.reports.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-white/5 hover:bg-gold-500/10 border border-transparent hover:border-gold-500/30 transition-all group">
                <svg class="w-8 h-8 text-gray-400 group-hover:text-gold-400 transition-colors mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-xs text-gray-400 group-hover:text-white transition-colors">Generate Report</span>
            </a>
        </div>
    </div>
</div>


@script
<script>
    // Daily Sales Line Chart with Gold Theme
    const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
    const dailySalesLabels = {!! json_encode($dailySalesData['labels'] ?? []) !!};
    const dailySalesData = {!! json_encode($dailySalesData['data'] ?? []) !!};
    
    new Chart(dailySalesCtx, {
        type: 'line',
        data: {
            labels: dailySalesLabels,
            datasets: [{
                label: 'Daily Sales ($)',
                data: dailySalesData,
                borderColor: '#cb943d',
                backgroundColor: 'rgba(203, 148, 61, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#cb943d',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    titleColor: '#f4d03f',
                    bodyColor: '#fff',
                    borderColor: '#cb943d',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    titleFont: {
                        size: 13,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    },
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(203, 148, 61, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#d1d5db',
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            return '\$' + value.toFixed(0);
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#d1d5db',
                        font: {
                            size: 11
                        },
                        maxRotation: 45,
                        minRotation: 0
                    }
                }
            }
        }
    });

    // Order Status Doughnut Chart
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const orderStatusLabels = {!! json_encode($orderStatusData['labels'] ?? ['Pending', 'Preparing', 'Ready', 'Completed', 'Cancelled']) !!};
    const orderStatusData = {!! json_encode($orderStatusData['data'] ?? [0, 0, 0, 0, 0]) !!};
    
    new Chart(orderStatusCtx, {
        type: 'doughnut',
        data: {
            labels: orderStatusLabels,
            datasets: [{
                data: orderStatusData,
                backgroundColor: [
                    'rgba(234, 179, 8, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderColor: '#0f172a',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.5,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#d1d5db',
                        padding: 12,
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        boxHeight: 8
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    titleColor: '#f4d03f',
                    bodyColor: '#fff',
                    borderColor: '#cb943d',
                    borderWidth: 1,
                    padding: 12,
                    titleFont: {
                        size: 13,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    }
                }
            },
            cutout: '65%'
        }
    });
</script>
@endscript



