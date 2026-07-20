<?php

namespace App\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ReportExportService
{
    /**
     * Export data to CSV format.
     *
     * @param  array<string>  $headers
     */
    public function exportToCsv(Collection|array $data, array $headers, string $filename = 'report.csv'): string
    {
        $data = is_array($data) ? collect($data) : $data;

        $output = fopen('php://temp', 'r+');

        // Write headers
        fputcsv($output, $headers);

        // Write data rows
        foreach ($data as $row) {
            $rowData = is_array($row) ? $row : (array) $row;
            fputcsv($output, $rowData);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Generate CSV response for download.
     *
     * @param  array<string>  $headers
     * @return Response
     */
    public function downloadCsv(Collection|array $data, array $headers, string $filename = 'report.csv')
    {
        $csv = $this->exportToCsv($data, $headers, $filename);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export sales report to CSV.
     */
    public function exportSalesReportToCsv(Collection $sales): string
    {
        $headers = ['Date', 'Order Number', 'Customer', 'Total Amount', 'Payment Method', 'Status'];

        $data = $sales->map(function ($sale) {
            return [
                'date' => $sale->created_at?->format('Y-m-d H:i:s') ?? '',
                'order_number' => $sale->order_number ?? '',
                'customer' => $sale->customer?->name ?? 'Guest',
                'total_amount' => $sale->total_amount ?? 0,
                'payment_method' => $sale->payment?->payment_method ?? 'N/A',
                'status' => $sale->status ?? '',
            ];
        });

        return $this->exportToCsv($data, $headers, 'sales_report.csv');
    }

    /**
     * Export inventory report to CSV.
     */
    public function exportInventoryReportToCsv(Collection $items): string
    {
        $headers = ['Item Name', 'Category', 'Supplier', 'Quantity', 'Unit', 'Cost Price', 'Total Value', 'Status'];

        $data = $items->map(function ($item) {
            return [
                'name' => $item['name'] ?? $item->name ?? '',
                'category' => $item['category'] ?? $item->category?->name ?? '',
                'supplier' => $item['supplier'] ?? $item->supplier?->name ?? '',
                'quantity' => $item['quantity'] ?? $item->quantity ?? 0,
                'unit' => $item['unit'] ?? $item->unit ?? '',
                'cost_price' => $item['cost_price'] ?? $item->cost_price ?? 0,
                'total_value' => $item['total_value'] ?? ($item->quantity * $item->cost_price) ?? 0,
                'status' => $item['status'] ?? ($item->isLowStock() ? 'Low Stock' : 'Normal') ?? '',
            ];
        });

        return $this->exportToCsv($data, $headers, 'inventory_report.csv');
    }

    /**
     * Export customer report to CSV.
     */
    public function exportCustomerReportToCsv(Collection $customers): string
    {
        $headers = ['Name', 'Email', 'Phone', 'Total Orders', 'Total Spent', 'Registration Date'];

        $data = $customers->map(function ($customer) {
            return [
                'name' => $customer->name ?? '',
                'email' => $customer->email ?? '',
                'phone' => $customer->phone ?? '',
                'total_orders' => $customer->orders_count ?? 0,
                'total_spent' => $customer->orders_sum_total_amount ?? 0,
                'registration_date' => $customer->created_at?->format('Y-m-d') ?? '',
            ];
        });

        return $this->exportToCsv($data, $headers, 'customer_report.csv');
    }

    /**
     * Export employee performance report to CSV.
     */
    public function exportEmployeePerformanceToCsv(Collection $performance, string $role): string
    {
        $headers = match (strtolower($role)) {
            'chef' => ['Employee Name', 'Email', 'Total Orders Prepared', 'Completed Orders', 'Total Items Prepared'],
            'waiter' => ['Employee Name', 'Email', 'Total Orders Handled', 'Delivered Orders', 'Total Sales', 'Average Order Value'],
            'cashier' => ['Employee Name', 'Email', 'Total Payments Processed', 'Total Amount Collected', 'Average Transaction Value'],
            default => ['Employee Name', 'Email', 'Performance Score'],
        };

        $data = $performance->map(function ($item) use ($role) {
            $employee = $item['employee'];

            return match (strtolower($role)) {
                'chef' => [
                    'name' => $employee->user?->name ?? '',
                    'email' => $employee->user?->email ?? '',
                    'total_orders_prepared' => $item['total_orders_prepared'] ?? 0,
                    'completed_orders' => $item['completed_orders'] ?? 0,
                    'total_items_prepared' => $item['total_items_prepared'] ?? 0,
                ],
                'waiter' => [
                    'name' => $employee->user?->name ?? '',
                    'email' => $employee->user?->email ?? '',
                    'total_orders_handled' => $item['total_orders_handled'] ?? 0,
                    'delivered_orders' => $item['delivered_orders'] ?? 0,
                    'total_sales' => $item['total_sales'] ?? 0,
                    'average_order_value' => $item['average_order_value'] ?? 0,
                ],
                'cashier' => [
                    'name' => $employee->user?->name ?? '',
                    'email' => $employee->user?->email ?? '',
                    'total_payments_processed' => $item['total_payments_processed'] ?? 0,
                    'total_amount_collected' => $item['total_amount_collected'] ?? 0,
                    'average_transaction_value' => $item['average_transaction_value'] ?? 0,
                ],
                default => [
                    'name' => $employee->user?->name ?? '',
                    'email' => $employee->user?->email ?? '',
                    'score' => $item['score'] ?? 0,
                ],
            };
        });

        return $this->exportToCsv($data, $headers, strtolower($role).'_performance_report.csv');
    }

    /**
     * Export purchase report to CSV.
     */
    public function exportPurchaseReportToCsv(Collection $purchases): string
    {
        $headers = ['Purchase Number', 'Supplier', 'Total Amount', 'Status', 'Items Count', 'Created At', 'Created By'];

        $data = $purchases->map(function ($purchase) {
            return [
                'purchase_number' => $purchase['purchase_number'] ?? $purchase->purchase_number ?? '',
                'supplier' => $purchase['supplier'] ?? $purchase->supplier?->name ?? '',
                'total_amount' => $purchase['total_amount'] ?? $purchase->total_amount ?? 0,
                'status' => $purchase['status'] ?? $purchase->status ?? '',
                'items_count' => $purchase['items_count'] ?? $purchase->items->count() ?? 0,
                'created_at' => ($purchase['created_at'] ?? $purchase->created_at)?->format('Y-m-d H:i:s') ?? '',
                'created_by' => $purchase['created_by'] ?? $purchase->creator?->name ?? '',
            ];
        });

        return $this->exportToCsv($data, $headers, 'purchase_report.csv');
    }

    /**
     * Prepare data for PDF export.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function preparePdfData(array $data, string $template): array
    {
        return [
            'data' => $data,
            'template' => $template,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'note' => 'PDF export requires dompdf or similar package to be installed.',
        ];
    }

    /**
     * Prepare data for Excel export.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function prepareExcelData(array $data, string $sheetName): array
    {
        return [
            'data' => $data,
            'sheet_name' => $sheetName,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'note' => 'Excel export requires maatwebsite/excel package to be installed.',
        ];
    }
}
