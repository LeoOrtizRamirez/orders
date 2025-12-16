<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function __construct(private Product $model) {}

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->withCount('purchaseOrderItems');

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

        if (isset($filters['low_stock'])) {
            $query->where('stock', '<=', 'reorder_point');
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
}