<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\PurchaseOrderManagementService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\SplitPurchaseOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\PurchaseOrder; // Import PurchaseOrder model

class PurchaseOrderController extends Controller
{
    public function __construct(private PurchaseOrderManagementService $purchaseOrderService)
    {
        $this->middleware('permission:view purchase_orders')->only(['index', 'show']);
        $this->middleware('permission:create purchase_orders')->only(['store']);
        $this->middleware('permission:edit purchase_orders')->only(['update']);
        $this->middleware('permission:delete purchase_orders')->only(['destroy']);
        $this->middleware('permission:approve purchase_orders')->only(['approve', 'reject']);
        $this->middleware('permission:receive purchase_orders')->only(['receive']);
        $this->middleware('permission:create purchase_orders')->only(['split']); // Add middleware for split
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'status', 'supplier_id', 'date_from', 'date_to', 'per_page']);
            $perPage = $request->get('per_page', 15);
            $filters['per_page'] = $perPage;

            $purchaseOrders = $this->purchaseOrderService->getAllPurchaseOrders($filters);
            
            // Load sub-order count for each order in the current page
            $purchaseOrders->getCollection()->each(function ($order) {
                $order->loadCount('subOrders');
            });

            return response()->json([
                'data' => $purchaseOrders->items(),
                'meta' => [
                    'current_page' => $purchaseOrders->currentPage(),
                    'last_page' => $purchaseOrders->lastPage(),
                    'per_page' => $purchaseOrders->perPage(),
                    'total' => $purchaseOrders->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load purchase orders',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'expected_delivery_date' => 'nullable|date',
                'notes' => 'nullable|string', // Keep for main order, service will handle
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|min:0',//PENDIENTE DESCOMENTAR CAMBIAR A 1
                'items.*.notes' => 'nullable|string', // Removed, handled by service as UserNote
                'items.*.checked' => 'nullable|boolean',
            ]);

            $purchaseOrder = $this->purchaseOrderService->createPurchaseOrder($validated, $request->user());

            return response()->json($purchaseOrder, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create purchase order',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $purchaseOrder = $this->purchaseOrderService->getPurchaseOrder((int)$id);
            
            return response()->json($purchaseOrder);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Purchase order not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'supplier_id' => 'sometimes|exists:suppliers,id',
                'expected_delivery_date' => 'nullable|date',
                'notes' => 'nullable|string',
                'items' => 'sometimes|array|min:1',
                'items.*.id' => 'nullable|integer|exists:purchase_order_items,id',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.notes' => 'nullable|string',
                'items.*.checked' => 'nullable|boolean',
            ]);

            $purchaseOrder = $this->purchaseOrderService->updatePurchaseOrder((int)$id, $validated, $request->user());

            return response()->json($purchaseOrder);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to update purchase order',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

        public function destroy($id): JsonResponse
    {
        try {
            $this->purchaseOrderService->deletePurchaseOrder((int)$id);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to delete purchase order',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function split(SplitPurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        try {
            // The request is already validated by SplitPurchaseOrderRequest
            $subOrder = $this->purchaseOrderService->splitPurchaseOrder(
                $purchaseOrder,
                $request->validated('items'),
                $request->validated('expected_delivery_date'),
                $request->validated('notes'),
                $request->user()
            );

            return response()->json($subOrder, 201);
        } catch (\Exception $e) {
            $exceptionCode = $e->getCode();
            // Ensure status code is a valid HTTP status code (100-599)
            $statusCode = ($exceptionCode >= 100 && $exceptionCode < 600) ? $exceptionCode : 500;
            return response()->json([
                'error' => 'Failed to split purchase order',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }
}