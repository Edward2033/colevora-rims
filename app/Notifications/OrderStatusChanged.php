<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $oldStatus) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Order Status Updated',
            'message' => "Order #{$this->order->order_number} is now ".ucfirst($this->order->status).'.',
            'url' => route('customer.order.track', $this->order->id),
            'type' => 'order_status',
            'order_id' => $this->order->id,
        ];
    }
}
