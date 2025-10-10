<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\VideoManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function __construct(private VideoManagementService $videoService) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['service_id', 'active', 'free', 'per_page']);
            $videos = $this->videoService->getAllVideos($filters);
            
            return response()->json([
                'success' => true,
                'data' => $videos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'service_id' => 'required|exists:services,id',
                'title' => 'required|string|max:255',
                'slug' => 'sometimes|string|max:255|unique:videos,slug',
                'description' => 'nullable|string',
                'video_url' => 'required|url',
                'thumbnail_url' => 'nullable|url',
                'duration_seconds' => 'required|integer|min:0',
                'order' => 'sometimes|integer',
                'is_free' => 'sometimes|boolean',
                'tags' => 'nullable|array'
            ]);

            $video = $this->videoService->createVideo($validated);
            
            return response()->json([
                'success' => true,
                'data' => $video,
                'message' => 'Vídeo creado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $video = $this->videoService->getVideo($id);
            
            return response()->json([
                'success' => true,
                'data' => $video
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'service_id' => 'sometimes|exists:services,id',
                'title' => 'sometimes|string|max:255',
                'slug' => 'sometimes|string|max:255|unique:videos,slug,' . $id,
                'description' => 'nullable|string',
                'video_url' => 'sometimes|url',
                'thumbnail_url' => 'nullable|url',
                'duration_seconds' => 'sometimes|integer|min:0',
                'order' => 'sometimes|integer',
                'is_free' => 'sometimes|boolean',
                'is_active' => 'sometimes|boolean',
                'tags' => 'nullable|array'
            ]);

            $video = $this->videoService->updateVideo($id, $validated);
            
            return response()->json([
                'success' => true,
                'data' => $video,
                'message' => 'Vídeo actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->videoService->deleteVideo($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Vídeo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $video = $this->videoService->toggleVideoStatus($id);
            
            return response()->json([
                'success' => true,
                'data' => $video,
                'message' => 'Estado del vídeo actualizado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function byService($serviceId): JsonResponse
    {
        try {
            $videos = $this->videoService->getVideosByService($serviceId);
            
            return response()->json([
                'data' => $videos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load videos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function freeVideos(): JsonResponse
    {
        try {
            $videos = $this->videoService->getFreeVideos();
            
            return response()->json([
                'success' => true,
                'data' => $videos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}