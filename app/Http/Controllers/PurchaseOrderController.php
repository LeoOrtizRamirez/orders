<?php

namespace App\Http\Controllers;

use App\Services\PurchaseOrderManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'status', 'supplier_id', 'date_from', 'date_to', 'per_page']);
            $perPage = $request->get('per_page', 15);
            $filters['per_page'] = $perPage;

            $purchaseOrders = $this->purchaseOrderService->getAllPurchaseOrders($filters);
            
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
                'notes' => 'nullable|string',
                'tax' => 'sometimes|numeric|min:0',
                'shipping' => 'sometimes|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'sometimes|numeric|min:0',
                'items.*.notes' => 'nullable|string',
            ]);

            $purchaseOrder = $this->purchaseOrderService->createPurchaseOrder($validated, $request->user()->id);

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
                'tax' => 'sometimes|numeric|min:0',
                'shipping' => 'sometimes|numeric|min:0',
                'items' => 'sometimes|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'sometimes|numeric|min:0',
                'items.*.notes' => 'nullable|string',
            ]);

            $purchaseOrder = $this->purchaseOrderService->updatePurchaseOrder((int)$id, $validated);

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

    public function approve($id, Request $request): JsonResponse
    {
        try {
            $purchaseOrder = $this->purchaseOrderService->approvePurchaseOrder($id, $request->user()->id);

            return response()->json($purchaseOrder);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to approve purchase order',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function reject($id, Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|min:10',
            ]);

            $purchaseOrder = $this->purchaseOrderService->rejectPurchaseOrder($id, $validated['reason'], $request->user()->id);

            return response()->json($purchaseOrder);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to reject purchase order',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function markAsOrdered($id): JsonResponse
    {
        try {
            $purchaseOrder = $this->purchaseOrderService->markAsOrdered($id);

            return response()->json($purchaseOrder);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to mark purchase order as ordered',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function receive($id, Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:purchase_order_items,id',
                'items.*.received_quantity' => 'required|integer|min:1',
            ]);

            $purchaseOrder = $this->purchaseOrderService->receivePurchaseOrder($id, $validated['items']);

            return response()->json($purchaseOrder);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to receive purchase order',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function pending(): JsonResponse
    {
        try {
            $purchaseOrders = $this->purchaseOrderService->getPendingOrders();

            return response()->json([
                'data' => $purchaseOrders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load pending purchase orders',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}