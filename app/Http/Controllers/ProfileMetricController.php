<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProfileMetricService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileMetricController extends Controller
{
    public function __construct(
        private ProfileMetricService $profileMetricService
    ) {}

    public function getUserMetrics(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Determinar el usuario objetivo
            $targetUserId = $request->get('user_id');
            
            // Si es admin y se especifica un user_id, usar ese usuario
            if ($user->hasRole('admin') && $targetUserId) {
                $finalUserId = (int) $targetUserId;
            } else {
                // Si no es admin o no se especifica user_id, usar el usuario autenticado
                $finalUserId = $user->id;
            }

            $period = $request->get('period', 'monthly');
            $year = (int) $request->get('year', date('Y'));
            $month = $request->get('month') ? (int) $request->get('month') : null;

            $metrics = $this->profileMetricService->getMetricsForCharts($finalUserId, $period, $year, $month);

            return response()->json([
                'success' => true,
                'data' => $metrics,
                'filters' => [
                    'user_id' => $finalUserId,
                    'period' => $period,
                    'year' => $year,
                    'month' => $month,
                    'is_admin_view' => $user->hasRole('admin') && $targetUserId
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en getUserMetrics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las métricas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUsersList(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if (!$user || !$user->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }

            $users = $this->profileMetricService->getUsersWithMetrics();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en getUsersList: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la lista de usuarios: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getGlobalMetrics(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if (!$user || !$user->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }

            $period = $request->get('period', 'monthly');
            $year = (int) $request->get('year', date('Y'));
            $month = $request->get('month') ? (int) $request->get('month') : null;

            $metrics = $this->profileMetricService->getGlobalMetrics($period, $year, $month);

            return response()->json([
                'success' => true,
                'data' => $metrics,
                'filters' => [
                    'period' => $period,
                    'year' => $year,
                    'month' => $month
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en getGlobalMetrics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las métricas globales: ' . $e->getMessage()
            ], 500);
        }
    }
}