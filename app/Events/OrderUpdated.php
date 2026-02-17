<?php

namespace App\Events;

use App\Models\PurchaseOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct(PurchaseOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('orders.board'),
        ];
    }

    public function broadcastWith(): array
    {
        // Aseguramos que el conteo de sub-órdenes esté cargado para la interfaz
        if (!isset($this->order->sub_orders_count)) {
            $this->order->loadCount('subOrders');
        }

        // Solo enviamos los campos necesarios para actualizar la tarjeta del Kanban
        // para evitar exceder el límite de 10KB de Pusher (especialmente con muchos items/notas)
        return [
            'order' => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'status' => $this->order->status,
                'supplier' => $this->order->supplier ? [
                    'id' => $this->order->supplier->id,
                    'name' => $this->order->supplier->name,
                ] : null,
                'creator' => $this->order->creator ? [
                    'id' => $this->order->creator->id,
                    'name' => $this->order->creator->name,
                ] : null,
                // El Kanban solo muestra la primera nota si existe
                'notes' => $this->order->notes ? $this->order->notes->take(1)->map(function($note) {
                    return ['note' => $note->note];
                }) : [],
                'created_at' => $this->order->created_at,
                'parent_id' => $this->order->parent_id,
                'sub_orders_count' => $this->order->sub_orders_count,
                'can_be_edited' => $this->order->can_be_edited,
                'can_be_deleted' => $this->order->can_be_deleted,
            ],
            'updated_by' => auth()->id(),
        ];
    }
}
