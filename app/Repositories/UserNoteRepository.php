<?php

namespace App\Repositories;

use App\Models\UserNote;
use Illuminate\Support\Collection;

class UserNoteRepository
{
    public function getNotesByUserId(int $userId): Collection
    {
        return UserNote::with('author')
            ->where('user_id', $userId)
            ->orderBy('is_important', 'desc')
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

    public function getImportantNotes(int $userId): Collection
    {
        return UserNote::with('author')
            ->where('user_id', $userId)
            ->important()
            ->get();
    }

    public function getNoteById(int $noteId): ?UserNote
    {
        return UserNote::with(['user', 'author'])->find($noteId);
    }
}