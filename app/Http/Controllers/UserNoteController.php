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

    public function index(Request $request, int $userId): JsonResponse
    {
        try {
            $notes = $this->userNoteService->getUserNotes($userId);

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

    public function store(Request $request, int $userId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'note' => 'required|string|max:1000',
                'is_important' => 'sometimes|boolean'
            ]);

            $noteData = array_merge($validated, [
                'user_id' => $userId,
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

    public function update(Request $request, int $userId, int $noteId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'note' => 'sometimes|string|max:1000',
                'is_important' => 'sometimes|boolean'
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

    public function destroy(int $userId, int $noteId): JsonResponse
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