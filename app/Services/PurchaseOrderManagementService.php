<?php

namespace App\Services;

use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Notifications\OrderStatusChanged;
use App\Repositories\ProductRepository;
use App\Repositories\PurchaseOrderRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class PurchaseOrderManagementService
{
    public function __construct(
        private PurchaseOrderRepository $purchaseOrderRepository,
        private ProductRepository $productRepository
    ) {}

    public function getAllPurchaseOrders(array $filters = [])
    {
        return $this->purchaseOrderRepository->getAll($filters);
    }

    public function getPurchaseOrder(int $id)
    {
        $purchaseOrder = $this->purchaseOrderRepository->findById($id);
        
        if (!$purchaseOrder) {
            throw new Exception('Orden de compra no encontrada', 404);
        }

        return $purchaseOrder;
    }

    public function createPurchaseOrder(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $data['order_number'] = $this->purchaseOrderRepository->generateOrderNumber();
            $data['created_by'] = $user->id;
            $data['order_date'] = now();
            $data['status'] = 'nuevo pedido';

            $itemsData = [];
            foreach ($data['items'] as $itemData) {
                $itemNotes = [];
                if (!empty($itemData['notes'])) {
                    $itemNotes[] = ['user_id' => $user->id, 'user_name' => $user->name, 'note' => $itemData['notes'], 'timestamp' => now()->toDateTimeString()];
                }
                $itemsData[] = ['product_id' => $itemData['product_id'], 'quantity' => $itemData['quantity'], 'notes' => !empty($itemNotes) ? $itemNotes : null];
            }

            $orderNotes = [];
            if (!empty($data['notes'])) {
                $orderNotes[] = ['user_id' => $user->id, 'user_name' => $user->name, 'note' => $data['notes'], 'timestamp' => now()->toDateTimeString()];
            }

            $purchaseOrderData = [
                'order_number' => $data['order_number'],
                'supplier_id' => $data['supplier_id'],
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => $data['status'],
                'notes' => !empty($orderNotes) ? $orderNotes : null,
                'created_by' => $data['created_by'],
            ];

            $purchaseOrder = $this->purchaseOrderRepository->create($purchaseOrderData);

            foreach ($itemsData as $itemData) {
                $purchaseOrder->items()->create($itemData);
            }

            $this->notifyUsersOnStatusChange($purchaseOrder, null);

            return $this->getPurchaseOrder($purchaseOrder->id);
        });
    }

    public function updatePurchaseOrder(int $id, array $data, User $user)
    {
        $purchaseOrder = $this->getPurchaseOrder($id);
        
        return DB::transaction(function () use ($purchaseOrder, $data, $user) {
            $updateData = [];

            if (isset($data['supplier_id'])) $updateData['supplier_id'] = $data['supplier_id'];
            if (isset($data['expected_delivery_date'])) $updateData['expected_delivery_date'] = $data['expected_delivery_date'];

            if (!empty($data['notes'])) {
                $newNote = ['user_id' => $user->id, 'user_name' => $user->name, 'note' => $data['notes'], 'timestamp' => now()->toDateTimeString()];
                $existingNotes = $purchaseOrder->notes ?? [];
                if (!is_array($existingNotes)) $existingNotes = [];
                $existingNotes[] = $newNote;
                $updateData['notes'] = $existingNotes;
            }

            if (isset($data['items'])) {
                $existingItemIds = $purchaseOrder->items->pluck('id')->toArray();
                $itemsToKeep = [];

                foreach ($data['items'] as $itemData) {
                    if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                        $item = $purchaseOrder->items->where('id', $itemData['id'])->first();
                        if ($item) {
                            $item->update(['product_id' => $itemData['product_id'], 'quantity' => $itemData['quantity']]);
                            $itemsToKeep[] = $item->id;
                        }
                    } else {
                        $newItem = $purchaseOrder->items()->create(['product_id' => $itemData['product_id'], 'quantity' => $itemData['quantity']]);
                        $itemsToKeep[] = $newItem->id;
                    }
                }

                $itemsToDelete = array_diff($existingItemIds, $itemsToKeep);
                if (!empty($itemsToDelete)) {
                    $purchaseOrder->items()->whereIn('id', $itemsToDelete)->delete();
                }
            }

            if (!empty($updateData)) {
                $purchaseOrder->update($updateData);
            }

            return $this->getPurchaseOrder($purchaseOrder->id);
        });
    }

    public function deletePurchaseOrder(int $id)
    {
        $this->getPurchaseOrder($id); // Check existence
        $deleted = $this->purchaseOrderRepository->delete($id);
        if (!$deleted) {
            throw new Exception('Error al eliminar la orden de compra', 500);
        }
        return true;
    }

    public function updateStatus(int $id, string $newStatus, int $userId)
    {
        if ($newStatus === 'preparar pedido') {
            return $this->prepareOrder($id, $userId);
        }

        $purchaseOrder = $this->getPurchaseOrder($id);
        $purchaseOrder->update(['status' => $newStatus]);
        
        $this->notifyUsersOnStatusChange($purchaseOrder, null);

        return $this->getPurchaseOrder($id);
    }

    private function prepareOrder(int $id, int $userId)
    {
        return DB::transaction(function () use ($id, $userId) {
            $purchaseOrder = $this->getPurchaseOrder($id);
            $purchaseOrder->load('items.product');

            foreach ($purchaseOrder->items as $item) {
                if ($item->product->stock < $item->quantity) {
                    throw new Exception("Stock insuficiente para 'preparar pedido': {$item->product->name}. Disponible: {$item->product->stock}, Solicitado: {$item->quantity}", 400);
                }
            }

            foreach ($purchaseOrder->items as $item) {
                $item->product->updateStock(-$item->quantity);
            }

            $purchaseOrder->update([
                'status' => 'preparar pedido',
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            $this->notifyUsersOnStatusChange($purchaseOrder, null);

            return $this->getPurchaseOrder($id);
        });
    }

    private function notifyUsersOnStatusChange(PurchaseOrder $order, ?string $customMessage = null)
    {
        $statusRoleMap = [
            'nuevo pedido' => ['Comercial'],
            'disponibilidad' => ['Comercial'],
            'preparar pedido' => ['Despachos'],
            'en preparación' => ['Despachos'],
            'facturación' => ['Contabilidad'],
            'en despacho' => ['Comercial', 'Despachos'],
            'en ruta' => ['Comercial'],
            'entregado' => ['Comercial'],
        ];

        $rolesToNotify = $statusRoleMap[$order->status] ?? [];

        // If it's a custom message, notify all roles that could ever be involved, plus admins.
        if ($customMessage) {
            $rolesToNotify = ['Comercial', 'Despachos', 'Contabilidad'];
        }

        if (empty($rolesToNotify)) {
            return;
        }

        $adminRole = 'Administrador';
        
        $users = User::whereHas('roles', function ($query) use ($rolesToNotify, $adminRole) {
            $query->whereIn('name', $rolesToNotify)->orWhere('name', $adminRole);
        })->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new OrderStatusChanged($order, $customMessage));
        }
    }

    public function splitPurchaseOrder(
        PurchaseOrder $parentOrder,
        array $itemsToSplit,
        ?string $expectedDeliveryDate,
        ?string $notes,
        User $user
    ): PurchaseOrder {
        return DB::transaction(function () use ($parentOrder, $itemsToSplit, $expectedDeliveryDate, $notes, $user) {
            if ($parentOrder->parent_id !== null) {
                throw new Exception('Cannot split a sub-order. Only main orders can be split.', 400);
            }

            $subOrder = $this->purchaseOrderRepository->create([
                'order_number' => $this->purchaseOrderRepository->generateOrderNumber(),
                'supplier_id' => $parentOrder->supplier_id,
                'order_date' => now(),
                'expected_delivery_date' => $expectedDeliveryDate,
                'status' => 'nuevo pedido',
                'notes' => $notes ? [['user_id' => $user->id, 'user_name' => $user->name, 'note' => $notes, 'timestamp' => now()->toDateTimeString()]] : null,
                'created_by' => $user->id,
                'parent_id' => $parentOrder->id,
            ]);

            foreach ($itemsToSplit as $splitItemData) {
                $originalItem = $parentOrder->items()->find($splitItemData['item_id']);

                if (!$originalItem) {
                    throw new Exception("Original item not found: {$splitItemData['item_id']}", 404);
                }

                $quantityToSplit = $splitItemData['quantity'];

                if ($quantityToSplit <= 0 || $quantityToSplit > $originalItem->quantity) {
                    throw new Exception("Invalid quantity to split for item {$originalItem->product->name}. Max available: {$originalItem->quantity}", 400);
                }

                $subOrder->items()->create([
                    'product_id' => $originalItem->product_id,
                    'quantity' => $quantityToSplit,
                    'notes' => $originalItem->notes,
                ]);

                if ($quantityToSplit === $originalItem->quantity) {
                    $originalItem->delete();
                } else {
                    $originalItem->quantity -= $quantityToSplit;
                    $originalItem->save();
                }
            }

            $this->notifyUsersOnStatusChange($subOrder);

            // Notify that the parent order has been modified.
            $parentOrder->refresh();
            $customMessage = "La orden #{$parentOrder->order_number} ha sido modificada para crear la orden secundaria #{$subOrder->order_number}.";
            $this->notifyUsersOnStatusChange($parentOrder, $customMessage);


            return $this->getPurchaseOrder($subOrder->id);
        });
    }
}