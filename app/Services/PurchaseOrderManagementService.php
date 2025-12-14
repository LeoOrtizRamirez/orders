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
        private ProductRepository $productRepository,
        private UserNoteService $userNoteService // Inject UserNoteService
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
        
        // Eager load item notes
        $purchaseOrder->load('items.itemNotes.author', 'notes.author');

        return $purchaseOrder;
    }

    public function createPurchaseOrder(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $data['order_number'] = $this->purchaseOrderRepository->generateOrderNumber();
            $data['created_by'] = $user->id;
            $data['order_date'] = now();
            $data['status'] = 'nuevo pedido';

            $purchaseOrderData = [
                'order_number' => $data['order_number'],
                'supplier_id' => $data['supplier_id'],
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => $data['status'],
                // No longer directly assign notes here
                'created_by' => $data['created_by'],
            ];

            $purchaseOrder = $this->purchaseOrderRepository->create($purchaseOrderData);

            // Handle main purchase order notes
            if (!empty($data['notes'])) {
                $this->userNoteService->createNote([
                    'user_id' => null, // No direct user for order notes
                    'author_id' => $user->id,
                    'note' => $data['notes'],
                    'is_important' => false,
                    'notable_id' => $purchaseOrder->id,
                    'notable_type' => PurchaseOrder::class,
                ]);
            }

            foreach ($data['items'] as $itemData) {
                $purchaseOrderItem = $purchaseOrder->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                ]);

                // Handle item-specific notes
                if (!empty($itemData['notes'])) {
                    $this->userNoteService->createNote([
                        'user_id' => null, // No direct user for item notes
                        'author_id' => $user->id,
                        'note' => $itemData['notes'],
                        'is_important' => false,
                        'notable_id' => $purchaseOrderItem->id,
                        'notable_type' => PurchaseOrderItem::class,
                    ]);
                }
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

            // Handle main purchase order notes update
            if (isset($data['notes'])) { // 'notes' field is now a single string from frontend
                $this->userNoteService->createNote([ // Always create a new note on update for history
                    'user_id' => null,
                    'author_id' => $user->id,
                    'note' => $data['notes'],
                    'is_important' => false,
                    'notable_id' => $purchaseOrder->id,
                    'notable_type' => PurchaseOrder::class,
                ]);
            }

            if (isset($data['items'])) {
                $existingItemIds = $purchaseOrder->items->pluck('id')->toArray();
                $itemsToKeep = [];

                foreach ($data['items'] as $itemData) {
                    if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                        $item = $purchaseOrder->items->where('id', $itemData['id'])->first();
                        if ($item) {
                            $item->update([
                                'product_id' => $itemData['product_id'],
                                'quantity' => $itemData['quantity']
                            ]);
                            $itemsToKeep[] = $item->id;

                            // Handle item-specific notes update
                            if (isset($itemData['notes'])) { // Assuming single note string from frontend
                                $this->userNoteService->createNote([
                                    'user_id' => null,
                                    'author_id' => $user->id,
                                    'note' => $itemData['notes'],
                                    'is_important' => false,
                                    'notable_id' => $item->id,
                                    'notable_type' => PurchaseOrderItem::class,
                                ]);
                            }
                        }
                    } else {
                        $newItem = $purchaseOrder->items()->create([
                            'product_id' => $itemData['product_id'],
                            'quantity' => $itemData['quantity']
                        ]);
                        $itemsToKeep[] = $newItem->id;

                        // Handle item-specific notes for new items
                        if (isset($itemData['notes'])) {
                            $this->userNoteService->createNote([
                                'user_id' => null,
                                'author_id' => $user->id,
                                'note' => $itemData['notes'],
                                'is_important' => false,
                                'notable_id' => $newItem->id,
                                'notable_type' => PurchaseOrderItem::class,
                            ]);
                        }
                    }
                }

                $itemsToDelete = array_diff($existingItemIds, $itemsToKeep);
                if (!empty($itemsToDelete)) {
                    // Delete associated UserNotes when an item is deleted
                    foreach ($itemsToDelete as $deletedItemId) {
                        $deletedItem = PurchaseOrderItem::find($deletedItemId);
                        if ($deletedItem) {
                            $deletedItem->itemNotes()->delete();
                            $deletedItem->delete();
                        }
                    }
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
        $purchaseOrder = $this->getPurchaseOrder($id); // Check existence and load relationships
        
        return DB::transaction(function () use ($purchaseOrder) {
            // Delete all associated UserNotes for the purchase order and its items
            $purchaseOrder->notes()->delete(); // Delete notes for the main order
            foreach ($purchaseOrder->items as $item) {
                $item->itemNotes()->delete(); // Delete notes for each item
            }
            $purchaseOrder->items()->delete(); // Delete order items
            $purchaseOrder->delete(); // Delete the purchase order itself

            return true;
        });
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
        ?string $notes, // This is a single string note for the sub-order
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
                // No longer directly assign notes here
                'created_by' => $user->id,
                'parent_id' => $parentOrder->id,
            ]);

            // Handle sub-order notes
            if (!empty($notes)) {
                $this->userNoteService->createNote([
                    'user_id' => null,
                    'author_id' => $user->id,
                    'note' => $notes,
                    'is_important' => false,
                    'notable_id' => $subOrder->id,
                    'notable_type' => PurchaseOrder::class,
                ]);
            }

            foreach ($itemsToSplit as $splitItemData) {
                $originalItem = $parentOrder->items()->find($splitItemData['item_id']);

                if (!$originalItem) {
                    throw new Exception("Original item not found: {$splitItemData['item_id']}", 404);
                }

                $quantityToSplit = $splitItemData['quantity'];

                if ($quantityToSplit <= 0 || $quantityToSplit > $originalItem->quantity) {
                    throw new Exception("Invalid quantity to split for item {$originalItem->product->name}. Max available: {$originalItem->quantity}", 400);
                }

                $newSubOrderItem = $subOrder->items()->create([
                    'product_id' => $originalItem->product_id,
                    'quantity' => $quantityToSplit,
                    // No longer assign notes directly
                ]);

                // Copy original item's notes to the new sub-order item
                foreach ($originalItem->itemNotes as $originalItemNote) {
                    $this->userNoteService->createNote([
                        'user_id' => null,
                        'author_id' => $originalItemNote->author_id, // Keep original author
                        'note' => $originalItemNote->note,
                        'is_important' => $originalItemNote->is_important,
                        'notable_id' => $newSubOrderItem->id,
                        'notable_type' => PurchaseOrderItem::class,
                    ]);
                }


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