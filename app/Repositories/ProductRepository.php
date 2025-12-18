<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\PurchaseOrder; // Added import
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function __construct(private Product $model) {}

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->withCount('purchaseOrderItems');

        // Calcular Stock Comprometido (Requerido en órdenes activas antes de facturación)
        $query->withSum(['purchaseOrderItems as committed_stock' => function($q) {
            $q->whereHas('purchaseOrder', function($orderQuery) {
                $orderQuery->whereIn('status', [
                    'nuevo pedido', 
                    'disponibilidad', 
                    'preparar pedido', 
                    'en preparación'
                ]);
            });
        }], 'quantity');

        if (isset($filters['active'])) {
            $query->where('is_active', $filters['active']);
        }

        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('sku', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['unit'])) {
            $query->where('unit', $filters['unit']);
        }

        if (isset($filters['stock_status'])) {
            if ($filters['stock_status'] === 'low') {
                $query->whereColumn('stock', '<=', 'reorder_point')
                      ->where('stock', '>', 0);
            } elseif ($filters['stock_status'] === 'out') {
                $query->where('stock', 0);
            }
        }

        return $query->ordered()->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id): ?Product
    {
        return $this->model->withCount('purchaseOrderItems')->find($id);
    }

    public function findBySku(string $sku): ?Product
    {
        return $this->model->where('sku', $sku)->first();
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $product = $this->findById($id);
        
        if (!$product) {
            return false;
        }

        return $product->update($data);
    }

    public function delete(int $id): bool
    {
        $product = $this->findById($id);
        
        if (!$product) {
            return false;
        }

        return $product->delete();
    }

    public function getActiveProducts()
    {
        return $this->model->where('is_active', true)->orderBy('name', 'asc')->get();
    }

    public function getLowStockProducts()
    {
        return $this->model->active()->lowStock()->ordered()->get();
    }

    public function getCategories()
    {
        return $this->model->active()->distinct()->pluck('category')->filter();
    }

    public function updateStock(int $id, int $quantity): bool
    {
        $product = $this->findById($id);
        
        if (!$product) {
            return false;
        }

        $product->stock += $quantity;
        return $product->save();
    }

    public function getPendingOrders(int $productId)
    {
        return PurchaseOrder::query()
            ->whereIn('status', [
                'nuevo pedido', 
                'disponibilidad', 
                'preparar pedido', 
                'en preparación'
            ])
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->with(['supplier:id,name', 'items' => function ($query) use ($productId) {
                $query->where('product_id', $productId)->select('id', 'purchase_order_id', 'quantity');
            }])
            ->select('id', 'order_number', 'status', 'order_date', 'supplier_id', 'created_by')
            ->orderBy('order_date', 'asc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'date' => $order->order_date ? $order->order_date->format('Y-m-d') : null,
                    'supplier' => $order->supplier->name ?? 'N/A',
                    'quantity_requested' => $order->items->first()->quantity ?? 0,
                ];
            });
    }
}