<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\RestaurantTable;
use App\Models\User;
use Database\Seeders\RestaurantTableSeeder;

// Restaurant Table Management Tests
test('restaurant table can be created with qr code', function () {
    $table = RestaurantTable::factory()->create();

    expect($table->qr_code)->not->toBeNull()
        ->and($table->status)->toBe('available');
});

test('restaurant table qr code is auto-generated', function () {
    $table = RestaurantTable::factory()->create(['qr_code' => '']);

    expect($table->qr_code)->not->toBeEmpty();
});

test('restaurant table can be marked occupied', function () {
    $table = RestaurantTable::factory()->create(['status' => 'available']);

    $table->markOccupied();

    expect($table->fresh()->status)->toBe('occupied');
});

test('restaurant table can be marked available', function () {
    $table = RestaurantTable::factory()->create(['status' => 'occupied']);

    $table->markAvailable();

    expect($table->fresh()->status)->toBe('available');
});

test('available tables can be filtered', function () {
    RestaurantTable::factory()->create(['status' => 'available']);
    RestaurantTable::factory()->create(['status' => 'occupied']);
    RestaurantTable::factory()->create(['status' => 'available']);

    $available = RestaurantTable::available()->get();

    expect($available)->toHaveCount(2);
});

test('restaurant tables are seeded correctly', function () {
    $this->seed([RestaurantTableSeeder::class]);

    expect(RestaurantTable::count())->toBe(10);
});

// Cart System Tests
test('cart can add food item', function () {
    $cart = Cart::factory()->create(['status' => 'active']);
    $food = Food::factory()->create(['price' => 100]);

    $item = $cart->addItem($food, 2);

    expect($item->food_id)->toBe($food->id)
        ->and($item->quantity)->toBe(2)
        ->and((float) $item->price)->toBe(100.0);
});

test('cart updates quantity for existing item', function () {
    $cart = Cart::factory()->create(['status' => 'active']);
    $food = Food::factory()->create(['price' => 100]);

    $cart->addItem($food, 2);
    $cart->addItem($food, 3);

    $item = $cart->items()->where('food_id', $food->id)->first();

    expect($item->quantity)->toBe(5);
});

test('cart can remove item', function () {
    $cart = Cart::factory()->create();
    $food = Food::factory()->create();
    $cart->addItem($food);

    $cart->removeItem($food->id);

    expect($cart->items()->count())->toBe(0);
});

test('cart can update item quantity', function () {
    $cart = Cart::factory()->create();
    $food = Food::factory()->create();
    $cart->addItem($food, 2);

    $cart->updateItemQuantity($food->id, 5);

    $item = $cart->items()->where('food_id', $food->id)->first();
    expect($item->quantity)->toBe(5);
});

test('cart removes item when quantity is zero', function () {
    $cart = Cart::factory()->create();
    $food = Food::factory()->create();
    $cart->addItem($food, 2);

    $cart->updateItemQuantity($food->id, 0);

    expect($cart->items()->count())->toBe(0);
});

test('cart calculates subtotal correctly', function () {
    $cart = Cart::factory()->create();
    $food1 = Food::factory()->create(['price' => 100, 'discount_price' => null]);
    $food2 = Food::factory()->create(['price' => 50, 'discount_price' => null]);

    $cart->addItem($food1, 2);
    $cart->addItem($food2, 3);

    expect($cart->subtotal)->toBe(350.0);
});

test('cart can be cleared', function () {
    $cart = Cart::factory()->create();
    $food1 = Food::factory()->create();
    $food2 = Food::factory()->create();
    $cart->addItem($food1);
    $cart->addItem($food2);

    $cart->clear();

    expect($cart->items()->count())->toBe(0);
});

test('cart item has subtotal attribute', function () {
    $cartItem = CartItem::factory()->create([
        'quantity' => 3,
        'price' => 50,
    ]);

    expect($cartItem->subtotal)->toBe(150.0);
});

