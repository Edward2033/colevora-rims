<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsService
{
    /**
     * Get daily sales for the current month.
     *
     * @return array<string, mixed>
     */
    public function getDailySales(): array
    {
        $sales = Payment::completed()
            ->whereNotNull('paid_at')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $sales->pluck('date')->toArray(),
            'data' => $sales->pluck('total')->map(fn ($v) => (float) $v)->toArray(),
        ];
    }

    /**
     * Get monthly revenue for the current year.
     *
     * @return array<string, mixed>
     */
    public function getMonthlyRevenue(): array
    {
        $revenue = Payment::completed()
            ->whereNotNull('paid_at')
            ->whereYear('paid_at', now()->year)
            ->select(
                DB::raw("MONTH(paid_at) as month"),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = array_fill(0, 12, 0);

        foreach ($revenue as $item) {
            $data[$item->month - 1] = (float) $item->total;
        }

        return [
            'labels' => $months,
            'data' => $data,
        ];
    }

    /**
     * Get yearly revenue comparison.
     *
     * @return array<string, mixed>
     */
    public function getYearlyComparison(): array
    {
        $currentYear = now()->year;
        $years = [$currentYear - 2, $currentYear - 1, $currentYear];

        $comparison = [];
        foreach ($years as $year) {
            $total = Payment::completed()
                ->whereNotNull('paid_at')
                ->whereYear('paid_at', $year)
                ->sum('amount');

            $comparison[] = [
                'year' => (string) $year,
                'total' => (float) $total,
            ];
        }

        return [
            'labels' => array_column($comparison, 'year'),
            'data' => array_column($comparison, 'total'),
        ];
    }

    /**
     * Get sales by payment method.
     *
     * @return array<string, mixed>
     */
    public function getSalesByPaymentMethod(): array
    {
        $sales = Payment::completed()
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        return [
            'labels' => $sales->pluck('payment_method')->toArray(),
            'data' => $sales->pluck('total')->map(fn ($v) => (float) $v)->toArray(),
        ];
    }
}
