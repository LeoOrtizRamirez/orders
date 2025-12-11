<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile; // Added

use App\Imports\ProductsStockImport;
use Maatwebsite\Excel\Facades\Excel;

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

    public function importProducts(UploadedFile $file): array
    {
        $importedCount = 0;
        $updatedCount = 0;
        $failedCount = 0;
        $errors = [];

        if (($handle = fopen($file->getPathname(), 'r')) !== FALSE) {
            $header = fgetcsv($handle, 1000, ','); // Read header row
            $expectedHeaders = ['name', 'sku', 'description', 'stock', 'min_stock', 'reorder_point', 'unit', 'category', 'brand', 'supplier', 'is_active', 'order'];

            // Basic header validation
            if (array_diff($expectedHeaders, $header)) {
                throw new Exception('Invalid CSV header. Expected: ' . implode(', ', $expectedHeaders));
            }

            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                // Combine header with row data
                $data = array_combine($header, $row);

                try {
                    // Basic validation for required fields
                    if (empty($data['name']) || empty($data['sku'])) {
                        throw new Exception('Product name and SKU are required.');
                    }

                    $productData = [
                        'name' => $data['name'],
                        'sku' => $data['sku'],
                        'description' => $data['description'] ?? null,
                        'stock' => (int)($data['stock'] ?? 0),
                        'min_stock' => (int)($data['min_stock'] ?? 0),
                        'reorder_point' => (int)($data['reorder_point'] ?? 0),
                        'unit' => $data['unit'] ?? 'unit',
                        'category' => $data['category'] ?? null,
                        'brand' => $data['brand'] ?? null,
                        'supplier' => $data['supplier'] ?? null,
                        'is_active' => (bool)($data['is_active'] ?? true),
                        'order' => (int)($data['order'] ?? 0),
                    ];

                    $existingProduct = $this->productRepository->findBySku($productData['sku']);

                    if ($existingProduct) {
                        // Update existing product
                        $this->productRepository->update($existingProduct->id, $productData);
                        $updatedCount++;
                    } else {
                        // Create new product
                        $this->productRepository->create($productData);
                        $importedCount++;
                    }
                } catch (Exception $e) {
                    $failedCount++;
                    $errors[] = ['row' => $data, 'error' => $e->getMessage()];
                }
            }
            fclose($handle);
        } else {
            throw new Exception('Could not open CSV file.');
        }

                return [

                    'imported' => $importedCount,

                    'updated' => $updatedCount,

                    'failed' => $failedCount,

                    'errors' => $errors,

                ];

            }

        }

        