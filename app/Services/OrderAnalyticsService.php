<?php

namespace App\Services;

use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderAnalyticsService
{
    /**
     * Get orders by status.
     *
     * @return array<string, mixed>
     */
    public function getOrdersByStatus(): array
    {
        $orders = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return [
            'labels' => $orders->pluck('status')->toArray(),
            'data' => $orders->pluck('count')->toArray(),
        ];
    }

    /**
     * Get order status distribution.
     *
     * @return array<string, mixed>
     */
    public function getOrderStatusDistribution(): array
    {
        return $this->getOrdersByStatus();
    }

    /**
     * Get popular food items.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPopularFoods(int $limit = 10): array
    {
        return $this->getPopularFoodItems($limit);
    }

    /**
     * Get orders by type.
     *
     * @return array<string, mixed>
     */
    public function getOrdersByType(): array
    {
        $orders = Order::select('order_type', DB::raw('COUNT(*) as count'))
            ->groupBy('order_type')
            ->get();

        return [
            'labels' => $orders->pluck('order_type')->toArray(),
            'data' => $orders->pluck('count')->toArray(),
        ];
    }

    /**
     * Get popular food items.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPopularFoodItems(int $limit = 10): array
    {
        return OrderItem::select('food_id', DB::raw('SUM(quantity) as total_ordered'))
            ->groupBy('food_id')
            ->orderByDesc('total_ordered')
            ->limit($limit)
            ->with('food')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->food->name ?? 'Unknown',
                    'total_ordered' => $item->total_ordered,
                ];
            })
            ->toArray();
    }

    /**
     * Get most ordered categories.
     *
     * @return array<string, mixed>
     */
    public function getMostOrderedCategories(): array
    {
        $categories = OrderItem::join('food', 'order_items.food_id', '=', 'food.id')
            ->join('categories', 'food.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.quantity) as total_ordered'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_ordered')
            ->get();

        return [
            'labels' => $categories->pluck('name')->toArray(),
            'data' => $categories->pluck('total_ordered')->toArray(),
        ];
    }

    /**
     * Get order trends over time.
     *
     * @return array<string, mixed>
     */
    public function getOrderTrends(int $days = 30): array
    {
        $orders = Order::where('created_at', '>=', now()->subDays($days))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $orders->pluck('date')->toArray(),
            'data' => $orders->pluck('count')->toArray(),
        ];
    }
}
