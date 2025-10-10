<?php
// app/Models/Service.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration_minutes',
        'is_active',
        'order',
        'icon',
        'color',
        'whatsapp_url'
    ];

    protected $casts = [
        'price' => 'integer',
        'is_active' => 'boolean',
        'duration_minutes' => 'integer',
        'order' => 'integer'
    ];

    protected $appends = ['videos_count'];

    /**
     * Relación con los vídeos del servicio
     */
    public function videos(): HasMany
    {
        $query =  $this->hasMany(Video::class)->orderBy('order');

        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            $query->where('is_active', true);
        }

        return $query;
    }

    /**
     * Obtener el contador de vídeos
     */
    public function getVideosCountAttribute(): int
    {
        // Si ya está cargada la relación, usar count()
        if ($this->relationLoaded('videos')) {
            return $this->videos->count();
        }
        
        // Si no, hacer count en la base de datos
        return $this->videos()->count();
    }

    /**
     * Scope para servicios activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ordenar por orden personalizado
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Obtener vídeos activos del servicio
     */
    public function activeVideos()
    {
        return $this->videos()->where('is_active', true);
    }

    /**
     * Cargar relaciones con contadores
     */
    public function scopeWithVideosCount($query)
    {
        return $query->withCount('videos');
    }
}