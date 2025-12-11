<?php

namespace App\Http\Controllers;

use App\Services\ProductManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse; // Added

class ProductController extends Controller
{
    public function __construct(private ProductManagementService $productService)
    {
        $this->middleware('permission:view products')->only(['index', 'show']);
        $this->middleware('permission:create products')->only(['store']);
        $this->middleware('permission:edit products')->only(['update']);
        $this->middleware('permission:delete products')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'active', 'category', 'low_stock', 'per_page']);
            $perPage = $request->get('per_page', 15);
            $filters['per_page'] = $perPage;

            $products = $this->productService->getAllProducts($filters);
            
            return response()->json([
                'data' => $products->items(),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load products',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'sometimes|string|max:255|unique:products,sku',
                'description' => 'nullable|string',
                'stock' => 'sometimes|integer|min:0',
                'min_stock' => 'sometimes|integer|min:0',
                'reorder_point' => 'sometimes|integer|min:0',
                'unit' => 'nullable|string|max:50',
                'category' => 'nullable|string|max:100',
                'brand' => 'nullable|string|max:100',
                'supplier' => 'nullable|string|max:100',
                'specifications' => 'nullable|array',
                'is_active' => 'sometimes|boolean',
                'order' => 'sometimes|integer',
            ]);

            $product = $this->productService->createProduct($validated);

            return response()->json($product, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create product',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $product = $this->productService->getProduct($id);
            
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Product not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'sku' => 'sometimes|string|max:255|unique:products,sku,' . $id,
                'description' => 'nullable|string',
                'stock' => 'sometimes|integer|min:0',
                'min_stock' => 'sometimes|integer|min:0',
                'reorder_point' => 'sometimes|integer|min:0',
                'unit' => 'nullable|string|max:50',
                'category' => 'nullable|string|max:100',
                'brand' => 'nullable|string|max:100',
                'supplier' => 'nullable|string|max:100',
                'specifications' => 'nullable|array',
                'is_active' => 'sometimes|boolean',
                'order' => 'sometimes|integer',
            ]);

            $product = $this->productService->updateProduct($id, $validated);

            return response()->json($product);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to update product',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to delete product',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function toggleStatus($id): JsonResponse
    {
        try {
            $product = $this->productService->toggleProductStatus($id);

            return response()->json($product);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to toggle product status',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function activeProducts(): JsonResponse
    {
        try {
            $products = $this->productService->getActiveProducts();

            return response()->json([
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load active products',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function lowStockProducts(): JsonResponse
    {
        try {
            $products = $this->productService->getLowStockProducts();

            return response()->json([
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load low stock products',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function categories(): JsonResponse
    {
        try {
            $categories = $this->productService->getCategories();

            return response()->json([
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load categories',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getProductBySku($sku): JsonResponse
    {
        try {
            $product = $this->productService->getProductBySku($sku);
            
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Product not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function importProductsCsv(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt',
            ]);

            $result = $this->productService->importProducts($request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Importación de productos completada.',
                'imported' => $result['imported'],
                'updated' => $result['updated'],
                'failed' => $result['failed'],
                'errors' => $result['errors'] ?? [],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to import products',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadProductsCsvTemplate(): BinaryFileResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products_template.csv"',
        ];

        $templateContent = "name,sku,description,stock,min_stock,reorder_point,unit,category,brand,is_active,order\n";
        $templateContent .= "Example Product,PROD001,Description for example product,100,10,5,unit,Electronics,BrandX,1,1\n";

        // Create a temporary file and write the content to it
        $tempFilePath = tempnam(sys_get_temp_dir(), 'products_template_');
        file_put_contents($tempFilePath, $templateContent);

        return response()->download($tempFilePath, 'products_template.csv', $headers)->deleteFileAfterSend(true);
    }
}