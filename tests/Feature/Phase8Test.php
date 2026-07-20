<?php

use App\Models\AuditLog;
use App\Models\HeroSlide;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Page;
use App\Models\Purchase;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\Supplier;
use App\Models\User;
use App\Notifications\LowStockAlert;
use App\Notifications\NewOrderPlaced;
use App\Notifications\OrderStatusChanged;
use App\Notifications\ReservationStatusChanged;

beforeEach(function () {
    $this->admin = User::factory()->create(['user_type' => 'admin', 'account_status' => 'active']);
    // Assign Administrator role so middleware passes
    $role = Role::firstOrCreate(['name' => 'Administrator'], ['description' => 'Administrator']);
    $this->admin->roles()->syncWithoutDetaching([$role->id]);
    $this->actingAs($this->admin);
});

// ── Admin CRUD Routes ──────────────────────────────────────────────────────────

it('admin users index is accessible', function () {
    $response = $this->get(route('admin.users.index'));
    $response->assertStatus(200);
});

it('admin employees index is accessible', function () {
    $response = $this->get(route('admin.employees.index'));
    $response->assertStatus(200);
});

it('admin roles index is accessible', function () {
    $response = $this->get(route('admin.roles.index'));
    $response->assertStatus(200);
});

it('admin suppliers index is accessible', function () {
    $response = $this->get(route('admin.suppliers.index'));
    $response->assertStatus(200);
});

it('admin inventory items index is accessible', function () {
    $response = $this->get(route('admin.inventory.items.index'));
    $response->assertStatus(200);
});

it('admin purchases index is accessible', function () {
    $response = $this->get(route('admin.purchases.index'));
    $response->assertStatus(200);
});

it('admin tables index is accessible', function () {
    $response = $this->get(route('admin.tables.index'));
    $response->assertStatus(200);
});

it('admin reservations index is accessible', function () {
    $response = $this->get(route('admin.reservations.index'));
    $response->assertStatus(200);
});

it('admin payments index is accessible', function () {
    $response = $this->get(route('admin.payments.index'));
    $response->assertStatus(200);
});

it('admin audit logs index is accessible', function () {
    $response = $this->get(route('admin.audit-logs.index'));
    $response->assertStatus(200);
});

it('admin cms hero slides index is accessible', function () {
    $response = $this->get(route('admin.cms.hero-slides.index'));
    $response->assertStatus(200);
});

it('admin cms pages index is accessible', function () {
    $response = $this->get(route('admin.cms.pages.index'));
    $response->assertStatus(200);
});

it('admin cms settings index is accessible', function () {
    $response = $this->get(route('admin.cms.settings.index'));
    $response->assertStatus(200);
});

// ── Create Routes ──────────────────────────────────────────────────────────────

it('admin users create page is accessible', function () {
    $response = $this->get(route('admin.users.create'));
    $response->assertStatus(200);
});

it('admin suppliers create page is accessible', function () {
    $response = $this->get(route('admin.suppliers.create'));
    $response->assertStatus(200);
});

it('admin inventory items create page is accessible', function () {
    $response = $this->get(route('admin.inventory.items.create'));
    $response->assertStatus(200);
});

it('admin tables create page is accessible', function () {
    $response = $this->get(route('admin.tables.create'));
    $response->assertStatus(200);
});

it('admin purchases create page is accessible', function () {
    $response = $this->get(route('admin.purchases.create'));
    $response->assertStatus(200);
});

it('admin hero slides create page is accessible', function () {
    $response = $this->get(route('admin.cms.hero-slides.create'));
    $response->assertStatus(200);
});

it('admin cms pages create page is accessible', function () {
    $response = $this->get(route('admin.cms.pages.create'));
    $response->assertStatus(200);
});

// ── Show/Edit Routes ───────────────────────────────────────────────────────────

it('admin user show page is accessible', function () {
    $user = User::factory()->create();
    $response = $this->get(route('admin.users.show', $user));
    $response->assertStatus(200);
});

it('admin user edit page is accessible', function () {
    $user = User::factory()->create();
    $response = $this->get(route('admin.users.edit', $user));
    $response->assertStatus(200);
});

it('admin supplier show page is accessible', function () {
    $supplier = Supplier::factory()->create();
    $response = $this->get(route('admin.suppliers.show', $supplier));
    $response->assertStatus(200);
});

it('admin supplier edit page is accessible', function () {
    $supplier = Supplier::factory()->create();
    $response = $this->get(route('admin.suppliers.edit', $supplier));
    $response->assertStatus(200);
});

it('admin inventory item show page is accessible', function () {
    $cat = InventoryCategory::factory()->create();
    $item = InventoryItem::factory()->create(['category_id' => $cat->id]);
    $response = $this->get(route('admin.inventory.items.show', $item));
    $response->assertStatus(200);
});

it('admin table show page is accessible', function () {
    $table = RestaurantTable::factory()->create();
    $response = $this->get(route('admin.tables.show', $table));
    $response->assertStatus(200);
});

