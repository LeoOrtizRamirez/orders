<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductManagementService
{
    public function __construct(private ProductRepository $productRepository) {}

    public function getAllProducts(array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->getAll($filters);
    }

    public function getProduct(int $id)
    {
        $product = $this->productRepository->findById($id);
        
        if (!$product) {
            throw new Exception('Producto no encontrado', 404);
        }

        return $product;
    }

    public function getProductBySku(string $sku)
    {
        $product = $this->productRepository->findBySku($sku);
        
        if (!$product) {
            throw new Exception('Producto no encontrado', 404);
        }

        return $product;
    }

    public function createProduct(array $data)
    {
        // Generar SKU automáticamente si no se proporciona
        if (!isset($data['sku'])) {
            $data['sku'] = $this->generateSku($data['name']);
        }

        // Validar SKU único
        $existingProduct = $this->productRepository->findBySku($data['sku']);
        if ($existingProduct) {
            throw new Exception('El SKU ya está en uso', 400);
        }

        return $this->productRepository->create($data);
    }

    public function updateProduct(int $id, array $data)
    {
        $product = $this->getProduct($id);

        // Si se actualiza el SKU, validar que sea único
        if (isset($data['sku']) && $data['sku'] !== $product->sku) {
            $existingProduct = $this->productRepository->findBySku($data['sku']);
            if ($existingProduct && $existingProduct->id != $id) {
                throw new Exception('El SKU ya está en uso', 400);
            }
        }

        $updated = $this->productRepository->update($id, $data);
        
        if (!$updated) {
            throw new Exception('Error al actualizar el producto', 500);
        }

        return $this->getProduct($id);
    }

    public function deleteProduct(int $id)
    {
        $product = $this->getProduct($id);
        
        // Verificar si tiene órdenes de compra asociadas
        if ($product->purchase_order_items_count > 0) {
            throw new Exception('No se puede eliminar el producto porque tiene órdenes de compra asociadas', 400);
        }

        $deleted = $this->productRepository->delete($id);
        
        if (!$deleted) {
            throw new Exception('Error al eliminar el producto', 500);
        }

        return true;
    }

    public function toggleProductStatus(int $id)
    {
        $product = $this->getProduct($id);
        
        $newStatus = !$product->is_active;
        
        $this->productRepository->update($id, ['is_active' => $newStatus]);
        
        return $this->getProduct($id);
    }

    public function getActiveProducts()
    {
        return $this->productRepository->getActiveProducts();
    }

    public function getLowStockProducts()
    {
        return $this->productRepository->getLowStockProducts();
    }

    public function getCategories()
    {
        return $this->productRepository->getCategories();
    }

    public function updateStock(int $id, int $quantity)
    {
        $product = $this->getProduct($id);
        
        $updated = $product->updateStock($quantity);
        
        if (!$updated) {
            throw new Exception('Error al actualizar el stock', 500);
        }

        return $this->getProduct($id);
    }

    private function generateSku(string $name): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3));
        $timestamp = time();
        return $prefix . '-' . substr($timestamp, -6);
    }
}