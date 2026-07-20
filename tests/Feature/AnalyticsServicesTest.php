<?php

use App\Models\Employee;
use App\Models\Food;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\StockTransaction;
use App\Models\User;
use App\Services\CustomerAnalyticsService;
use App\Services\DashboardService;
use App\Services\EmployeePerformanceService;
use App\Services\InventoryReportService;
use App\Services\OrderAnalyticsService;
use App\Services\ReportExportService;
use App\Services\SalesAnalyticsService;
use Illuminate\Support\Collection;

beforeEach(function () {
    // Skip user creation in beforeEach - create users as needed per test
});

test('dashboard service returns correct statistics', function () {
    $service = new DashboardService;

    // Create test data
    $order = Order::factory()->create(['status' => 'completed', 'total_amount' => 100]);
    Payment::factory()->create([
        'order_id' => $order->id,
        'amount' => 100,
        'status' => 'completed',
        'paid_at' => now(),
    ]);

    $stats = $service->getStatistics();

    expect($stats)->toHaveKeys(['sales', 'orders', 'customers', 'employees', 'low_stock_items']);
    expect($stats['sales'])->toHaveKeys(['total_sales', 'today_sales', 'monthly_sales']);
    expect($stats['orders'])->toHaveKeys(['total_orders', 'pending_orders', 'completed_orders']);
});

test('dashboard service calculates sales correctly', function () {
    $service = new DashboardService;

    // Create completed payment
    $order = Order::factory()->create(['status' => 'completed', 'total_amount' => 150]);
    Payment::factory()->create([
        'order_id' => $order->id,
        'amount' => 150,
        'status' => 'completed',
        'paid_at' => now(),
    ]);

    $totalSales = $service->getTotalSales();
    $todaySales = $service->getTodaySales();

    expect($totalSales)->toBeGreaterThanOrEqual(150);
    expect($todaySales)->toBeGreaterThanOrEqual(150);
});

test('sales analytics service returns daily sales chart', function () {
    $service = new SalesAnalyticsService;

    $order = Order::factory()->create(['status' => 'completed', 'total_amount' => 200]);
    Payment::factory()->create([
        'order_id' => $order->id,
        'amount' => 200,
        'status' => 'completed',
        'paid_at' => now(),
    ]);

    $dailySales = $service->getDailySales();

    expect($dailySales)->toBeArray();
    expect($dailySales)->toHaveKey('data');
});

test('sales analytics service returns monthly revenue chart', function () {
    $service = new SalesAnalyticsService;

    $order = Order::factory()->create(['status' => 'completed', 'total_amount' => 300]);
    Payment::factory()->create([
        'order_id' => $order->id,
        'amount' => 300,
        'status' => 'completed',
        'paid_at' => now(),
    ]);

    $monthlyRevenue = $service->getMonthlyRevenue();

    expect($monthlyRevenue)->toBeArray();
    expect($monthlyRevenue)->toHaveKey('data');
});

test('sales analytics service returns sales by payment method', function () {
    $service = new SalesAnalyticsService;

    $order = Order::factory()->create(['status' => 'completed', 'total_amount' => 100]);
    Payment::factory()->create([
        'order_id' => $order->id,
        'amount' => 100,
        'status' => 'completed',
        'paid_at' => now(),
        'payment_method' => 'cash',
    ]);

    $byPaymentMethod = $service->getSalesByPaymentMethod();

    expect($byPaymentMethod)->toBeArray();
    expect($byPaymentMethod)->toHaveKey('data');
});

test('order analytics service returns orders by status', function () {
    $service = new OrderAnalyticsService;

    Order::factory()->create(['status' => 'pending']);
    Order::factory()->create(['status' => 'completed']);

    $ordersByStatus = $service->getOrdersByStatus();

    expect($ordersByStatus)->toBeArray();
    expect($ordersByStatus)->toHaveKey('data');
});

test('order analytics service returns popular food items', function () {
    $service = new OrderAnalyticsService;

    $food = Food::factory()->create();
    $order = Order::factory()->create();
    OrderItem::factory()->create([
        'order_id' => $order->id,
        'food_id' => $food->id,
        'quantity' => 5,
    ]);

    $popularFoods = $service->getPopularFoodItems(10);

    expect($popularFoods)->toBeArray();
});

