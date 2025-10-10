<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ServiceManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
    public function __construct(private ServiceManagementService $serviceService)
    {
        $this->middleware('permission:view services')->only(['index', 'show']);
        $this->middleware('permission:create services')->only(['store']);
        $this->middleware('permission:edit services')->only(['update']);
        $this->middleware('permission:delete services')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'active', 'per_page']);
            $perPage = $request->get('per_page', 15);
            $filters['per_page'] = $perPage;

            $services = $this->serviceService->getAllServices($filters);
            
            return response()->json([
                'data' => $services->items(),
                'meta' => [
                    'current_page' => $services->currentPage(),
                    'last_page' => $services->lastPage(),
                    'per_page' => $services->perPage(),
                    'total' => $services->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load services',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'sometimes|string|max:255|unique:services,slug',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'duration_minutes' => 'required|integer|min:1',
                'is_active' => 'sometimes|boolean',
                'order' => 'sometimes|integer',
                'icon' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:7',
                'whatsapp_url' => 'nullable|string',
            ]);

            $service = $this->serviceService->createService($validated);

            return response()->json($service, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create service',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $service = $this->serviceService->getService($id);
            
            return response()->json($service);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Service not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'slug' => 'sometimes|string|max:255|unique:services,slug,' . $id,
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric|min:0',
                'duration_minutes' => 'sometimes|integer|min:1',
                'is_active' => 'sometimes|boolean',
                'order' => 'sometimes|integer',
                'icon' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:7',
                'whatsapp_url' => 'nullable|string',
            ]);

            $service = $this->serviceService->updateService($id, $validated);

            return response()->json($service);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to update service',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->serviceService->deleteService($id);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to delete service',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function toggleStatus($id): JsonResponse
    {
        try {
            $service = $this->serviceService->toggleServiceStatus($id);

            return response()->json($service);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => 'Failed to toggle service status',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function activeServices(): JsonResponse
    {
        try {
            $services = $this->serviceService->getActiveServices();

            return response()->json([
                'data' => $services
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load active services',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getServiceBySlug($slug): JsonResponse
    {
        try {
            $service = $this->serviceService->getServiceBySlug($slug);
            
            return response()->json($service);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Service not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }
}