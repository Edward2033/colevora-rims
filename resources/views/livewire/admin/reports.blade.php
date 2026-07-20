<?php

use App\Services\SalesAnalyticsService;
use App\Services\OrderAnalyticsService;
use App\Services\CustomerAnalyticsService;
use App\Services\EmployeePerformanceService;
use App\Services\InventoryReportService;
use function Livewire\Volt\{state, mount, layout};

layout('components.layouts.admin');

state([
    'selectedReport' => 'sales',
    'salesData'      => [],
    'orderData'      => [],
    'customerData'   => [],
    'employeeData'   => [],
    'inventoryData'  => [],
]);

mount(function () {
    $this->loadReportData();
});

$loadReportData = function () {
    $salesService     = app(SalesAnalyticsService::class);
    $orderService     = app(OrderAnalyticsService::class);
    $customerService  = app(CustomerAnalyticsService::class);
    $employeeService  = app(EmployeePerformanceService::class);
    $inventoryService = app(InventoryReportService::class);

    $this->salesData = [
        'daily'    => $salesService->getDailySales(),
        'monthly'  => $salesService->getMonthlyRevenue(),
        'yearly'   => $salesService->getYearlyComparison(),
        'byMethod' => $salesService->getSalesByPaymentMethod(),
    ];

    $this->orderData = [
        'status'  => $orderService->getOrderStatusDistribution(),
        'popular' => $orderService->getPopularFoods(),
        'byType'  => $orderService->getOrdersByType(),
    ];

    $this->customerData = [
        'topCustomers' => $customerService->getTopCustomersBySpending(10)->map(fn($c) => [
            'name'         => $c->name,
            'email'        => $c->email,
            'total_orders' => $c->orders()->count(),
            'total_spent'  => (float) ($c->orders_sum_total_amount ?? 0),
        ])->toArray(),
        'newCustomers'   => $customerService->getCustomerStatistics()['new_customers_this_month'],
        'totalCustomers' => $customerService->getCustomerStatistics()['total_customers'],
        'withOrders'     => $customerService->getCustomerStatistics()['customers_with_orders'],
        'retention'      => $customerService->getCustomerStatistics()['total_customers'] > 0
            ? round($customerService->getCustomerStatistics()['customers_with_orders'] / $customerService->getCustomerStatistics()['total_customers'] * 100, 1)
            : 0,
    ];

    $this->employeeData = [
        'performance'      => $employeeService->getEmployeePerformance(),
        'ordersByEmployee' => $employeeService->getOrdersByEmployee(),
    ];

    $this->inventoryData = [
        'lowStock'     => $inventoryService->getLowStockItems(),
        'stockValue'   => $inventoryService->getTotalStockValue(),
        'transactions' => $inventoryService->getRecentTransactions(),
    ];
};

$changeReport = function (string $type) {
    $this->selectedReport = $type;
};

