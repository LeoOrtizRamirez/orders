<?php
// app/Models/ProfileMetric.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileMetric extends Model
{
    use HasFactory;

    protected $table = 'profile_metrics';

    protected $fillable = [
        'user_id',
        'visitas_perfil',
        'ganancias_suscripciones',
        'nuevas_suscripciones',
        'renovaciones',
        'fecha_registro'
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'ganancias_suscripciones' => 'decimal:2',
        'visitas_perfil' => 'integer',
        'nuevas_suscripciones' => 'integer',
        'renovaciones' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('fecha_registro', [$startDate, $endDate]);
    }

    public function scopeForYear($query, $year)
    {
        return $query->whereYear('fecha_registro', $year);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('fecha_registro', $year)
                    ->whereMonth('fecha_registro', $month);
    }
}