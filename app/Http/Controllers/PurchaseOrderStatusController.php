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

    

        /**

         * Actualiza el estado de una orden de compra

         */

        public function updateStatus(Request $request, PurchaseOrder $purchaseOrder): JsonResponse

        {

            $request->validate([

                'status' => 'required|string|in:draft,pending,approved,ordered,received,rejected,cancelled'

            ]);

    

            $newStatus = $request->input('status');

            $userId = auth()->id();

            $success = false;

            $message = '';

    

            try {

                switch ($newStatus) {

                    case 'draft':

                        if (in_array($purchaseOrder->status, ['pending', 'approved', 'ordered', 'received', 'rejected', 'cancelled'])) {

                            // Direct status update for simplicity, assuming this is an admin action to revert

                            $purchaseOrder->status = 'draft';

                            $purchaseOrder->save();

                            $success = true;

                            $message = 'Orden actualizada a borrador correctamente.';

                        } else {

                            throw new \Exception('Transición a borrador no permitida desde el estado actual.');

                        }

                        break;

                    case 'pending':

                        if ($purchaseOrder->status === 'draft') {

                            $success = $purchaseOrder->submit();

                            $message = 'Orden enviada a aprobación correctamente.';

                        } elseif (in_array($purchaseOrder->status, ['rejected', 'cancelled'])) {

                            $success = $purchaseOrder->reopen();

                            $message = 'Orden reabierta y puesta en estado pendiente correctamente.';

                        } else {

                            throw new \Exception('Transición a pendiente no permitida desde el estado actual.');

                        }

                        break;

                    case 'approved':

                        $success = $purchaseOrder->approve($userId);

                        $message = 'Orden aprobada correctamente.';

                        break;

                    case 'ordered':

                        $success = $purchaseOrder->markAsOrdered();

                        $message = 'Orden marcada como enviada al proveedor.';

                        break;

                    case 'received':

                        // Special case: Receiving requires items. Not directly supported by simple drag-and-drop.

                        throw new \Exception('Para marcar una orden como "recibida", use la acción específica que requiere detalles de los ítems.');

                    case 'rejected':

                        // Special case: Rejecting requires a reason. Not directly supported by simple drag-and-drop.

                        throw new \Exception('Para rechazar una orden, use la acción específica que requiere una razón de rechazo.');

                    case 'cancelled':

                        // Special case: Cancelling usually requires a reason. Not directly supported by simple drag-and-drop.

                        throw new \Exception('Para cancelar una orden, use la acción específica que requiere una razón de cancelación.');

                    default:

                        throw new \Exception('Estado no válido o transición no soportada.');

                }

    

                if (!$success) {

                    throw new \Exception('No se pudo actualizar el estado de la orden. Verifique las condiciones de transición.');

                }

    

                return response()->json([

                    'success' => true,

                    'message' => $message,

                    'order' => $purchaseOrder->fresh()

                ]);

    

            } catch (\Exception $e) {

                return response()->json([

                    'success' => false,

                    'message' => $e->getMessage()

                ], 422);

            }

        }

    }

    