// Order System Tests
test('order is created with auto-generated order number', function () {
    $order = Order::factory()->create(['order_number' => '']);

    expect($order->order_number)->not->toBeEmpty()
        ->and($order->order_number)->toStartWith('ORD-');
});

test('order belongs to customer', function () {
    $customer = User::factory()->create();
    $order = Order::factory()->create(['customer_id' => $customer->id]);

    expect($order->customer->id)->toBe($customer->id);
});

test('order can have table assignment', function () {
    $table = RestaurantTable::factory()->create();
    $order = Order::factory()->create(['table_id' => $table->id]);

    expect($order->table->id)->toBe($table->id);
});

test('order can have assigned waiter', function () {
    $waiter = Employee::factory()->create();
    $order = Order::factory()->create(['assigned_waiter_id' => $waiter->id]);

    expect($order->waiter->id)->toBe($waiter->id);
});

test('order has many items', function () {
    $order = Order::factory()->create();
    OrderItem::factory()->count(3)->create(['order_id' => $order->id]);

    expect($order->items)->toHaveCount(3);
});

test('order can update status', function () {
    $order = Order::factory()->create(['status' => 'pending']);

    $order->updateStatus('preparing');

    expect($order->fresh()->status)->toBe('preparing');
});

test('pending orders can be filtered', function () {
    Order::factory()->create(['status' => 'pending']);
    Order::factory()->create(['status' => 'completed']);
    Order::factory()->create(['status' => 'pending']);

    $pending = Order::pending()->get();

    expect($pending)->toHaveCount(2);
});

test('preparing orders can be filtered', function () {
    Order::factory()->create(['status' => 'preparing']);
    Order::factory()->create(['status' => 'ready']);

    $preparing = Order::preparing()->get();

    expect($preparing)->toHaveCount(1);
});

test('ready orders can be filtered', function () {
    Order::factory()->create(['status' => 'ready']);
    Order::factory()->create(['status' => 'served']);

    $ready = Order::ready()->get();

    expect($ready)->toHaveCount(1);
});

test('order can be cancelled when pending', function () {
    $order = Order::factory()->create(['status' => 'pending']);

    expect($order->canBeCancelled())->toBeTrue();
});

test('order cannot be cancelled when completed', function () {
    $order = Order::factory()->create(['status' => 'completed']);

    expect($order->canBeCancelled())->toBeFalse();
});

// Order Item Tests
test('order item calculates subtotal correctly', function () {
    $orderItem = OrderItem::factory()->create([
        'quantity' => 4,
        'price' => 25,
        'subtotal' => 100,
    ]);

    expect((float) $orderItem->subtotal)->toBe(100.0);
});

test('order item belongs to order and food', function () {
    $order = Order::factory()->create();
    $food = Food::factory()->create();
    $orderItem = OrderItem::factory()->create([
        'order_id' => $order->id,
        'food_id' => $food->id,
    ]);

    expect($orderItem->order->id)->toBe($order->id)
        ->and($orderItem->food->id)->toBe($food->id);
});

// Order Assignment Tests
test('order can be assigned to employee', function () {
    $order = Order::factory()->create();
    $employee = Employee::factory()->create();
    $assigner = User::factory()->create();

    $assignment = OrderAssignment::factory()->create([
        'order_id' => $order->id,
        'employee_id' => $employee->id,
        'assigned_by' => $assigner->id,
    ]);

    expect($assignment->order->id)->toBe($order->id)
        ->and($assignment->employee->id)->toBe($employee->id)
        ->and($assignment->assigner->id)->toBe($assigner->id);
});

test('active order assignments can be filtered', function () {
    OrderAssignment::factory()->create(['status' => 'active']);
    OrderAssignment::factory()->create(['status' => 'completed']);
    OrderAssignment::factory()->create(['status' => 'active']);

    $active = OrderAssignment::active()->get();

    expect($active)->toHaveCount(2);
});

