<?php

use App\Models\Category;
use App\Models\Food;
use App\Models\FoodIngredient;
use App\Models\InventoryAlert;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\InventoryCategorySeeder;

// Supplier Management Tests
test('supplier can be created', function () {
    $user = User::factory()->create();
    $supplier = Supplier::factory()->create(['created_by' => $user->id]);

    expect($supplier->name)->not->toBeEmpty()
        ->and($supplier->creator->id)->toBe($user->id);
});

test('active suppliers can be filtered', function () {
    Supplier::factory()->create(['status' => 'active']);
    Supplier::factory()->create(['status' => 'inactive']);
    Supplier::factory()->create(['status' => 'active']);

    $active = Supplier::active()->get();

    expect($active)->toHaveCount(2);
});

test('supplier has inventory items relationship', function () {
    $supplier = Supplier::factory()->create();
    InventoryItem::factory()->count(3)->create(['supplier_id' => $supplier->id]);

    expect($supplier->inventoryItems)->toHaveCount(3);
});

// Inventory Category Tests
test('inventory categories are seeded correctly', function () {
    $this->seed([InventoryCategorySeeder::class]);

    expect(InventoryCategory::count())->toBe(8)
        ->and(InventoryCategory::where('name', 'Vegetables')->exists())->toBeTrue()
        ->and(InventoryCategory::where('name', 'Meat & Poultry')->exists())->toBeTrue();
});

test('inventory category has items relationship', function () {
    $category = InventoryCategory::factory()->create();
    InventoryItem::factory()->count(5)->create(['category_id' => $category->id]);

    expect($category->inventoryItems)->toHaveCount(5);
});

// Inventory Item Tests
test('inventory item can be created', function () {
    $category = InventoryCategory::factory()->create();
    $supplier = Supplier::factory()->create();

    $item = InventoryItem::factory()->create([
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'name' => 'Tomatoes',
        'unit' => 'kg',
        'quantity' => 50,
        'minimum_quantity' => 10,
    ]);

    expect($item->name)->toBe('Tomatoes')
        ->and($item->category->id)->toBe($category->id)
        ->and($item->supplier->id)->toBe($supplier->id);
});

test('inventory item detects low stock', function () {
    $item = InventoryItem::factory()->create([
        'quantity' => 5,
        'minimum_quantity' => 10,
    ]);

    expect($item->isLowStock())->toBeTrue();
});

test('inventory item detects sufficient stock', function () {
    $item = InventoryItem::factory()->create([
        'quantity' => 50,
        'minimum_quantity' => 10,
    ]);

    expect($item->isLowStock())->toBeFalse();
});

test('low stock items can be filtered', function () {
    InventoryItem::factory()->create(['quantity' => 5, 'minimum_quantity' => 10]);
    InventoryItem::factory()->create(['quantity' => 50, 'minimum_quantity' => 10]);
    InventoryItem::factory()->create(['quantity' => 3, 'minimum_quantity' => 10]);

    $lowStock = InventoryItem::lowStock()->get();

    expect($lowStock)->toHaveCount(2);
});

// Stock Transaction Tests
test('inventory item can add stock', function () {
    $item = InventoryItem::factory()->create(['quantity' => 10]);
    $user = User::factory()->create();

    $item->addStock(20, $user, 'purchase', 'New stock arrival');

    expect((float) $item->fresh()->quantity)->toBe(30.0)
        ->and($item->stockTransactions)->toHaveCount(1)
        ->and($item->stockTransactions->first()->type)->toBe('purchase');
});

test('inventory item can reduce stock', function () {
    $item = InventoryItem::factory()->create(['quantity' => 50]);
    $user = User::factory()->create();

    $item->reduceStock(20, $user, 'usage', 'Used for cooking');

    expect((float) $item->fresh()->quantity)->toBe(30.0)
        ->and($item->stockTransactions)->toHaveCount(1)
        ->and((float) $item->stockTransactions->first()->quantity)->toBe(-20.0);
});

test('stock transaction records reference', function () {
    $item = InventoryItem::factory()->create(['quantity' => 50]);
    $user = User::factory()->create();
    $order = Order::factory()->create();

    $item->reduceStock(10, $user, 'usage', 'Order usage', Order::class, $order->id);

    $transaction = $item->stockTransactions->first();
    expect($transaction->reference_type)->toBe(Order::class)
        ->and($transaction->reference_id)->toBe($order->id);
});

// Purchase Management Tests
test('purchase is created with auto-generated number', function () {
    $purchase = Purchase::factory()->create(['purchase_number' => '']);

    expect($purchase->purchase_number)->not->toBeEmpty()
        ->and($purchase->purchase_number)->toStartWith('PO-');
});

