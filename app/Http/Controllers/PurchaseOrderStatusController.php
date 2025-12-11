<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\PurchaseOrderManagementService; // Import the service

class PurchaseOrderStatusController extends Controller
{
    protected $purchaseOrderService;

    public function __construct(PurchaseOrderManagementService $purchaseOrderService)
    {
        $this->purchaseOrderService = $purchaseOrderService;
    }

    /**
     * Actualiza el estado de una orden de compra
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $request->validate([
            'status' => 'required|string', // 'in' validation removed as per new requirement
        ]);

        $newStatus = $request->input('status');
        $userId = auth()->id();

        try {
            $updatedOrder = $this->purchaseOrderService->updateStatus($purchaseOrder->id, $newStatus, $userId);

            return response()->json([
                'success' => true,
                'message' => "Estado de la orden actualizado a '{$newStatus}' correctamente.",
                'order' => $updatedOrder->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}