test('customer analytics service returns new customers', function () {
    $service = new CustomerAnalyticsService;

    User::factory()->count(3)->create(['user_type' => 'customer']);

    $newCustomers = $service->getNewCustomers();

    expect($newCustomers)->toHaveKey('total');
    expect($newCustomers)->toHaveKey('customers');
    expect($newCustomers['total'])->toBeGreaterThanOrEqual(3);
});

test('customer analytics service returns returning customers', function () {
    $service = new CustomerAnalyticsService;

    $customer = User::factory()->create(['user_type' => 'customer']);
    Order::factory()->count(3)->create(['customer_id' => $customer->id]);

    $returningCustomers = $service->getReturningCustomers();

    expect($returningCustomers)->toHaveKey('total');
    expect($returningCustomers)->toHaveKey('customers');
});

test('customer analytics service returns customer order history', function () {
    $service = new CustomerAnalyticsService;

    $customer = User::factory()->create(['user_type' => 'customer']);
    Order::factory()->count(2)->create([
        'customer_id' => $customer->id,
        'total_amount' => 100,
    ]);

    $history = $service->getCustomerOrderHistory($customer->id);

    expect($history)->toHaveKey('customer');
    expect($history)->toHaveKey('total_orders');
    expect($history)->toHaveKey('total_spent');
    expect($history['total_orders'])->toBe(2);
});

test('customer analytics service returns top customers by order count', function () {
    $service = new CustomerAnalyticsService;

    $customer = User::factory()->create(['user_type' => 'customer']);
    Order::factory()->count(5)->create(['customer_id' => $customer->id]);

    $topCustomers = $service->getTopCustomersByOrderCount(10);

    expect($topCustomers)->toBeInstanceOf(Collection::class);
});

test('customer analytics service returns customer statistics', function () {
    $service = new CustomerAnalyticsService;

    User::factory()->count(5)->create(['user_type' => 'customer']);

    $stats = $service->getCustomerStatistics();

    expect($stats)->toHaveKey('total_customers');
    expect($stats)->toHaveKey('customers_with_orders');
    expect($stats)->toHaveKey('new_customers_today');
});

test('employee performance service returns chef performance', function () {
    $service = new EmployeePerformanceService;

    $employee = Employee::factory()->create(['job_title' => 'Chef']);
    $order = Order::factory()->create(['status' => 'completed']);
    OrderAssignment::factory()->create([
        'employee_id' => $employee->id,
        'order_id' => $order->id,
    ]);

    $performance = $service->getChefPerformance($employee->id);

    expect($performance)->toHaveKey('employee');
    expect($performance)->toHaveKey('total_orders_prepared');
    expect($performance['total_orders_prepared'])->toBeGreaterThanOrEqual(1);
});

test('employee performance service returns waiter performance', function () {
    $service = new EmployeePerformanceService;

    $employee = Employee::factory()->create(['job_title' => 'Waiter']);
    Order::factory()->count(2)->create([
        'assigned_waiter_id' => $employee->id,
        'status' => 'served',
        'total_amount' => 100,
    ]);

    $performance = $service->getWaiterPerformance($employee->id);

    expect($performance)->toHaveKey('employee');
    expect($performance)->toHaveKey('total_orders_handled');
    expect($performance)->toHaveKey('delivered_orders');
    expect($performance)->toHaveKey('total_sales');
});

test('employee performance service returns cashier performance', function () {
    $service = new EmployeePerformanceService;

    $employee = Employee::factory()->create(['job_title' => 'Cashier']);
    $order = Order::factory()->create(['total_amount' => 150]);
    Payment::factory()->create([
        'order_id' => $order->id,
        'amount' => 150,
        'status' => 'completed',
        'paid_by' => $employee->user_id,
        'paid_at' => now(),
    ]);

    $performance = $service->getCashierPerformance($employee->id);

    expect($performance)->toHaveKey('employee');
    expect($performance)->toHaveKey('total_payments_processed');
    expect($performance)->toHaveKey('total_amount_collected');
    expect($performance['total_payments_processed'])->toBeGreaterThanOrEqual(1);
});

test('inventory report service returns current stock report', function () {
    $service = new InventoryReportService;

    InventoryItem::factory()->count(3)->create(['status' => 'active']);

    $report = $service->getCurrentStockReport();

    expect($report)->toHaveKey('total_items');
    expect($report)->toHaveKey('total_value');
    expect($report)->toHaveKey('items');
    expect($report['total_items'])->toBeGreaterThanOrEqual(3);
});