test('purchase has supplier and items relationships', function () {
    $supplier = Supplier::factory()->create();
    $purchase = Purchase::factory()->create(['supplier_id' => $supplier->id]);
    PurchaseItem::factory()->count(3)->create(['purchase_id' => $purchase->id]);

    expect($purchase->supplier->id)->toBe($supplier->id)
        ->and($purchase->items)->toHaveCount(3);
});

test('purchase can be marked received and updates inventory', function () {
    $supplier = Supplier::factory()->create();
    $item1 = InventoryItem::factory()->create(['quantity' => 10]);
    $item2 = InventoryItem::factory()->create(['quantity' => 20]);
    $user = User::factory()->create();

    $purchase = Purchase::factory()->create([
        'supplier_id' => $supplier->id,
        'status' => 'pending',
    ]);

    PurchaseItem::factory()->create([
        'purchase_id' => $purchase->id,
        'inventory_item_id' => $item1->id,
        'quantity' => 50,
    ]);

    PurchaseItem::factory()->create([
        'purchase_id' => $purchase->id,
        'inventory_item_id' => $item2->id,
        'quantity' => 30,
    ]);

    $purchase->markReceived($user);

    expect($purchase->fresh()->status)->toBe('received')
        ->and((float) $item1->fresh()->quantity)->toBe(60.0)
        ->and((float) $item2->fresh()->quantity)->toBe(50.0);
});

test('pending purchases can be filtered', function () {
    Purchase::factory()->create(['status' => 'pending']);
    Purchase::factory()->create(['status' => 'completed']);
    Purchase::factory()->create(['status' => 'pending']);

    $pending = Purchase::pending()->get();

    expect($pending)->toHaveCount(2);
});

// Food Ingredient System Tests
test('food can have ingredients', function () {
    $category = Category::factory()->create();
    $food = Food::factory()->create(['category_id' => $category->id]);
    $item1 = InventoryItem::factory()->create(['name' => 'Tomatoes']);
    $item2 = InventoryItem::factory()->create(['name' => 'Cheese']);

    FoodIngredient::factory()->create([
        'food_id' => $food->id,
        'inventory_item_id' => $item1->id,
        'quantity_required' => 0.5,
        'unit' => 'kg',
    ]);

    FoodIngredient::factory()->create([
        'food_id' => $food->id,
        'inventory_item_id' => $item2->id,
        'quantity_required' => 0.2,
        'unit' => 'kg',
    ]);

    expect($food->ingredients)->toHaveCount(2);
});

test('completing order deducts ingredients from inventory', function () {
    // Create inventory items
    $tomatoes = InventoryItem::factory()->create([
        'name' => 'Tomatoes',
        'quantity' => 100,
        'minimum_quantity' => 10,
    ]);

    $cheese = InventoryItem::factory()->create([
        'name' => 'Cheese',
        'quantity' => 50,
        'minimum_quantity' => 5,
    ]);

    // Create food with ingredients
    $category = Category::factory()->create();
    $pizza = Food::factory()->create([
        'category_id' => $category->id,
        'name' => 'Pizza',
    ]);

    FoodIngredient::factory()->create([
        'food_id' => $pizza->id,
        'inventory_item_id' => $tomatoes->id,
        'quantity_required' => 0.5,
        'unit' => 'kg',
    ]);

    FoodIngredient::factory()->create([
        'food_id' => $pizza->id,
        'inventory_item_id' => $cheese->id,
        'quantity_required' => 0.3,
        'unit' => 'kg',
    ]);

    // Create order
    $customer = User::factory()->create();
    $order = Order::factory()->create([
        'customer_id' => $customer->id,
        'status' => 'ready',
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'food_id' => $pizza->id,
        'quantity' => 2,
    ]);

    // Complete order - should trigger inventory deduction
    $order->updateStatus('completed');

    // Expected deduction: 2 pizzas * 0.5kg tomatoes = 1kg
    // Expected deduction: 2 pizzas * 0.3kg cheese = 0.6kg
    expect((float) $tomatoes->fresh()->quantity)->toBe(99.0)
        ->and((float) $cheese->fresh()->quantity)->toBe(49.4);
});

// Inventory Alert Tests
test('low stock generates alert automatically', function () {
    $item = InventoryItem::factory()->create([
        'quantity' => 50,
        'minimum_quantity' => 10,
    ]);

    $user = User::factory()->create();

    // Reduce stock below minimum
    $item->reduceStock(45, $user);

    expect($item->alerts()->where('status', 'active')->count())->toBe(1)
        ->and($item->alerts->first()->message)->toContain('Low stock alert');
});

