<?php

namespace App\Services;

use App\Repositories\PurchaseOrderRepository;
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

    public function createPurchaseOrder(array $data, int $userId)
    {
        return DB::transaction(function () use ($data, $userId) {
            // Generar número de orden
            $data['order_number'] = $this->purchaseOrderRepository->generateOrderNumber();
            $data['created_by'] = $userId;
            $data['order_date'] = now();

            // Crear la orden
            $purchaseOrder = $this->purchaseOrderRepository->create($data);

            // Crear items
            $subtotal = 0;
            foreach ($data['items'] as $itemData) {
                $product = $this->productRepository->findById($itemData['product_id']);
                if (!$product) {
                    throw new Exception("Producto no encontrado: {$itemData['product_id']}", 404);
                }

                $unitPrice = $itemData['unit_price'] ?? $product->cost ?? $product->price;
                $totalPrice = $itemData['quantity'] * $unitPrice;

                $purchaseOrder->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'notes' => $itemData['notes'] ?? null,
                ]);

                $subtotal += $totalPrice;
            }

            // Actualizar totales
            $purchaseOrder->calculateTotals();
            $purchaseOrder->save();

            return $this->getPurchaseOrder($purchaseOrder->id);
        });
    }

    public function updatePurchaseOrder(int $id, array $data)
    {
        $purchaseOrder = $this->getPurchaseOrder($id);

        if (!$purchaseOrder->canBeEdited()) {
            throw new Exception('No se puede editar una orden en este estado', 400);
        }

        return DB::transaction(function () use ($purchaseOrder, $data) {
            // Actualizar datos básicos
            if (isset($data['supplier_id'])) {
                $purchaseOrder->supplier_id = $data['supplier_id'];
            }

            if (isset($data['expected_delivery_date'])) {
                $purchaseOrder->expected_delivery_date = $data['expected_delivery_date'];
            }

            if (isset($data['notes'])) {
                $purchaseOrder->notes = $data['notes'];
            }

            // Actualizar items si se proporcionan
            if (isset($data['items'])) {
                $purchaseOrder->items()->delete();
                $subtotal = 0;

                foreach ($data['items'] as $itemData) {
                    $product = $this->productRepository->findById($itemData['product_id']);
                    if (!$product) {
                        throw new Exception("Producto no encontrado: {$itemData['product_id']}", 404);
                    }

                    $unitPrice = $itemData['unit_price'] ?? $product->cost ?? $product->price;
                    $totalPrice = $itemData['quantity'] * $unitPrice;

                    $purchaseOrder->items()->create([
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'notes' => $itemData['notes'] ?? null,
                    ]);

                    $subtotal += $totalPrice;
                }

                $purchaseOrder->subtotal = $subtotal;
                $purchaseOrder->total = $subtotal + $purchaseOrder->tax + $purchaseOrder->shipping;
            }

            $purchaseOrder->save();

            return $this->getPurchaseOrder($purchaseOrder->id);
        });
    }

    public function deletePurchaseOrder(int $id)
    {
        $purchaseOrder = $this->getPurchaseOrder($id);

        if (!$purchaseOrder->canBeEdited()) {
            throw new Exception('No se puede eliminar una orden en este estado', 400);
        }

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

                // Actualizar stock del producto
                $this->productManagementService->updateStock($item->product_id, $receivedQuantity);

                if ($item->getPendingQuantity() > 0) {
                    $allReceived = false;
                }
            }

            // Actualizar estado de la orden
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
}