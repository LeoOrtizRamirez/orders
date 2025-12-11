<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $purchaseOrder;
    public $customMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(PurchaseOrder $purchaseOrder, ?string $customMessage = null)
    {
        $this->purchaseOrder = $purchaseOrder;
        $this->customMessage = $customMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $message = $this->customMessage ?? "La orden #{$this->purchaseOrder->order_number} ha cambiado su estado a: {$this->purchaseOrder->status}";

        return [
            'order_id' => $this->purchaseOrder->id,
            'order_number' => $this->purchaseOrder->order_number,
            'status' => $this->purchaseOrder->status,
            'message' => $message,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = $this->customMessage ?? "La orden #{$this->purchaseOrder->order_number} ha cambiado su estado a: {$this->purchaseOrder->status}";
        
        return [
            'order_id' => $this->purchaseOrder->id,
            'message' => $message,
        ];
    }
}
