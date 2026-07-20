<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

class DashboardService
{
    /**
     * Get all dashboard statistics.
     *
     * @return array<string, mixed>
     */
    public function getStatistics(): array
    {
        return [
            'sales' => $this->getSalesStatistics(),
            'orders' => $this->getOrderStatistics(),
            'customers' => $this->getCustomerCount(),
            'employees' => $this->getEmployeeCount(),
            'low_stock_items' => $this->getLowStockCount(),
        ];
    }

    /**
     * Get sales statistics.
     *
     * @return array<string, float>
     */
    public function getSalesStatistics(): array
    {
        return [
            'total_sales' => $this->getTotalSales(),
            'today_sales' => $this->getTodaySales(),
            'monthly_sales' => $this->getMonthlySales(),
        ];
    }

    /**
     * Get total sales from all completed payments.
     */
    public function getTotalSales(): float
    {
        return (float) Payment::completed()->sum('amount');
    }

    /**
     * Get today's sales.
     */
    public function getTodaySales(): float
    {
        return (float) Payment::completed()
            ->whereDate('paid_at', today())
            ->sum('amount');
    }

    /**
     * Get current month sales.
     */
    public function getMonthlySales(): float
    {
        return (float) Payment::completed()
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');
    }

    /**
     * Get order statistics.
     *
     * @return array<string, int>
     */
    public function getOrderStatistics(): array
    {
        return [
            'total_orders' => Order::count(),
            'pending_orders' => Order::pending()->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
        ];
    }

    /**
     * Get customer count.
     */
    public function getCustomerCount(): int
    {
        return User::where('user_type', 'customer')->count();
    }

    /**
     * Get employee count.
     */
    public function getEmployeeCount(): int
    {
        return Employee::count();
    }

    /**
     * Get low stock items count.
     */
    public function getLowStockCount(): int
    {
        return InventoryItem::lowStock()->count();
    }
}