test('adding stock resolves low stock alert', function () {
    $item = InventoryItem::factory()->create([
        'quantity' => 5,
        'minimum_quantity' => 10,
    ]);

    // Generate alert
    $item->generateLowStockAlert();
    expect($item->alerts()->active()->count())->toBe(1);

    // Add stock above minimum
    $user = User::factory()->create();
    $item->addStock(20, $user);

    expect($item->alerts()->active()->count())->toBe(0)
        ->and($item->alerts()->where('status', 'resolved')->count())->toBe(1);
});

test('alert can be resolved manually', function () {
    $alert = InventoryAlert::factory()->create(['status' => 'active']);

    $alert->resolve();

    expect($alert->fresh()->status)->toBe('resolved');
});

test('active alerts can be filtered', function () {
    InventoryAlert::factory()->create(['status' => 'active']);
    InventoryAlert::factory()->create(['status' => 'resolved']);
    InventoryAlert::factory()->create(['status' => 'active']);

    $active = InventoryAlert::active()->get();

    expect($active)->toHaveCount(2);
});

// Integration Tests
test('complete inventory workflow: purchase to usage', function () {
    // Setup
    $supplier = Supplier::factory()->create(['name' => 'Fresh Foods Inc']);
    $category = InventoryCategory::factory()->create(['name' => 'Vegetables']);
    $user = User::factory()->create();

    // Create inventory item
    $tomatoes = InventoryItem::factory()->create([
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'name' => 'Tomatoes',
        'quantity' => 10,
        'minimum_quantity' => 20,
        'cost_price' => 5,
    ]);

    // Item is low on stock
    expect($tomatoes->isLowStock())->toBeTrue();

    // Create purchase order
    $purchase = Purchase::factory()->create([
        'supplier_id' => $supplier->id,
        'status' => 'pending',
        'total_amount' => 250,
        'created_by' => $user->id,
    ]);

    PurchaseItem::factory()->create([
        'purchase_id' => $purchase->id,
        'inventory_item_id' => $tomatoes->id,
        'quantity' => 50,
        'unit_price' => 5,
        'subtotal' => 250,
    ]);

    // Receive purchase
    $purchase->markReceived($user);

    expect($purchase->fresh()->status)->toBe('received')
        ->and((float) $tomatoes->fresh()->quantity)->toBe(60.0)
        ->and($tomatoes->fresh()->isLowStock())->toBeFalse();

    // Use in food preparation
    $tomatoes->reduceStock(15, $user, 'usage', 'Daily prep');

    expect((float) $tomatoes->fresh()->quantity)->toBe(45.0)
        ->and($tomatoes->stockTransactions()->count())->toBe(2);
});

test('complete food order workflow with ingredient deduction', function () {
    // Setup inventory
    $flour = InventoryItem::factory()->create([
        'name' => 'Flour',
        'quantity' => 100,
        'unit' => 'kg',
        'minimum_quantity' => 10,
    ]);

    $sugar = InventoryItem::factory()->create([
        'name' => 'Sugar',
        'quantity' => 50,
        'unit' => 'kg',
        'minimum_quantity' => 5,
    ]);

    // Create food item (Cake)
    $foodCategory = Category::factory()->create();
    $cake = Food::factory()->create([
        'category_id' => $foodCategory->id,
        'name' => 'Chocolate Cake',
        'price' => 25,
    ]);

    // Define ingredients
    FoodIngredient::factory()->create([
        'food_id' => $cake->id,
        'inventory_item_id' => $flour->id,
        'quantity_required' => 0.5,
        'unit' => 'kg',
    ]);

    FoodIngredient::factory()->create([
        'food_id' => $cake->id,
        'inventory_item_id' => $sugar->id,
        'quantity_required' => 0.3,
        'unit' => 'kg',
    ]);

    // Customer places order
    $customer = User::factory()->create();
    $order = Order::factory()->create([
        'customer_id' => $customer->id,
        'status' => 'pending',
        'subtotal' => 75,
        'total_amount' => 75,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'food_id' => $cake->id,
        'quantity' => 3,
        'price' => 25,
        'subtotal' => 75,
    ]);

    // Process order through workflow
    $order->updateStatus('preparing');
    $order->updateStatus('ready');
    $order->updateStatus('served');

    // Complete order - this should deduct ingredients
    $order->updateStatus('completed');

    // Verify inventory deduction
    // 3 cakes * 0.5kg flour = 1.5kg deducted
    // 3 cakes * 0.3kg sugar = 0.9kg deducted
    expect((float) $flour->fresh()->quantity)->toBe(98.5)
        ->and((float) $sugar->fresh()->quantity)->toBe(49.1)
        ->and($flour->stockTransactions()->where('type', 'usage')->count())->toBe(1)
        ->and($sugar->stockTransactions()->where('type', 'usage')->count())->toBe(1);
});