?>

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Reports & Analytics</h1>
            <p class="text-sm text-gray-400 mt-1">Comprehensive business insights and performance metrics</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.reports.export', 'sales') }}"
               class="flex items-center gap-2 bg-green-500/10 hover:bg-green-500/20 border border-green-500/30 text-green-400 px-4 py-2 rounded-xl text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Sales
            </a>
            <a href="{{ route('admin.reports.export', 'inventory') }}"
               class="flex items-center gap-2 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/30 text-blue-400 px-4 py-2 rounded-xl text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Inventory
            </a>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="glass-card rounded-2xl border border-gold-500/20 overflow-hidden">
        <nav class="flex overflow-x-auto">
            @foreach([
                'sales'     => 'Sales Analytics',
                'orders'    => 'Orders Report',
                'customers' => 'Customer Insights',
                'employees' => 'Employee Performance',
                'inventory' => 'Inventory Report',
            ] as $key => $label)
            <button wire:click="changeReport('{{ $key }}')"
                    class="px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition
                        {{ $selectedReport === $key
                            ? 'border-gold-500 text-gold-400 bg-gold-500/5'
                            : 'border-transparent text-gray-400 hover:text-white hover:border-white/20' }}">
                {{ $label }}
            </button>
            @endforeach
        </nav>
    </div>

    {{-- ── SALES ── --}}
    @if($selectedReport === 'sales')
    <div class="space-y-6">
        {{-- Summary stats --}}
        @php
            $totalRevenue = array_sum($salesData['monthly']['data'] ?? []);
            $todayRevenue = array_sum(array_filter($salesData['daily']['data'] ?? [], fn($v,$k) => ($salesData['daily']['labels'][$k] ?? '') === now()->toDateString(), ARRAY_FILTER_USE_BOTH));
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">This Month</p>
                <p class="text-2xl font-bold text-white mt-1">${{ number_format(array_sum($salesData['monthly']['data'] ?? []), 2) }}</p>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">This Year</p>
                @php
                    $yearlyData = $salesData['yearly']['data'] ?? [0];
                    $thisYearRevenue = end($yearlyData) ?: 0;
                @endphp
                <p class="text-2xl font-bold text-white mt-1">${{ number_format($thisYearRevenue, 2) }}</p>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Payment Methods</p>
                <p class="text-2xl font-bold text-white mt-1">{{ count($salesData['byMethod']['labels'] ?? []) }}</p>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Sales Days (Month)</p>
                <p class="text-2xl font-bold text-white mt-1">{{ count($salesData['daily']['labels'] ?? []) }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="glass-card rounded-2xl border border-gold-500/20 p-6">
                <h3 class="text-base font-semibold text-white mb-4">Daily Sales Trend</h3>
                <div class="relative" style="height:220px">
                    <canvas id="dailySalesReportChart"></canvas>
                </div>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-6">
                <h3 class="text-base font-semibold text-white mb-4">Monthly Revenue</h3>
                <div class="relative" style="height:220px">
                    <canvas id="monthlyRevenueReportChart"></canvas>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="glass-card rounded-2xl border border-gold-500/20 p-6">
                <h3 class="text-base font-semibold text-white mb-4">Sales by Payment Method</h3>
                <div class="relative" style="height:220px">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-6">
                <h3 class="text-base font-semibold text-white mb-4">Yearly Comparison</h3>
                <div class="relative" style="height:220px">
                    <canvas id="yearlyComparisonChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── ORDERS ── --}}
    @if($selectedReport === 'orders')
    <div class="space-y-6">
        {{-- Summary stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $statusLabels = $orderData['status']['labels'] ?? [];
                $statusData   = $orderData['status']['data'] ?? [];
                $statusMap    = array_combine($statusLabels, $statusData) ?: [];
            @endphp
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-yellow-400 uppercase tracking-wider">Pending</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $statusMap['pending'] ?? 0 }}</p>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Preparing</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $statusMap['preparing'] ?? 0 }}</p>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-green-400 uppercase tracking-wider">Completed</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $statusMap['completed'] ?? 0 }}</p>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-red-400 uppercase tracking-wider">Cancelled</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $statusMap['cancelled'] ?? 0 }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="glass-card rounded-2xl border border-gold-500/20 p-6">
                <h3 class="text-base font-semibold text-white mb-4">Order Status Distribution</h3>
                <div class="relative" style="height:220px">
                    <canvas id="orderStatusReportChart"></canvas>
                </div>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-6">
                <h3 class="text-base font-semibold text-white mb-4">Orders by Type</h3>
                <div class="relative" style="height:220px">
                    <canvas id="orderTypeChart"></canvas>
                </div>
            </div>
        </div>
        <div class="glass-card rounded-2xl border border-gold-500/20 overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5">
                <h3 class="text-base font-semibold text-white">Top 10 Popular Foods</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white/[0.04] border-b border-white/[0.07]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Food Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Orders</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($orderData['popular'] ?? [] as $i => $food)
                        <tr class="hover:bg-white/[0.03] transition">
                            <td class="px-6 py-4 font-bold text-gold-400">#{{ $i + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-white">{{ $food['name'] ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $food['total_ordered'] ?? 0 }}</td>
                            <td class="px-6 py-4 text-green-400 font-medium">—</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No data available</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ── CUSTOMERS ── --}}
    @if($selectedReport === 'customers')
    <div class="space-y-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Customers</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $customerData['totalCustomers'] ?? 0 }}</p>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">New This Month</p>
                <p class="text-2xl font-bold text-gold-400 mt-1">{{ $customerData['newCustomers'] ?? 0 }}</p>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">With Orders</p>
                <p class="text-2xl font-bold text-green-400 mt-1">{{ $customerData['withOrders'] ?? 0 }}</p>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Retention Rate</p>
                <p class="text-2xl font-bold text-blue-400 mt-1">{{ number_format($customerData['retention'] ?? 0, 1) }}%</p>
            </div>
        </div>
        <div class="glass-card rounded-2xl border border-gold-500/20 overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5">
                <h3 class="text-base font-semibold text-white">Top Customers by Revenue</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white/[0.04] border-b border-white/[0.07]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Orders</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Spent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($customerData['topCustomers'] ?? [] as $i => $customer)
                        <tr class="hover:bg-white/[0.03] transition">
                            <td class="px-6 py-4 font-bold text-gold-400">#{{ $i + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-white">{{ $customer['name'] }}</td>
                            <td class="px-6 py-4 text-gray-400">{{ $customer['email'] ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $customer['total_orders'] }}</td>
                            <td class="px-6 py-4 text-green-400 font-semibold">${{ number_format($customer['total_spent'], 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No customer data available</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ── EMPLOYEES ── --}}
    @if($selectedReport === 'employees')
    <div class="glass-card rounded-2xl border border-gold-500/20 overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5">
            <h3 class="text-base font-semibold text-white">Employee Performance Metrics</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-white/[0.04] border-b border-white/[0.07]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Orders Handled</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Performance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($employeeData['performance'] ?? [] as $employee)
                    <tr class="hover:bg-white/[0.03] transition">
                        <td class="px-6 py-4 font-medium text-white">{{ $employee['name'] }}</td>
                        <td class="px-6 py-4 text-gray-400">{{ $employee['role'] }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ $employee['orders'] }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 max-w-[120px] bg-white/10 rounded-full h-1.5">
                                    <div class="bg-gradient-to-r from-gold-500 to-gold-400 h-1.5 rounded-full" style="width: {{ $employee['score'] }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gold-400 w-10">{{ $employee['score'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No data available</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── INVENTORY ── --}}
    @if($selectedReport === 'inventory')
    <div class="space-y-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Stock Value</p>
                <p class="text-2xl font-bold text-green-400 mt-1">${{ number_format($inventoryData['stockValue'] ?? 0, 2) }}</p>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Low Stock Items</p>
                <p class="text-2xl font-bold text-red-400 mt-1">{{ count($inventoryData['lowStock'] ?? []) }}</p>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Recent Transactions</p>
                <p class="text-2xl font-bold text-white mt-1">{{ count($inventoryData['transactions'] ?? []) }}</p>
            </div>
            <div class="glass-card rounded-2xl border border-gold-500/20 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Stock-In Transactions</p>
                <p class="text-2xl font-bold text-blue-400 mt-1">{{ collect($inventoryData['transactions'] ?? [])->where('quantity', '>', 0)->count() }}</p>
            </div>
        </div>

        <div class="glass-card rounded-2xl border border-gold-500/20 overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5">
                <h3 class="text-base font-semibold text-white">Low Stock Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white/[0.04] border-b border-white/[0.07]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Current Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Reorder Level</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($inventoryData['lowStock'] ?? [] as $item)
                        <tr class="hover:bg-white/[0.03] transition">
                            <td class="px-6 py-4 font-medium text-white">{{ $item->name }}</td>
                            <td class="px-6 py-4 font-semibold text-red-400">{{ $item->quantity }} {{ $item->unit }}</td>
                            <td class="px-6 py-4 text-gray-400">{{ $item->minimum_quantity }} {{ $item->unit }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-red-400/20 text-red-400 border border-red-400/30">Low Stock</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No low stock items</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="glass-card rounded-2xl border border-gold-500/20 overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5">
                <h3 class="text-base font-semibold text-white">Recent Stock Transactions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white/[0.04] border-b border-white/[0.07]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Quantity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($inventoryData['transactions'] ?? [] as $tx)
                        <tr class="hover:bg-white/[0.03] transition">
                            <td class="px-6 py-4 text-gray-400">{{ $tx->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-medium text-white">{{ $tx->inventoryItem->name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full
                                    {{ $tx->quantity > 0 ? 'bg-green-400/20 text-green-400 border border-green-400/30' : 'bg-red-400/20 text-red-400 border border-red-400/30' }}">
                                    {{ strtoupper($tx->type ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold {{ $tx->quantity > 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $tx->quantity > 0 ? '+' : '' }}{{ $tx->quantity }} {{ $tx->inventoryItem?->unit }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No transactions</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>

@script
<script>
    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: '#d1d5db',
                    font: { size: 11 },
                    usePointStyle: true,
                    boxWidth: 8,
                }
            },
            tooltip: {
                backgroundColor: 'rgba(15,23,42,0.95)',
                titleColor: '#f4d03f',
                bodyColor: '#fff',
                borderColor: '#cb943d',
                borderWidth: 1,
                padding: 10,
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { color: '#9ca3af', font: { size: 11 }, maxRotation: 45 }
            },
            y: {
                grid: { color: 'rgba(203,148,61,0.08)' },
                ticks: { color: '#9ca3af', font: { size: 11 } }
            }
        }
    };

    const doughnutDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#d1d5db', font: { size: 11 }, usePointStyle: true, boxWidth: 8, padding: 12 }
            },
            tooltip: {
                backgroundColor: 'rgba(15,23,42,0.95)',
                titleColor: '#f4d03f',
                bodyColor: '#fff',
                borderColor: '#cb943d',
                borderWidth: 1,
                padding: 10,
            }
        }
    };

    function buildChart(id, config) {
        const el = document.getElementById(id);
        if (!el) return;
        // Destroy existing instance if any
        const existing = Chart.getChart(el);
        if (existing) existing.destroy();
        new Chart(el.getContext('2d'), config);
    }

    function initCharts() {
        // Daily Sales
        buildChart('dailySalesReportChart', {
            type: 'line',
            data: {
                labels: @json($salesData['daily']['labels'] ?? []),
                datasets: [{
                    label: 'Sales ($)',
                    data: @json($salesData['daily']['data'] ?? []),
                    borderColor: '#cb943d',
                    backgroundColor: 'rgba(203,148,61,0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#cb943d',
                    pointRadius: 3,
                }]
            },
            options: chartDefaults
        });

        // Monthly Revenue
        buildChart('monthlyRevenueReportChart', {
            type: 'bar',
            data: {
                labels: @json($salesData['monthly']['labels'] ?? []),
                datasets: [{
                    label: 'Revenue ($)',
                    data: @json($salesData['monthly']['data'] ?? []),
                    backgroundColor: 'rgba(203,148,61,0.7)',
                    borderColor: '#cb943d',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: chartDefaults
        });

        // Payment Method
        buildChart('paymentMethodChart', {
            type: 'doughnut',
            data: {
                labels: @json($salesData['byMethod']['labels'] ?? []),
                datasets: [{
                    data: @json($salesData['byMethod']['data'] ?? []),
                    backgroundColor: ['rgba(203,148,61,0.8)', 'rgba(59,130,246,0.8)', 'rgba(16,185,129,0.8)', 'rgba(168,85,247,0.8)'],
                    borderColor: '#0f172a',
                    borderWidth: 2,
                }]
            },
            options: doughnutDefaults
        });

        // Yearly Comparison
        buildChart('yearlyComparisonChart', {
            type: 'bar',
            data: {
                labels: @json($salesData['yearly']['labels'] ?? []),
                datasets: [{
                    label: 'Revenue ($)',
                    data: @json($salesData['yearly']['data'] ?? []),
                    backgroundColor: 'rgba(203,148,61,0.7)',
                    borderColor: '#cb943d',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: chartDefaults
        });

        // Order Status
        buildChart('orderStatusReportChart', {
            type: 'doughnut',
            data: {
                labels: @json($orderData['status']['labels'] ?? []),
                datasets: [{
                    data: @json($orderData['status']['data'] ?? []),
                    backgroundColor: ['rgba(251,191,36,0.8)','rgba(59,130,246,0.8)','rgba(168,85,247,0.8)','rgba(16,185,129,0.8)','rgba(239,68,68,0.8)'],
                    borderColor: '#0f172a',
                    borderWidth: 2,
                }]
            },
            options: doughnutDefaults
        });

        // Order Type
        buildChart('orderTypeChart', {
            type: 'doughnut',
            data: {
                labels: @json($orderData['byType']['labels'] ?? []),
                datasets: [{
                    data: @json($orderData['byType']['data'] ?? []),
                    backgroundColor: ['rgba(203,148,61,0.8)','rgba(59,130,246,0.8)','rgba(16,185,129,0.8)'],
                    borderColor: '#0f172a',
                    borderWidth: 2,
                }]
            },
            options: doughnutDefaults
        });
    }

    // Run on initial load and after Livewire re-renders
    initCharts();
    document.addEventListener('livewire:navigated', initCharts);
</script>
@endscript
