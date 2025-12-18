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

                // BOM handling for UTF-8 files created in Excel

                if (isset($header[0]) && str_starts_with($header[0], "\xEF\xBB\xBF")) {

                    $header[0] = substr($header[0], 3);

                }

                

                $expectedHeaders = ['ITEM', 'DESCRIPCION', 'LINEA', 'TIPOFACTOR', 'ONHAND'];

    

                // Basic header validation

                if (array_diff($expectedHeaders, $header)) {

                    throw new Exception('Invalid CSV header. Expected: ' . implode(', ', $expectedHeaders));

                }

    

                while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {

                    // Combine header with row data

                    if (count($header) !== count($row)) {

                        $failedCount++;

                        $errors[] = ['row' => $row, 'error' => 'Row column count mismatch header count'];

                        continue;

                    }

                    

                    $data = array_combine($header, $row);

    

                    try {

                        // Basic validation for required fields

                        if (empty($data['DESCRIPCION']) || empty($data['ITEM'])) {

                            throw new Exception('Product name (DESCRIPCION) and SKU (ITEM) are required.');

                        }

    

                        // Normalize Category (LINEA) from Code (01, 02) to Enum Value

                        $lineaCode = isset($data['LINEA']) ? trim((string)$data['LINEA']) : null;

                        $categoryMap = [

                            '01' => 'FRUTAS',

                            '02' => 'VERDURAS',

                            '03' => 'TUBERCULOS',

                            '04' => 'JUGOS',

                            '05' => 'PULPAS',

                            '99' => 'OTROS',

                        ];

                        $category = $categoryMap[$lineaCode] ?? 'OTROS';

    

                        // Normalize Unit to uppercase if needed (e.g. 'Unidad' -> 'UNIDAD')

                        $unit = isset($data['TIPOFACTOR']) ? strtoupper($data['TIPOFACTOR']) : 'UNIDAD';

    

                        $productData = [

                            'name' => $data['DESCRIPCION'],

                            'sku' => $data['ITEM'],

                            'description' => null,

                            'stock' => (int)($data['ONHAND'] ?? 0),

                            'min_stock' => 5,

                            'reorder_point' => 10,

                            'unit' => $unit,

                            'category' => $category,

                            'brand' => null,

                            'supplier' => null,

                            'is_active' => true,

                            'order' => 0,

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

    

        public function getPendingOrdersForProduct(int $productId)

        {

            return $this->productRepository->getPendingOrders($productId);

        }

    }

        