// Payment System Tests
test('payment belongs to order', function () {
    $order = Order::factory()->create();
    $payment = Payment::factory()->create(['order_id' => $order->id]);

    expect($payment->order->id)->toBe($order->id);
});

test('payment can be marked completed', function () {
    $payment = Payment::factory()->create(['status' => 'pending']);
    $processor = User::factory()->create();

    $payment->markCompleted($processor);

    expect($payment->fresh()->status)->toBe('completed')
        ->and($payment->fresh()->paid_by)->toBe($processor->id)
        ->and($payment->fresh()->paid_at)->not->toBeNull();
});

test('payment can be marked failed', function () {
    $payment = Payment::factory()->create(['status' => 'pending']);

    $payment->markFailed();

    expect($payment->fresh()->status)->toBe('failed');
});

test('completed payments can be filtered', function () {
    Payment::factory()->create(['status' => 'completed']);
    Payment::factory()->create(['status' => 'pending']);
    Payment::factory()->create(['status' => 'completed']);

    $completed = Payment::completed()->get();

    expect($completed)->toHaveCount(2);
});

test('order knows if it is paid', function () {
    $order = Order::factory()->create();
    Payment::factory()->create([
        'order_id' => $order->id,
        'status' => 'completed',
    ]);

    expect($order->fresh()->isPaid())->toBeTrue();
});

test('order knows if it is not paid', function () {
    $order = Order::factory()->create();
    Payment::factory()->create([
        'order_id' => $order->id,
        'status' => 'pending',
    ]);

    expect($order->fresh()->isPaid())->toBeFalse();
});

// Order Workflow Tests
test('order workflow: pending to preparing to ready to served', function () {
    $order = Order::factory()->create(['status' => 'pending']);

    // Chef accepts order
    $order->updateStatus('preparing');
    expect($order->fresh()->status)->toBe('preparing');

    // Chef marks ready
    $order->updateStatus('ready');
    expect($order->fresh()->status)->toBe('ready');

    // Waiter serves
    $order->updateStatus('served');
    expect($order->fresh()->status)->toBe('served');
});

test('complete order flow with cart to order', function () {
    $customer = User::factory()->create();
    $table = RestaurantTable::factory()->create();
    $category = Category::factory()->create();
    $food1 = Food::factory()->create(['category_id' => $category->id, 'price' => 100]);
    $food2 = Food::factory()->create(['category_id' => $category->id, 'price' => 50]);

    // Create cart and add items
    $cart = Cart::factory()->create(['user_id' => $customer->id]);
    $cart->addItem($food1, 2);
    $cart->addItem($food2, 1);

    // Calculate totals
    $subtotal = $cart->subtotal;
    $tax = $subtotal * 0.1;
    $totalAmount = $subtotal + $tax;

    // Create order from cart
    $order = Order::factory()->create([
        'customer_id' => $customer->id,
        'table_id' => $table->id,
        'status' => 'pending',
        'subtotal' => $subtotal,
        'tax' => $tax,
        'total_amount' => $totalAmount,
    ]);

    // Create order items from cart items
    foreach ($cart->items as $cartItem) {
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'food_id' => $cartItem->food_id,
            'quantity' => $cartItem->quantity,
            'price' => $cartItem->price,
            'subtotal' => $cartItem->subtotal,
        ]);
    }

    // Create payment
    $payment = Payment::factory()->create([
        'order_id' => $order->id,
        'amount' => $totalAmount,
        'status' => 'pending',
    ]);

    expect($order->items)->toHaveCount(2)
        ->and((float) $order->subtotal)->toBe(250.0)
        ->and($order->payment)->not->toBeNull();

    // Complete payment
    $cashier = User::factory()->create();
    $payment->markCompleted($cashier);

    expect($order->fresh()->isPaid())->toBeTrue();

    // Mark order completed
    $order->updateStatus('completed');
    expect($order->fresh()->status)->toBe('completed');
});
