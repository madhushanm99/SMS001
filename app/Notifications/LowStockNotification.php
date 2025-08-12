<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $itemId;
    public string $itemName;
    public int $currentQty;
    public int $reorderLevel;
    public string $title;
    public string $message;

    public function __construct(string $itemId, string $itemName, int $currentQty, int $reorderLevel, string $title, string $message)
    {
        $this->itemId = $itemId;
        $this->itemName = $itemName;
        $this->currentQty = $currentQty;
        $this->reorderLevel = $reorderLevel;
        $this->title = $title;
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'low_stock',
            'title' => $this->title,
            'message' => $this->message,
            'item_id' => $this->itemId,
            'item_name' => $this->itemName,
            'current_qty' => $this->currentQty,
            'reorder_level' => $this->reorderLevel,
            'url' => route('stock.index'),
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}