it('admin purchase show page is accessible', function () {
    $supplier = Supplier::factory()->create();
    $purchase = Purchase::factory()->create(['supplier_id' => $supplier->id, 'created_by' => $this->admin->id]);
    $response = $this->get(route('admin.purchases.show', $purchase));
    $response->assertStatus(200);
});

it('admin hero slide show page is accessible', function () {
    $slide = HeroSlide::factory()->create();
    $response = $this->get(route('admin.cms.hero-slides.show', $slide));
    $response->assertStatus(200);
});

it('admin cms page show is accessible', function () {
    $page = Page::factory()->create();
    $response = $this->get(route('admin.cms.pages.show', $page));
    $response->assertStatus(200);
});

// ── Notifications ──────────────────────────────────────────────────────────────

it('order status changed notification can be created', function () {
    $customer = User::factory()->create(['user_type' => 'customer']);
    $order = Order::factory()->create(['customer_id' => $customer->id]);

    $notification = new OrderStatusChanged($order, 'pending');
    $data = $notification->toArray($customer);

    expect($data)->toHaveKey('title')
        ->and($data)->toHaveKey('message')
        ->and($data)->toHaveKey('type')
        ->and($data['type'])->toBe('order_status');
});

it('low stock alert notification can be created', function () {
    $cat = InventoryCategory::factory()->create();
    $item = InventoryItem::factory()->create(['category_id' => $cat->id]);

    $notification = new LowStockAlert($item);
    $data = $notification->toArray($this->admin);

    expect($data)->toHaveKey('title')
        ->and($data['type'])->toBe('low_stock')
        ->and($data['item_id'])->toBe($item->id);
});

it('reservation status changed notification can be created', function () {
    $customer = User::factory()->create(['user_type' => 'customer']);
    $reservation = Reservation::factory()->create(['user_id' => $customer->id]);

    $notification = new ReservationStatusChanged($reservation);
    $data = $notification->toArray($customer);

    expect($data)->toHaveKey('title')
        ->and($data['type'])->toBe('reservation')
        ->and($data['reservation_id'])->toBe($reservation->id);
});

it('new order placed notification can be created', function () {
    $customer = User::factory()->create(['user_type' => 'customer']);
    $order = Order::factory()->create(['customer_id' => $customer->id]);

    $notification = new NewOrderPlaced($order);
    $data = $notification->toArray($this->admin);

    expect($data)->toHaveKey('title')
        ->and($data['type'])->toBe('new_order')
        ->and($data['order_id'])->toBe($order->id);
});

it('user can receive database notifications', function () {
    $customer = User::factory()->create(['user_type' => 'customer']);
    $order = Order::factory()->create(['customer_id' => $customer->id]);

    $customer->notify(new OrderStatusChanged($order, 'pending'));

    expect($customer->notifications()->count())->toBe(1);
    expect($customer->unreadNotifications()->count())->toBe(1);
});

it('user can mark notifications as read', function () {
    $customer = User::factory()->create(['user_type' => 'customer']);
    $order = Order::factory()->create(['customer_id' => $customer->id]);

    $customer->notify(new OrderStatusChanged($order, 'pending'));
    $customer->unreadNotifications->markAsRead();

    expect($customer->unreadNotifications()->count())->toBe(0);
    expect($customer->readNotifications()->count())->toBe(1);
});

// ── Site Settings ──────────────────────────────────────────────────────────────

it('site settings can be set and retrieved', function () {
    SiteSetting::set('restaurant_name', 'Colevora Test');
    expect(SiteSetting::get('restaurant_name'))->toBe('Colevora Test');
});

it('site settings cache is cleared on update', function () {
    SiteSetting::set('currency_symbol', '$');
    SiteSetting::set('currency_symbol', '€');
    expect(SiteSetting::get('currency_symbol'))->toBe('€');
});

// ── Reservation Model ──────────────────────────────────────────────────────────

it('reservation can be created', function () {
    $table = RestaurantTable::factory()->create();
    $reservation = Reservation::factory()->create([
        'table_id' => $table->id,
        'status' => 'pending',
    ]);

    expect($reservation->status)->toBe('pending');
    expect($reservation->table->id)->toBe($table->id);
});

it('pending reservations can be filtered', function () {
    Reservation::factory()->create(['status' => 'pending']);
    Reservation::factory()->create(['status' => 'confirmed']);

    expect(Reservation::pending()->count())->toBe(1);
});

// ── AuditLog ───────────────────────────────────────────────────────────────────

it('audit log belongs to user', function () {
    $log = AuditLog::factory()->create(['user_id' => $this->admin->id]);
    expect($log->user->id)->toBe($this->admin->id);
});

it('audit log can be created without user', function () {
    $log = AuditLog::factory()->create(['user_id' => null]);
    expect($log->user)->toBeNull();
});
