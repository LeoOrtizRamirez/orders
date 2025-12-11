<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\PurchaseOrderRepository;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Support\Facades\DB;

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
            $data['status'] = 'nuevo pedido'; // Set default status to 'nuevo pedido'

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

    /**
     * Update the status of a purchase order.
     * Special logic for 'preparar pedido' to check and deduct stock.
     */
    public function updateStatus(int $id, string $newStatus, int $userId)
    {
        if ($newStatus === 'preparar pedido') {
            return $this->prepareOrder($id, $userId);
        }

        $purchaseOrder = $this->getPurchaseOrder($id);
        $purchaseOrder->update(['status' => $newStatus]);
        return $this->getPurchaseOrder($id);
    }

    /**
     * Handles the specific logic for moving an order to 'preparar pedido'.
     */
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
                'approved_by' => $userId, // Using approved_by for tracking who prepared it
                'approved_at' => now(),   // Using approved_at for tracking when it was prepared
            ]);

            return $this->getPurchaseOrder($id);
        });
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

            // Create new sub-order
            $subOrder = $this->purchaseOrderRepository->create([
                'order_number' => $this->purchaseOrderRepository->generateOrderNumber(),
                'supplier_id' => $parentOrder->supplier_id,
                'order_date' => now(),
                'expected_delivery_date' => $expectedDeliveryDate,
                'status' => 'nuevo pedido', // Set status to 'nuevo pedido'
                'notes' => $notes ? [['user_id' => $user->id, 'user_name' => $user->name, 'note' => $notes, 'timestamp' => now()->toDateTimeString()]] : null,
                'created_by' => $user->id,
                'parent_id' => $parentOrder->id,
            ]);

            // Process items to split
            foreach ($itemsToSplit as $splitItemData) {
                $originalItem = $parentOrder->items()->find($splitItemData['item_id']);

                if (!$originalItem) {
                    throw new Exception("Original item not found: {$splitItemData['item_id']}", 404);
                }

                $quantityToSplit = $splitItemData['quantity'];

                if ($quantityToSplit <= 0 || $quantityToSplit > $originalItem->quantity) {
                    throw new Exception("Invalid quantity to split for item {$originalItem->product->name}. Max available: {$originalItem->quantity}", 400);
                }

                // Create new item for sub-order
                $subOrder->items()->create([
                    'product_id' => $originalItem->product_id,
                    'quantity' => $quantityToSplit,
                    'notes' => $originalItem->notes,
                ]);

                // Update original item quantity or delete if fully moved
                if ($quantityToSplit === $originalItem->quantity) {
                    $originalItem->delete();
                } else {
                    $originalItem->quantity -= $quantityToSplit;
                    $originalItem->save();
                }
            }

            return $this->getPurchaseOrder($subOrder->id);
        });
    }
}