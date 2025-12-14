<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\UserNoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserNoteController extends Controller
{
    public function __construct(
        private UserNoteService $userNoteService
    ) {}

    public function index(Request $request, ?int $userId = null): JsonResponse
    {
        try {
            $notableId = $request->input('notable_id');
            $notableType = $request->input('notable_type');

            if (!$userId && (!$notableId || !$notableType)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Se requiere user_id o notable_id y notable_type.'
                ], 400);
            }

            $notes = $this->userNoteService->getUserNotes($userId, $notableId, $notableType);

            return response()->json([
                'success' => true,
                'data' => $notes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las notas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, ?int $userId = null): JsonResponse
    {
        try {
            $validated = $request->validate([
                'note' => 'required|string|max:1000',
                'is_important' => 'sometimes|boolean',
                'notable_id' => 'sometimes|integer|nullable',
                'notable_type' => 'sometimes|string|nullable'
            ]);

            if (!$userId && (!isset($validated['notable_id']) || !isset($validated['notable_type']))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Se requiere user_id o notable_id y notable_type para crear una nota.'
                ], 400);
            }

            $noteData = array_merge($validated, [
                'user_id' => $userId, // This will be null if not provided in route
                'author_id' => auth()->id()
            ]);

            $result = $this->userNoteService->createNote($noteData);

            return response()->json($result, $result['success'] ? 201 : 422);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la nota: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, ?int $userId = null, int $noteId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'note' => 'sometimes|string|max:1000',
                'is_important' => 'sometimes|boolean',
                'notable_id' => 'sometimes|integer|nullable',
                'notable_type' => 'sometimes|string|nullable'
            ]);

            $result = $this->userNoteService->updateNote($noteId, $validated);

            return response()->json($result, $result['success'] ? 200 : 422);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la nota: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(?int $userId, int $noteId): JsonResponse
    {
        try {
            $result = $this->userNoteService->deleteNote($noteId);

            return response()->json($result, $result['success'] ? 200 : 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la nota: ' . $e->getMessage()
            ], 500);
        }
    }
}