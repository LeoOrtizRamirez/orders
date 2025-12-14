<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'author_id',
        'note',
        'is_important',
        'notable_id',
        'notable_type'
    ];

    protected $casts = [
        'is_important' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function notable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}