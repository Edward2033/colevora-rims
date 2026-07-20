<?php

namespace App\Services;

use App\Models\FoodIngredient;
use App\Models\InventoryItem;
use App\Models\Purchase;
use App\Models\StockTransaction;
use Carbon\Carbon;

class InventoryReportService
{
    /**
     * Get current stock report.
     *
     * @return array<string, mixed>
     */
    public function getCurrentStockReport(): array
    {
        $items = InventoryItem::with(['category', 'supplier'])
            ->where('status', 'active')
            ->get();

        $totalValue = $items->sum(fn ($item) => $item->quantity * $item->cost_price);

        return [
            'total_items' => $items->count(),
            'total_value' => $totalValue,
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'category' => $item->category?->name,
                    'supplier' => $item->supplier?->name,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'cost_price' => $item->cost_price,
                    'total_value' => $item->quantity * $item->cost_price,
                    'status' => $item->isLowStock() ? 'low_stock' : 'normal',
                ];
            }),
        ];
    }

    /**
     * Get low stock report.
     *
     * @return array<string, mixed>
     */
    public function getLowStockReport(): array
    {
        $items = InventoryItem::with(['category', 'supplier', 'alerts' => function ($query) {
            $query->where('status', 'active');
        }])
            ->lowStock()
            ->get();

        return [
            'total_low_stock_items' => $items->count(),
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'category' => $item->category?->name,
                    'supplier' => $item->supplier?->name,
                    'current_quantity' => $item->quantity,
                    'minimum_quantity' => $item->minimum_quantity,
                    'unit' => $item->unit,
                    'shortage' => $item->minimum_quantity - $item->quantity,
                    'active_alerts' => $item->alerts->count(),
                ];
            }),
        ];
    }

    /**
     * Get ingredient usage report.
     *
     * @return array<string, mixed>
     */
    public function getIngredientUsageReport(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = StockTransaction::with(['inventoryItem.category'])
            ->where('type', 'usage');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $transactions = $query->get();

        $usageByItem = $transactions->groupBy('inventory_item_id')->map(function ($group) {
            $item = $group->first()->inventoryItem;

            return [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'category' => $item->category?->name,
                'unit' => $item->unit,
                'total_used' => abs($group->sum('quantity')),
                'usage_count' => $group->count(),
                'current_stock' => $item->quantity,
            ];
        })->sortByDesc('total_used')->values();

        return [
            'total_transactions' => $transactions->count(),
            'usage_by_item' => $usageByItem,
        ];
    }

    /**
     * Get purchase report.
     *
     * @return array<string, mixed>
     */
    public function getPurchaseReport(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Purchase::with(['supplier', 'items.inventoryItem', 'creator']);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $purchases = $query->get();

        $totalAmount = $purchases->sum('total_amount');
        $bySupplier = $purchases->groupBy('supplier_id')->map(function ($group) {
            $supplier = $group->first()->supplier;

            return [
                'supplier_name' => $supplier?->name,
                'total_purchases' => $group->count(),
                'total_amount' => $group->sum('total_amount'),
            ];
        })->sortByDesc('total_amount')->values();

        $byStatus = $purchases->groupBy('status')->map(fn ($group) => $group->count());

        return [
            'total_purchases' => $purchases->count(),
            'total_amount' => $totalAmount,
            'by_supplier' => $bySupplier,
            'by_status' => $byStatus,
            'purchases' => $purchases->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'purchase_number' => $purchase->purchase_number,
                    'supplier' => $purchase->supplier?->name,
                    'total_amount' => $purchase->total_amount,
                    'status' => $purchase->status,
                    'items_count' => $purchase->items->count(),
                    'created_at' => $purchase->created_at,
                    'created_by' => $purchase->creator?->name,
                ];
            }),
        ];
    }

    /**
     * Get stock movement report.
     *
     * @return array<string, mixed>
     */
    public function getStockMovementReport(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = StockTransaction::with(['inventoryItem', 'creator']);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $transactions = $query->get();

        $byType = $transactions->groupBy('type')->map(function ($group, $type) {
            return [
                'type' => $type,
                'count' => $group->count(),
                'total_quantity' => abs($group->sum('quantity')),
            ];
        })->values();

        return [
            'total_transactions' => $transactions->count(),
            'by_type' => $byType,
            'transactions' => $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'item_name' => $transaction->inventoryItem->name,
                    'type' => $transaction->type,
                    'quantity' => $transaction->quantity,
                    'notes' => $transaction->notes,
                    'created_at' => $transaction->created_at,
                    'created_by' => $transaction->creator?->name,
                ];
            }),
        ];
    }

    /**
     * Get inventory valuation report.
     *
     * @return array<string, mixed>
     */
    public function getInventoryValuationReport(): array
    {
        $items = InventoryItem::with('category')
            ->where('status', 'active')
            ->get();

        $byCategory = $items->groupBy('category_id')->map(function ($group) {
            $category = $group->first()->category;
            $totalValue = $group->sum(fn ($item) => $item->quantity * $item->cost_price);

            return [
                'category_name' => $category?->name ?? 'Uncategorized',
                'items_count' => $group->count(),
                'total_value' => $totalValue,
            ];
        })->sortByDesc('total_value')->values();

        $totalValue = $items->sum(fn ($item) => $item->quantity * $item->cost_price);

        return [
            'total_inventory_value' => $totalValue,
            'total_items' => $items->count(),
            'by_category' => $byCategory,
        ];
    }

    /**
     * Get low stock items (alias used by reports page).
     */
    public function getLowStockItems(): \Illuminate\Support\Collection
    {
        return InventoryItem::with(['category', 'supplier'])->lowStock()->get();
    }

    /**
     * Get total stock value.
     */
    public function getTotalStockValue(): float
    {
        return (float) InventoryItem::where('status', 'active')
            ->get()
            ->sum(fn ($item) => $item->quantity * $item->cost_price);
    }

    /**
     * Get recent stock transactions.
     */
    public function getRecentTransactions(int $limit = 20): \Illuminate\Support\Collection
    {
        return StockTransaction::with('inventoryItem')->latest()->limit($limit)->get();
    }

    /**
     * Get employee performance summary (stub for reports page).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getEmployeePerformance(): array
    {
        return [];
    }

    /**
     * Get ingredient forecast report (based on food recipes).
     *
     * @return array<string, mixed>
     */
    public function getIngredientForecastReport(int $days = 7): array
    {
        $ingredients = FoodIngredient::with(['inventoryItem', 'food'])
            ->get()
            ->groupBy('inventory_item_id');

        $forecast = $ingredients->map(function ($group) use ($days) {
            $item = $group->first()->inventoryItem;
            $totalRequired = $group->sum('quantity_required');

            // Calculate average daily usage based on recent transactions
            $recentUsage = StockTransaction::where('inventory_item_id', $item->id)
                ->where('type', 'usage')
                ->where('created_at', '>=', now()->subDays(30))
                ->get();

            $averageDailyUsage = $recentUsage->count() > 0
                ? abs($recentUsage->sum('quantity')) / 30
                : 0;

            $projectedUsage = $averageDailyUsage * $days;
            $projectedStock = $item->quantity - $projectedUsage;

            return [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'current_stock' => $item->quantity,
                'unit' => $item->unit,
                'average_daily_usage' => round($averageDailyUsage, 2),
                'projected_usage_'.$days.'_days' => round($projectedUsage, 2),
                'projected_stock' => round($projectedStock, 2),
                'needs_reorder' => $projectedStock <= $item->minimum_quantity,
                'used_in_foods' => $group->pluck('food.name'),
            ];
        })->sortBy('projected_stock')->values();

        return [
            'forecast_period_days' => $days,
            'items' => $forecast,
            'items_needing_reorder' => $forecast->where('needs_reorder', true)->count(),
        ];
    }
}
