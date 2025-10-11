<?php

namespace App\Repositories;

use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierRepository
{
    public function __construct(private Supplier $model) {}

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->withCount('purchaseOrders');

        // Filtrar por estado activo
        if (isset($filters['active'])) {
            $query->where('is_active', $filters['active']);
        }

        // Buscar por nombre
        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('contact_person', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        return $query->ordered()->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id): ?Supplier
    {
        return $this->model->withCount('purchaseOrders')->find($id);
    }

    public function create(array $data): Supplier
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $supplier = $this->findById($id);
        
        if (!$supplier) {
            return false;
        }

        return $supplier->update($data);
    }

    public function delete(int $id): bool
    {
        $supplier = $this->findById($id);
        
        if (!$supplier) {
            return false;
        }

        return $supplier->delete();
    }

    public function getActiveSuppliers()
    {
        return $this->model->active()->withCount('purchaseOrders')->ordered()->get();
    }

    public function getSuppliersForSelect()
    {
        return $this->model->active()->ordered()->get(['id', 'name']);
    }
}