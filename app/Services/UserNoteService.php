<?php

namespace App\Services;

use App\Repositories\UserNoteRepository;
use Illuminate\Support\Collection;

class UserNoteService
{
    public function __construct(
        private UserNoteRepository $userNoteRepository
    ) {}

    public function getUserNotes(int $userId): Collection
    {
        return $this->userNoteRepository->getNotesByUserId($userId);
    }

    public function createNote(array $noteData): array
    {
        try {
            $note = $this->userNoteRepository->createNote($noteData);
            
            return [
                'success' => true,
                'data' => $note->load('author'),
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
            $updated = $this->userNoteRepository->updateNote($noteId, $noteData);
            
            if ($updated) {
                $note = $this->userNoteRepository->getNoteById($noteId);
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
        return $this->userNoteRepository->getImportantNotes($userId);
    }
}