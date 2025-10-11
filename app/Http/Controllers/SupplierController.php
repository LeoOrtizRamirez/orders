<?php

namespace App\Http\Controllers;

use App\Services\SupplierManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    public function __construct(private SupplierManagementService $supplierService)
    {
        $this->middleware('permission:view suppliers')->only(['index', 'show']);
        $this->middleware('permission:create suppliers')->only(['store']);
        $this->middleware('permission:edit suppliers')->only(['update']);
        $this->middleware('permission:delete suppliers')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'active', 'per_page']);
            $perPage = $request->get('per_page', 15);
            $filters['per_page'] = $perPage;

            $suppliers = $this->supplierService->getAllSuppliers($filters);
            
            return response()->json([
                'data' => $suppliers->items(),
                'meta' => [
                    'current_page' => $suppliers->currentPage(),
                    'last_page' => $suppliers->lastPage(),
                    'per_page' => $suppliers->perPage(),
                    'total' => $suppliers->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load suppliers',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'tax_id' => 'nullable|string|max:50',
                'payment_terms' => 'nullable|string',
                'notes' => 'nullable|string',
                'is_active' => 'sometimes|boolean',
            ]);

            $supplier = $this->supplierService->createSupplier($validated);

            return response()->json($supplier, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create supplier',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $supplier = $this->supplierService->getSupplier((int)$id);
            
            return response()->json($supplier);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Supplier not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'tax_id' => 'nullable|string|max:50',
                'payment_terms' => 'nullable|string',
                'notes' => 'nullable|string',
                'is_active' => 'sometimes|boolean',
            ]);

            $supplier = $this->supplierService->updateSupplier((int)$id, $validated);

            return response()->json($supplier);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to update supplier',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->supplierService->deleteSupplier((int)$id);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to delete supplier',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function toggleStatus($id): JsonResponse
    {
        try {
            $supplier = $this->supplierService->toggleSupplierStatus((int)$id);

            return response()->json($supplier);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to toggle supplier status',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function activeSuppliers(): JsonResponse
    {
        try {
            $suppliers = $this->supplierService->getActiveSuppliers();

            return response()->json([
                'data' => $suppliers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load active suppliers',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function forSelect(): JsonResponse
    {
        try {
            $suppliers = $this->supplierService->getSuppliersForSelect();

            return response()->json([
                'data' => $suppliers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load suppliers for select',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}