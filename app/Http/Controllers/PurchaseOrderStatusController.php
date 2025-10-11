<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PurchaseOrderStatusController extends Controller
{
    /**
     * Enviar borrador a aprobación
     */
    public function submit(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        if (!$purchaseOrder->submit()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede enviar esta orden a aprobación en su estado actual.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden enviada a aprobación correctamente.',
            'order' => $purchaseOrder->fresh()
        ]);
    }

    /**
     * Aprobar una orden de compra
     */
    public function approve(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        if (!$purchaseOrder->approve(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede aprobar esta orden en su estado actual.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden aprobada correctamente.',
            'order' => $purchaseOrder->fresh()
        ]);
    }

    /**
     * Rechazar una orden de compra
     */
    public function reject(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        if (!$purchaseOrder->reject($request->rejection_reason, auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede rechazar esta orden en su estado actual.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden rechazada correctamente.',
            'order' => $purchaseOrder->fresh()
        ]);
    }

    /**
     * Marcar orden como enviada al proveedor
     */
    public function markOrdered(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        if (!$purchaseOrder->markAsOrdered()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede marcar como ordenada en su estado actual.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden marcada como enviada al proveedor.',
            'order' => $purchaseOrder->fresh()
        ]);
    }

    /**
     * Recibir una orden
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        if (!$purchaseOrder->receive()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede recibir esta orden en su estado actual.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden recibida correctamente.',
            'order' => $purchaseOrder->fresh()
        ]);
    }

    /**
     * Cancelar una orden
     */
    public function cancel(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        if (!$purchaseOrder->cancel($request->reason)) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede cancelar esta orden en su estado actual.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden cancelada correctamente.',
            'order' => $purchaseOrder->fresh()
        ]);
    }

    /**
     * Reabrir una orden
     */
    public function reopen(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        if (!$purchaseOrder->reopen()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede reabrir esta orden en su estado actual.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden reabierta correctamente.',
            'order' => $purchaseOrder->fresh()
        ]);
    }
}