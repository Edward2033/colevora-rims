<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if status changed to completed
        if ($order->isDirty('status') && $order->status === 'completed') {
            $this->deductIngredients($order);
        }
    }

    /**
     * Deduct ingredients from inventory when order is completed.
     */
    protected function deductIngredients(Order $order): void
    {
        foreach ($order->items as $orderItem) {
            $food = $orderItem->food;

            // Check if food has ingredients defined
            foreach ($food->ingredients as $ingredient) {
                $quantityNeeded = $ingredient->quantity_required * $orderItem->quantity;

                // Reduce stock
                $ingredient->inventoryItem->reduceStock(
                    $quantityNeeded,
                    $order->customer,
                    'usage',
                    "Order {$order->order_number} - {$food->name}",
                    Order::class,
                    $order->id
                );
            }
        }
    }
}