test('inventory report service returns low stock report', function () {
    $service = new InventoryReportService;

    InventoryItem::factory()->create([
        'quantity' => 5,
        'minimum_quantity' => 10,
    ]);

    $report = $service->getLowStockReport();

    expect($report)->toHaveKey('total_low_stock_items');
    expect($report)->toHaveKey('items');
    expect($report['total_low_stock_items'])->toBeGreaterThanOrEqual(1);
});

test('inventory report service returns ingredient usage report', function () {
    $service = new InventoryReportService;

    $user = User::factory()->create();
    $item = InventoryItem::factory()->create();
    StockTransaction::factory()->create([
        'inventory_item_id' => $item->id,
        'type' => 'usage',
        'quantity' => -10,
        'created_by' => $user->id,
    ]);

    $report = $service->getIngredientUsageReport();

    expect($report)->toHaveKey('total_transactions');
    expect($report)->toHaveKey('usage_by_item');
});

test('inventory report service returns purchase report', function () {
    $service = new InventoryReportService;

    $user = User::factory()->create();
    Purchase::factory()->count(2)->create([
        'total_amount' => 500,
        'created_by' => $user->id,
    ]);

    $report = $service->getPurchaseReport();

    expect($report)->toHaveKey('total_purchases');
    expect($report)->toHaveKey('total_amount');
    expect($report)->toHaveKey('by_supplier');
    expect($report['total_purchases'])->toBeGreaterThanOrEqual(2);
});

test('inventory report service returns stock movement report', function () {
    $service = new InventoryReportService;

    $user = User::factory()->create();
    $item = InventoryItem::factory()->create();
    StockTransaction::factory()->count(3)->create([
        'inventory_item_id' => $item->id,
        'created_by' => $user->id,
    ]);

    $report = $service->getStockMovementReport();

    expect($report)->toHaveKey('total_transactions');
    expect($report)->toHaveKey('by_type');
    expect($report['total_transactions'])->toBeGreaterThanOrEqual(3);
});

test('inventory report service returns inventory valuation report', function () {
    $service = new InventoryReportService;

    InventoryItem::factory()->count(3)->create([
        'status' => 'active',
        'quantity' => 10,
        'cost_price' => 50,
    ]);

    $report = $service->getInventoryValuationReport();

    expect($report)->toHaveKey('total_inventory_value');
    expect($report)->toHaveKey('total_items');
    expect($report)->toHaveKey('by_category');
});

test('report export service exports to csv', function () {
    $service = new ReportExportService;

    $data = collect([
        ['name' => 'Item 1', 'quantity' => 10],
        ['name' => 'Item 2', 'quantity' => 20],
    ]);

    $headers = ['Name', 'Quantity'];
    $csv = $service->exportToCsv($data, $headers);

    expect($csv)->toBeString();
    expect($csv)->toContain('Name,Quantity');
});

test('report export service exports sales report to csv', function () {
    $service = new ReportExportService;

    $customer = User::factory()->create(['user_type' => 'customer']);
    $order = Order::factory()->create([
        'order_number' => 'ORD-123',
        'customer_id' => $customer->id,
        'total_amount' => 100,
    ]);

    $sales = collect([$order]);
    $csv = $service->exportSalesReportToCsv($sales);

    expect($csv)->toBeString();
    expect($csv)->toContain('Order Number');
});

test('report export service exports inventory report to csv', function () {
    $service = new ReportExportService;

    $items = collect([
        [
            'name' => 'Test Item',
            'category' => 'Test Category',
            'supplier' => 'Test Supplier',
            'quantity' => 10,
            'unit' => 'kg',
            'cost_price' => 50,
            'total_value' => 500,
            'status' => 'Normal',
        ],
    ]);

    $csv = $service->exportInventoryReportToCsv($items);

    expect($csv)->toBeString();
    expect($csv)->toContain('Item Name');
});

test('report export service exports customer report to csv', function () {
    $service = new ReportExportService;

    $customer = User::factory()->create([
        'user_type' => 'customer',
        'name' => 'Test Customer',
    ]);

    $customers = collect([$customer]);
    $csv = $service->exportCustomerReportToCsv($customers);

    expect($csv)->toBeString();
    expect($csv)->toContain('Name,Email');
});
