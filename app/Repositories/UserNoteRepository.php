<?php

namespace App\Repositories;

use App\Models\UserNote;
use Illuminate\Support\Collection;

class UserNoteRepository
{
    public function getNotes(?int $userId = null, ?int $notableId = null, ?string $notableType = null, bool $isImportant = false): Collection
    {
        $query = UserNote::with('author');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($notableId && $notableType) {
            $query->where('notable_id', $notableId)
                  ->where('notable_type', $notableType)
                  ->with('notable'); // Eager load the notable relationship
        }

        if ($isImportant) {
            $query->important();
        }

        return $query->orderBy('is_important', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createNote(array $data): UserNote
    {
        return UserNote::create($data);
    }

    public function updateNote(int $noteId, array $data): bool
    {
        return UserNote::where('id', $noteId)->update($data);
    }

    public function deleteNote(int $noteId): bool
    {
        return UserNote::where('id', $noteId)->delete();
    }

    public function getNoteById(int $noteId): ?UserNote
    {
        return UserNote::with(['user', 'author'])->find($noteId);
    }
}