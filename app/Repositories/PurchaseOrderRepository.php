<?php

namespace App\Repositories;

use App\Models\PurchaseOrder;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseOrderRepository
{
    public function __construct(private PurchaseOrder $model) {}

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['supplier', 'creator', 'items.product']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('order_number', 'like', "%{$filters['search']}%")
                  ->orWhereHas('supplier', function($q) use ($filters) {
                      $q->where('name', 'like', "%{$filters['search']}%");
                  });
            });
        }

        if (isset($filters['date_from'])) {
            $query->where('order_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('order_date', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id): ?PurchaseOrder
    {
        return $this->model->with(['supplier', 'creator', 'approver', 'items.product'])->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?PurchaseOrder
    {
        return $this->model->with(['supplier', 'creator', 'items.product'])->where('order_number', $orderNumber)->first();
    }

    public function create(array $data): PurchaseOrder
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $purchaseOrder = $this->findById($id);
        
        if (!$purchaseOrder) {
            return false;
        }

        return $purchaseOrder->update($data);
    }

    public function delete(int $id): bool
    {
        $purchaseOrder = $this->findById($id);
        
        if (!$purchaseOrder) {
            return false;
        }

        return $purchaseOrder->delete();
    }

    public function getPendingOrders()
    {
        return $this->model->pending()->with(['supplier', 'creator'])->latest()->get();
    }

    public function generateOrderNumber(): string
    {
        $prefix = 'PO-' . date('YmdHis');
        $lastOrder = $this->model->where('order_number', 'like', $prefix . '%')->latest()->first();
        
        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . '-' . $newNumber;
    }
}