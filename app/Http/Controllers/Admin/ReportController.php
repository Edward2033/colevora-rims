<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportExportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ReportExportService $exportService
    ) {}

    /**
     * Export report as CSV
     */
    public function export(Request $request, string $type)
    {
        $filename = $type.'_report_'.now()->format('Y-m-d').'.csv';

        $content = match ($type) {
            'sales' => $this->exportService->exportSalesReport(),
            'orders' => $this->exportService->exportOrdersReport(),
            'inventory' => $this->exportService->exportInventoryReport(),
            'customers' => $this->exportService->exportCustomersReport(),
            'employees' => $this->exportService->exportEmployeePerformance(),
            default => abort(404, 'Report type not found'),
        };

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
