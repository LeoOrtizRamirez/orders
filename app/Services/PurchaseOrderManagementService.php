<?php

namespace App\Services;

use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Notifications\OrderStatusChanged;
use App\Repositories\ProductRepository;
use App\Repositories\PurchaseOrderRepository;
use App\Repositories\UserNoteRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class PurchaseOrderManagementService
{
    public function __construct(
        private PurchaseOrderRepository $purchaseOrderRepository,
        private ProductRepository $productRepository,
        private UserNoteService $userNoteService,
        private UserNoteRepository $userNoteRepository
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
        
        $purchaseOrder->load('notes.author');
        if ($purchaseOrder->items) {
            $purchaseOrder->items->load('itemNotes.author');
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

            $purchaseOrderData = [
                'order_number' => $data['order_number'],
                'supplier_id' => $data['supplier_id'],
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => $data['status'],
                'created_by' => $data['created_by'],
            ];

            $purchaseOrder = $this->purchaseOrderRepository->create($purchaseOrderData);

            if (!empty($data['notes'])) {
                $this->userNoteService->createNote([
                    'user_id' => null,
                    'author_id' => $user->id,
                    'note' => $data['notes'],
                    'is_important' => false,
                    'notable_id' => $purchaseOrder->id,
                    'notable_type' => 'purchase_order',
                ]);
            }

            foreach ($data['items'] as $itemData) {
                $purchaseOrderItem = $purchaseOrder->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'checked' => $itemData['checked'] ?? false,
                ]);

                if (!empty($itemData['notes'])) {
                    $this->userNoteService->createNote([
                        'user_id' => null,
                        'author_id' => $user->id,
                        'note' => $itemData['notes'],
                        'is_important' => false,
                        'notable_id' => $purchaseOrderItem->id,
                        'notable_type' => 'purchase_order_item',
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

            if (isset($data['notes'])) {
                $this->processNoteUpdate($user, $data['notes'], $purchaseOrder->notes, $purchaseOrder->id, 'purchase_order');
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
                                'quantity' => $itemData['quantity'],
                                'checked' => $itemData['checked'] ?? false,
                            ]);
                            $itemsToKeep[] = $item->id;

                            if (isset($itemData['notes'])) {
                                $this->processNoteUpdate($user, $itemData['notes'], $item->itemNotes, $item->id, 'purchase_order_item');
                            }
                        }
                    } else {
                        $newItem = $purchaseOrder->items()->create([
                            'product_id' => $itemData['product_id'],
                            'quantity' => $itemData['quantity'],
                            'checked' => $itemData['checked'] ?? false,
                        ]);
                        $itemsToKeep[] = $newItem->id;

                        if (isset($itemData['notes'])) {
                            $this->userNoteService->createNote([
                                'user_id' => null,
                                'author_id' => $user->id,
                                'note' => $itemData['notes'],
                                'is_important' => false,
                                'notable_id' => $newItem->id,
                                'notable_type' => 'purchase_order_item',
                            ]);
                        }
                    }
                }

                $itemsToDelete = array_diff($existingItemIds, $itemsToKeep);
                if (!empty($itemsToDelete)) {
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

    private function processNoteUpdate(User $user, ?string $newNoteText, $existingNotes, int $notableId, string $notableType)
    {
        if (!empty($newNoteText)) {
            $this->userNoteService->createNote([
                'user_id' => null,
                'author_id' => $user->id,
                'note' => $newNoteText,
                'is_important' => false,
                'notable_id' => $notableId,
                'notable_type' => $notableType,
            ]);
        }
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

        if ($newStatus === 'facturación') {
            return $this->processBilling($id);
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
            
            $purchaseOrder->update([
                'status' => 'preparar pedido',
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            $this->notifyUsersOnStatusChange($purchaseOrder, null);

            return $this->getPurchaseOrder($id);
        });
    }

    private function processBilling(int $id)
    {
        return DB::transaction(function () use ($id) {
            $purchaseOrder = $this->getPurchaseOrder($id);
            $purchaseOrder->load('items.product');

            foreach ($purchaseOrder->items as $item) {
                if ($item->product->stock < $item->quantity) {
                    throw new Exception("Stock insuficiente para 'facturación': {$item->product->name}. Disponible: {$item->product->stock}, Solicitado: {$item->quantity}", 400);
                }
            }

            foreach ($purchaseOrder->items as $item) {
                $item->product->updateStock(-$item->quantity);
            }

            $purchaseOrder->update([
                'status' => 'facturación',
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
                    'notable_type' => 'purchase_order',
                ]);
            }

            foreach ($itemsToSplit as $splitItemData) {
                // CASE 1: SPLIT EXISTING ITEM
                if (isset($splitItemData['item_id'])) {
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
                    ]);

                    // Copy original item's notes to the new sub-order item
                    foreach ($originalItem->itemNotes as $originalItemNote) {
                        $this->userNoteService->createNote([
                            'user_id' => null,
                            'author_id' => $originalItemNote->author_id, // Keep original author
                            'note' => $originalItemNote->note,
                            'is_important' => $originalItemNote->is_important,
                            'notable_id' => $newSubOrderItem->id,
                            'notable_type' => 'purchase_order_item',
                        ]);
                    }

                    // Add new note for this split item if provided
                    if (!empty($splitItemData['notes'])) {
                        $this->userNoteService->createNote([
                            'user_id' => null,
                            'author_id' => $user->id,
                            'note' => $splitItemData['notes'],
                            'is_important' => false,
                            'notable_id' => $newSubOrderItem->id,
                            'notable_type' => 'purchase_order_item',
                        ]);
                    }

                    if ($quantityToSplit === $originalItem->quantity) {
                        $originalItem->delete();
                    } else {
                        $originalItem->quantity -= $quantityToSplit;
                        $originalItem->save();
                    }
                } 
                // CASE 2: ADD NEW ITEM
                elseif (isset($splitItemData['product_id'])) {
                    $newSubOrderItem = $subOrder->items()->create([
                        'product_id' => $splitItemData['product_id'],
                        'quantity' => $splitItemData['quantity'],
                    ]);

                    // Add note for this new item if provided
                    if (!empty($splitItemData['notes'])) {
                        $this->userNoteService->createNote([
                            'user_id' => null,
                            'author_id' => $user->id,
                            'note' => $splitItemData['notes'],
                            'is_important' => false,
                            'notable_id' => $newSubOrderItem->id,
                            'notable_type' => 'purchase_order_item',
                        ]);
                    }
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