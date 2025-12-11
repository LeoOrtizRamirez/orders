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
        $defaultMessage = "La orden #{$this->purchaseOrder->order_number} ha cambiado su estado a: {$this->purchaseOrder->status}";
        $message = $this->customMessage ?? $defaultMessage;

        // Default structured message
        $messageParts = [
            ['type' => 'text', 'content' => 'La orden '],
            ['type' => 'link', 'content' => "#{$this->purchaseOrder->order_number}"],
            ['type' => 'text', 'content' => " ha cambiado su estado a: {$this->purchaseOrder->status}"],
        ];

        // If a custom message is provided, we won't use the structured format for now,
        // as we'd need a more complex parser. We will just send the plain custom message.
        if ($this->customMessage) {
            $messageParts = [
                ['type' => 'text', 'content' => $this->customMessage],
            ];
        }

        return [
            'order_id' => $this->purchaseOrder->id,
            'order_number' => $this->purchaseOrder->order_number,
            'status' => $this->purchaseOrder->status,
            'message' => $message, // Keep for simple clients
            'message_parts' => $messageParts, // For rich clients
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $defaultMessage = "La orden #{$this->purchaseOrder->order_number} ha cambiado su estado a: {$this->purchaseOrder->status}";
        $message = $this->customMessage ?? $defaultMessage;
        
        return [
            'order_id' => $this->purchaseOrder->id,
            'message' => $message,
        ];
    }
}
