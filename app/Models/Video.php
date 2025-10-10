<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'title',
        'slug',
        'description',
        'video_url',
        'thumbnail_url',
        'duration_seconds',
        'order',
        'is_free',
        'is_active',
        'tags'
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_active' => 'boolean',
        'duration_seconds' => 'integer',
        'order' => 'integer',
        'tags' => 'array'
    ];

    /**
     * Relación con el servicio
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Scope para vídeos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para vídeos gratuitos
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope para ordenar por orden personalizado
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('title');
    }

    /**
     * Formatear duración a formato legible
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}