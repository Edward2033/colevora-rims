<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderPlaced extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Order Received',
            'message' => "Order #{$this->order->order_number} has been placed for \${$this->order->total_amount}.",
            'url' => route('admin.orders.show', $this->order->id),
            'type' => 'new_order',
            'order_id' => $this->order->id,
        ];
    }
}
