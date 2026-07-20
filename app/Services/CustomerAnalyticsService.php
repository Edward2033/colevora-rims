<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CustomerAnalyticsService
{
    /**
     * Get new customers within date range.
     *
     * @return array<string, mixed>
     */
    public function getNewCustomers(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = User::where('user_type', 'customer');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $customers = $query->get();

        return [
            'total' => $customers->count(),
            'customers' => $customers,
        ];
    }

    /**
     * Get returning customers (customers with more than one order).
     *
     * @return array<string, mixed>
     */
    public function getReturningCustomers(): array
    {
        $customers = User::where('user_type', 'customer')
            ->withCount('orders')
            ->get()
            ->filter(fn ($customer) => $customer->orders_count > 1)
            ->values();

        return [
            'total' => $customers->count(),
            'customers' => $customers,
        ];
    }

    /**
     * Get customer order history.
     *
     * @return array<string, mixed>
     */
    public function getCustomerOrderHistory(int $customerId): array
    {
        $customer = User::with(['orders.items.food', 'orders.payment'])
            ->findOrFail($customerId);

        $orders = $customer->orders;

        return [
            'customer' => $customer,
            'total_orders' => $orders->count(),
            'total_spent' => $orders->sum('total_amount'),
            'completed_orders' => $orders->where('status', 'completed')->count(),
            'pending_orders' => $orders->where('status', 'pending')->count(),
            'orders' => $orders,
        ];
    }

    /**
     * Get top customers by order count.
     */
    public function getTopCustomersByOrderCount(int $limit = 10): Collection
    {
        return User::where('user_type', 'customer')
            ->withCount('orders')
            ->get()
            ->filter(fn ($customer) => $customer->orders_count > 0)
            ->sortByDesc('orders_count')
            ->take($limit)
            ->values();
    }

    /**
     * Get top customers by total spent.
     */
    public function getTopCustomersBySpending(int $limit = 10): Collection
    {
        return User::where('user_type', 'customer')
            ->withSum('orders', 'total_amount')
            ->get()
            ->filter(fn ($customer) => ($customer->orders_sum_total_amount ?? 0) > 0)
            ->sortByDesc('orders_sum_total_amount')
            ->take($limit)
            ->values();
    }

    /**
     * Get customer statistics.
     *
     * @return array<string, mixed>
     */
    public function getCustomerStatistics(): array
    {
        $totalCustomers = User::where('user_type', 'customer')->count();
        $customersWithOrders = User::where('user_type', 'customer')
            ->has('orders')
            ->count();

        return [
            'total_customers' => $totalCustomers,
            'customers_with_orders' => $customersWithOrders,
            'customers_without_orders' => $totalCustomers - $customersWithOrders,
            'new_customers_today' => User::where('user_type', 'customer')
                ->whereDate('created_at', today())
                ->count(),
            'new_customers_this_month' => User::where('user_type', 'customer')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Get customer growth by month.
     */
    public function getCustomerGrowth(int $months = 12): Collection
    {
        $startDate = now()->subMonths($months)->startOfMonth();

        return User::where('user_type', 'customer')
            ->where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
