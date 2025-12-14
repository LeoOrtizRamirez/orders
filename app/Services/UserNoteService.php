<?php

namespace App\Services;

use App\Repositories\UserNoteRepository;
use Illuminate\Support\Collection;

class UserNoteService
{
    public function __construct(
        private UserNoteRepository $userNoteRepository
    ) {}

    public function getUserNotes(?int $userId = null, ?int $notableId = null, ?string $notableType = null): Collection
    {
        return $this->userNoteRepository->getNotes($userId, $notableId, $notableType);
    }

    public function createNote(array $noteData): array
    {
        try {
            // Ensure notable_id and notable_type are included if present
            $dataToCreate = array_filter($noteData, function($value) {
                return $value !== null;
            });

            $note = $this->userNoteRepository->createNote($dataToCreate);
            
            return [
                'success' => true,
                'data' => $note->load('author', 'notable'), // Load notable relationship
                'message' => 'Nota creada exitosamente'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al crear la nota: ' . $e->getMessage()
            ];
        }
    }

    public function updateNote(int $noteId, array $noteData): array
    {
        try {
            $note = $this->userNoteRepository->getNoteById($noteId);

            if (!$note) {
                return [
                    'success' => false,
                    'message' => 'Nota no encontrada'
                ];
            }

            if ($note->author_id !== auth()->id()) {
                return [
                    'success' => false,
                    'message' => 'No tienes permiso para actualizar esta nota'
                ];
            }

            $updated = $this->userNoteRepository->updateNote($noteId, $noteData);
            
            if ($updated) {
                $note = $this->userNoteRepository->getNoteById($noteId); // Re-fetch to get updated data
                return [
                    'success' => true,
                    'data' => $note,
                    'message' => 'Nota actualizada exitosamente'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'No se pudo actualizar la nota'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al actualizar la nota: ' . $e->getMessage()
            ];
        }
    }

    public function deleteNote(int $noteId): array
    {
        try {
            $note = $this->userNoteRepository->getNoteById($noteId);

            if (!$note) {
                return [
                    'success' => false,
                    'message' => 'Nota no encontrada'
                ];
            }

            if ($note->author_id !== auth()->id()) {
                return [
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar esta nota'
                ];
            }

            $deleted = $this->userNoteRepository->deleteNote($noteId);
            
            return [
                'success' => $deleted,
                'message' => $deleted ? 'Nota eliminada exitosamente' : 'No se pudo eliminar la nota'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al eliminar la nota: ' . $e->getMessage()
            ];
        }
    }

    public function getImportantNotes(int $userId): Collection
    {
        return $this->userNoteRepository->getNotes($userId, null, null, true);
    }
}