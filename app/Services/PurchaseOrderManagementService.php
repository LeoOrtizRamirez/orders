<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\PurchaseOrderRepository;
use App\Models\PurchaseOrder; // Add this import
use App\Models\PurchaseOrderItem; // Add this import
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
            $data['status'] = 'draft';

            $itemsData = [];
            foreach ($data['items'] as $itemData) {
                $itemNotes = [];
                if (!empty($itemData['notes'])) {
                    $itemNotes[] = [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'note' => $itemData['notes'],
                        'timestamp' => now()->toDateTimeString(),
                    ];
                }

                $itemsData[] = [
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'notes' => !empty($itemNotes) ? $itemNotes : null,
                ];
            }

            $orderNotes = [];
            if (!empty($data['notes'])) {
                $orderNotes[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'note' => $data['notes'],
                    'timestamp' => now()->toDateTimeString(),
                ];
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

            if (isset($data['supplier_id'])) {
                $updateData['supplier_id'] = $data['supplier_id'];
            }

            if (isset($data['expected_delivery_date'])) {
                $updateData['expected_delivery_date'] = $data['expected_delivery_date'];
            }

            if (!empty($data['notes'])) {
                $newNote = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'note' => $data['notes'],
                    'timestamp' => now()->toDateTimeString(),
                ];
                $existingNotes = $purchaseOrder->notes ?? [];
                // Asegurarse de que $existingNotes es un array antes de añadir
                if (!is_array($existingNotes)) {
                    $existingNotes = [];
                }
                $existingNotes[] = $newNote;
                $updateData['notes'] = $existingNotes;
            }

            // --- Lógica para Items ---
            if (isset($data['items'])) {
                $existingItemIds = $purchaseOrder->items->pluck('id')->toArray();
                $itemsToKeep = [];

                foreach ($data['items'] as $itemData) {
                    // Si el ítem tiene ID y existe en la orden actual
                    if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                        $item = $purchaseOrder->items->where('id', $itemData['id'])->first();
                        if ($item) {
                            $itemUpdateData = [
                                'product_id' => $itemData['product_id'],
                                'quantity' => $itemData['quantity'],
                            ];

                            // Manejo de notas del ítem
                            if (!empty($itemData['notes'])) {
                                $newItemNote = [
                                    'user_id' => $user->id,
                                    'user_name' => $user->name,
                                    'note' => $itemData['notes'],
                                    'timestamp' => now()->toDateTimeString(),
                                ];
                                $existingItemNotes = $item->notes ?? [];
                                if (!is_array($existingItemNotes)) {
                                    $existingItemNotes = [];
                                }
                                $existingItemNotes[] = $newItemNote;
                                $itemUpdateData['notes'] = $existingItemNotes;
                            }
                            
                            $item->update($itemUpdateData);
                            $itemsToKeep[] = $item->id;
                        }
                    } else {
                        // Es un nuevo ítem o un ítem sin ID, crearlo
                        $itemNotes = [];
                        if (!empty($itemData['notes'])) {
                            $itemNotes[] = [
                                'user_id' => $user->id,
                                'user_name' => $user->name,
                                'note' => $itemData['notes'],
                                'timestamp' => now()->toDateTimeString(),
                            ];
                        }
                        $newItem = $purchaseOrder->items()->create([
                            'product_id' => $itemData['product_id'],
                            'quantity' => $itemData['quantity'],
                            'notes' => !empty($itemNotes) ? $itemNotes : null,
                        ]);
                        $itemsToKeep[] = $newItem->id;
                    }
                }

                // Eliminar ítems que ya no están en la solicitud
                $itemsToDelete = array_diff($existingItemIds, $itemsToKeep);
                if (!empty($itemsToDelete)) {
                    $purchaseOrder->items()->whereIn('id', $itemsToDelete)->delete();
                }
            }

            // Actualizar la orden principal
            if (!empty($updateData)) {
                $purchaseOrder->update($updateData);
            }

            return $this->getPurchaseOrder($purchaseOrder->id);
        });
    }

    public function deletePurchaseOrder(int $id)
    {
        $purchaseOrder = $this->getPurchaseOrder($id);

        $deleted = $this->purchaseOrderRepository->delete($id);
        
        if (!$deleted) {
            throw new Exception('Error al eliminar la orden de compra', 500);
        }

        return true;
    }

    public function approvePurchaseOrder(int $id, int $userId)
    {
        $purchaseOrder = $this->getPurchaseOrder($id);

        if (!$purchaseOrder->canBeApproved()) {
            throw new Exception('No se puede aprobar esta orden de compra', 400);
        }

        $purchaseOrder->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        return $this->getPurchaseOrder($id);
    }

    public function rejectPurchaseOrder(int $id, string $reason, int $userId)
    {
        $purchaseOrder = $this->getPurchaseOrder($id);

        if (!$purchaseOrder->canBeApproved()) {
            throw new Exception('No se puede rechazar esta orden de compra', 400);
        }

        $purchaseOrder->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        return $this->getPurchaseOrder($id);
    }

    public function markAsOrdered(int $id)
    {
        $purchaseOrder = $this->getPurchaseOrder($id);

        if ($purchaseOrder->status !== 'approved') {
            throw new Exception('Solo se pueden marcar como ordenadas las órdenes aprobadas', 400);
        }

        $purchaseOrder->update([
            'status' => 'ordered',
        ]);

        return $this->getPurchaseOrder($id);
    }

    public function receivePurchaseOrder(int $id, array $receivedItems)
    {
        $purchaseOrder = $this->getPurchaseOrder($id);

        if (!$purchaseOrder->canBeReceived()) {
            throw new Exception('No se puede recibir esta orden de compra', 400);
        }

        return DB::transaction(function () use ($purchaseOrder, $receivedItems) {
            $allReceived = true;

            foreach ($receivedItems as $receivedItem) {
                $item = $purchaseOrder->items()->find($receivedItem['item_id']);
                if (!$item) {
                    throw new Exception("Item no encontrado: {$receivedItem['item_id']}", 404);
                }

                $receivedQuantity = $receivedItem['received_quantity'];
                $item->received_quantity += $receivedQuantity;
                $item->save();

                $this->productRepository->updateStock($item->product_id, $receivedQuantity);

                if ($item->getPendingQuantity() > 0) {
                    $allReceived = false;
                }
            }

            if ($allReceived) {
                $purchaseOrder->update([
                    'status' => 'received',
                    'delivery_date' => now(),
                ]);
            } else {
                $purchaseOrder->update([
                    'status' => 'partially_received',
                ]);
            }

            return $this->getPurchaseOrder($purchaseOrder->id);
        });
    }

        public function getPendingOrders()

        {

            return $this->purchaseOrderRepository->getPendingOrders();

        }

    

        public function getKanbanPurchaseOrders()
        {
            return $this->purchaseOrderRepository->getKanbanOrders();
        }

    public function splitPurchaseOrder(
        PurchaseOrder $parentOrder,
        array $itemsToSplit,
        ?string $expectedDeliveryDate,
        ?string $notes,
        User $user
    ): PurchaseOrder
    {
        return DB::transaction(function () use ($parentOrder, $itemsToSplit, $expectedDeliveryDate, $notes, $user) {
            if ($parentOrder->parent_id !== null) {
                throw new Exception('Cannot split a sub-order. Only main orders can be split.', 400);
            }

            // Create new sub-order
            $subOrder = $this->purchaseOrderRepository->create([
                'order_number' => $this->purchaseOrderRepository->generateOrderNumber(),
                'supplier_id' => $parentOrder->supplier_id, // Same supplier as parent
                'order_date' => now(),
                'expected_delivery_date' => $expectedDeliveryDate,
                'status' => 'draft', // Sub-order starts as draft
                'notes' => $notes ? [['user_id' => $user->id, 'user_name' => $user->name, 'note' => $notes, 'timestamp' => now()->toDateTimeString()]] : null,
                'created_by' => $user->id,
                'parent_id' => $parentOrder->id, // Link to parent
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
                    'notes' => $originalItem->notes, // Inherit notes or make independent? User said independent, but for items, maybe inherit initially. Clarify. For now, inherit.
                ]);

                // Update original item quantity or delete if fully moved
                if ($quantityToSplit === $originalItem->quantity) {
                    $originalItem->delete();
                } else {
                    $originalItem->quantity -= $quantityToSplit;
                    $originalItem->save();
                }
            }

            // Update parent order totals (this should be done automatically if a relationship is setup for totals)
            // For now, re-fetch parent order to ensure relationships are loaded for recalculation
            // $parentOrder->refresh(); // Or recalculate explicitly if needed.
            // $parentOrder->calculateTotals(); // If a calculateTotals method exists and updates order's total fields directly.

            // Reload parent order items to correctly check if it's empty
            $parentOrder->load('items');

            $parentOrder->status = 'draft'; // New status for parent
            
            $parentOrder->save();

            return $this->getPurchaseOrder($subOrder->id);
        });
    }
}