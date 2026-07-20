<?php

use App\Http\Controllers\Admin\MarkNotificationsReadController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CartCountController;
use App\Http\Controllers\DashboardRedirectController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Public Routes
Volt::route('/', 'public.home')->name('home');
Volt::route('/menu', 'public.menu')->name('menu');
Volt::route('/menu/{id}', 'public.food-details')->name('food.show');
Volt::route('/about', 'public.about')->name('about');
Volt::route('/gallery', 'public.gallery')->name('gallery');
Volt::route('/contact', 'public.contact')->name('contact');
Volt::route('/reservation', 'public.reservation')->name('reservation');
Volt::route('/search', 'public.search')->name('search');
Volt::route('/testimonials', 'public.testimonials')->name('testimonials');

// Cart (accessible to guests)
Volt::route('/cart', 'public.cart')->name('cart.index');

// Checkout (Auth Required)
Route::middleware(['auth'])->group(function () {
    Volt::route('/checkout', 'public.checkout')->name('checkout');
});

// Customer Portal Routes
Route::middleware(['auth', 'customer'])->prefix('customer')->name('customer.')->group(function () {
    Volt::route('/dashboard', 'customer.dashboard')->name('dashboard');
    Volt::route('/orders', 'customer.orders')->name('orders');
    Volt::route('/orders/{id}', 'customer.order-tracking')->name('order.track');
    Volt::route('/notifications', 'customer.notifications')->name('notifications');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Volt::route('/dashboard', 'admin.dashboard')->name('dashboard');

    // Notifications
    Route::post('/notifications/mark-all-read', MarkNotificationsReadController::class)
        ->name('notifications.mark-all-read');

    // Users (Volt + Controller for mutations)
    Volt::route('/users', 'admin.users.index')->name('users.index');
    Volt::route('/users/create', 'admin.users.create')->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Volt::route('/users/{id}', 'admin.users.show')->name('users.show');
    Volt::route('/users/{id}/edit', 'admin.users.edit')->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Employees (Volt)
    Volt::route('/employees', 'admin.employees.index')->name('employees.index');
    Volt::route('/employees/create', 'admin.employees.create')->name('employees.create');
    Volt::route('/employees/{id}', 'admin.employees.show')->name('employees.show');
    Volt::route('/employees/{id}/edit', 'admin.employees.edit')->name('employees.edit');

    // Roles (Volt)
    Volt::route('/roles', 'admin.roles.index')->name('roles.index');
    Volt::route('/roles/create', 'admin.roles.create')->name('roles.create');
    Volt::route('/roles/{id}', 'admin.roles.show')->name('roles.show');
    Volt::route('/roles/{id}/edit', 'admin.roles.edit')->name('roles.edit');

    // Categories (Volt)
    Volt::route('/categories', 'admin.categories.index')->name('categories.index');
    Volt::route('/categories/create', 'admin.categories.create')->name('categories.create');
    Volt::route('/categories/{id}', 'admin.categories.show')->name('categories.show');
    Volt::route('/categories/{id}/edit', 'admin.categories.edit')->name('categories.edit');

    // Foods (Volt)
    Volt::route('/foods', 'admin.foods.index')->name('foods.index');
    Volt::route('/foods/create', 'admin.foods.create')->name('foods.create');
    Volt::route('/foods/{id}', 'admin.foods.show')->name('foods.show');
    Volt::route('/foods/{id}/edit', 'admin.foods.edit')->name('foods.edit');

    // Orders (Volt - view only)
    Volt::route('/orders', 'admin.orders.index')->name('orders.index');
    Volt::route('/orders/create', 'admin.orders.create')->name('orders.create');
    Volt::route('/orders/{id}', 'admin.orders.show')->name('orders.show');
    Volt::route('/orders/{id}/edit', 'admin.orders.edit')->name('orders.edit');

    // Inventory routes (Volt)
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Volt::route('/items', 'admin.inventory.items.index')->name('items.index');
        Volt::route('/items/create', 'admin.inventory.items.create')->name('items.create');
        Volt::route('/items/{id}', 'admin.inventory.items.show')->name('items.show');
        Volt::route('/items/{id}/edit', 'admin.inventory.items.edit')->name('items.edit');
    });

    // Suppliers (Volt + Controller for mutations)
    Volt::route('/suppliers', 'admin.suppliers.index')->name('suppliers.index');
    Volt::route('/suppliers/create', 'admin.suppliers.create')->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Volt::route('/suppliers/{id}', 'admin.suppliers.show')->name('suppliers.show');
    Volt::route('/suppliers/{id}/edit', 'admin.suppliers.edit')->name('suppliers.edit');
    Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    // Purchases (Volt + Controller for mutations)
    Volt::route('/purchases', 'admin.purchases.index')->name('purchases.index');
    Volt::route('/purchases/create', 'admin.purchases.create')->name('purchases.create');
    Volt::route('/purchases/{id}', 'admin.purchases.show')->name('purchases.show');
    Volt::route('/purchases/{id}/edit', 'admin.purchases.edit')->name('purchases.edit');
    Route::put('/purchases/{id}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');

    // Tables (Volt)
    Volt::route('/tables', 'admin.tables.index')->name('tables.index');
    Volt::route('/tables/create', 'admin.tables.create')->name('tables.create');
    Volt::route('/tables/{id}', 'admin.tables.show')->name('tables.show');
    Volt::route('/tables/{id}/edit', 'admin.tables.edit')->name('tables.edit');

    // CMS routes (Volt)
    Route::prefix('cms')->name('cms.')->group(function () {
        Volt::route('/hero-slides', 'admin.cms.hero-slides.index')->name('hero-slides.index');
        Volt::route('/hero-slides/create', 'admin.cms.hero-slides.create')->name('hero-slides.create');
        Volt::route('/hero-slides/{id}', 'admin.cms.hero-slides.show')->name('hero-slides.show');
        Volt::route('/hero-slides/{id}/edit', 'admin.cms.hero-slides.edit')->name('hero-slides.edit');

        Volt::route('/pages', 'admin.cms.pages.index')->name('pages.index');
        Volt::route('/pages/create', 'admin.cms.pages.create')->name('pages.create');
        Volt::route('/pages/{id}', 'admin.cms.pages.show')->name('pages.show');
        Volt::route('/pages/{id}/edit', 'admin.cms.pages.edit')->name('pages.edit');

        Volt::route('/settings', 'admin.cms.settings.index')->name('settings.index');
        Volt::route('/settings/{id}/edit', 'admin.cms.settings.edit')->name('settings.edit');
    });

    // Reservations
    Volt::route('/reservations', 'admin.reservations.index')->name('reservations.index');
    Volt::route('/reservations/{id}', 'admin.reservations.show')->name('reservations.show');
    Volt::route('/reservations/{id}/edit', 'admin.reservations.edit')->name('reservations.edit');

    // Payments
    Volt::route('/payments', 'admin.payments.index')->name('payments.index');
    Volt::route('/payments/{id}', 'admin.payments.show')->name('payments.show');

    // Audit Logs
    Volt::route('/audit-logs', 'admin.audit-logs.index')->name('audit-logs.index');

    // Reports
    Volt::route('/reports', 'admin.reports')->name('reports.index');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
});

// Employee Routes
Route::middleware(['auth', 'employee'])->prefix('employee')->name('employee.')->group(function () {
    Volt::route('/chef', 'employee.chef-dashboard')->name('chef.dashboard');
    Volt::route('/waiter', 'employee.waiter-dashboard')->name('waiter.dashboard');
    Volt::route('/cashier', 'employee.cashier-dashboard')->name('cashier.dashboard');
    Volt::route('/inventory-officer', 'employee.inventory-officer-dashboard')->name('inventory-officer.dashboard');
    Volt::route('/receptionist', 'employee.receptionist-dashboard')->name('receptionist.dashboard');
});

// Cart API endpoint for count
Route::get('/api/cart/count', CartCountController::class)->name('api.cart.count');

// Settings Routes (Authenticated Users)
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', \App\Http\Controllers\DashboardRedirectController::class)
        ->middleware(['verified'])
        ->name('dashboard');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
