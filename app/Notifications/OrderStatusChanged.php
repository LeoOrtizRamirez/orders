<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class OrderStatusChanged extends Notification implements ShouldQueue, ShouldBroadcast
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
        return ['database', 'broadcast'];
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
            'id' => $this->id, // Include Notification ID for tracking
            'order_id' => $this->purchaseOrder->id,
            'order_number' => $this->purchaseOrder->order_number,
            'status' => $this->purchaseOrder->status,
            'message' => $message, // Keep for simple clients
            'message_parts' => $messageParts, // For rich clients
            'created_at' => now()->toIso8601String(),
            'read_at' => null,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        // Use the same structure as toDatabase for consistency on frontend
        return new BroadcastMessage($this->toDatabase($notifiable));
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
