<?php

namespace App\Notifications;

use App\Models\InventoryItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification
{
    use Queueable;

    public function __construct(public InventoryItem $item) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Low Stock Alert',
            'message' => "{$this->item->name} is running low ({$this->item->quantity} {$this->item->unit} remaining).",
            'url' => route('admin.inventory.items.show', $this->item->id),
            'type' => 'low_stock',
            'item_id' => $this->item->id,
        ];
    }
}
