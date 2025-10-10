<?php
// app/Repositories/ProfileMetricRepository.php

namespace App\Repositories;

use App\Models\ProfileMetric;
use App\Models\User;
use Illuminate\Support\Collection;

class ProfileMetricRepository
{
    public function getYearlyMetrics(int $userId, int $startYear, int $endYear): Collection
    {
        return ProfileMetric::where('user_id', $userId)
            ->whereYear('fecha_registro', '>=', $startYear)
            ->whereYear('fecha_registro', '<=', $endYear)
            ->selectRaw('
                YEAR(fecha_registro) as year,
                SUM(visitas_perfil) as visitas_perfil,
                SUM(ganancias_suscripciones) as ganancias_suscripciones,
                SUM(nuevas_suscripciones) as nuevas_suscripciones,
                SUM(renovaciones) as renovaciones
            ')
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();
    }

    public function getMonthlyMetrics(int $userId, int $year): Collection
    {
        return ProfileMetric::where('user_id', $userId)
            ->whereYear('fecha_registro', $year)
            ->selectRaw('
                MONTH(fecha_registro) as month,
                SUM(visitas_perfil) as visitas_perfil,
                SUM(ganancias_suscripciones) as ganancias_suscripciones,
                SUM(nuevas_suscripciones) as nuevas_suscripciones,
                SUM(renovaciones) as renovaciones
            ')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
    }

    public function getDailyMetrics(int $userId, int $year, int $month): Collection
    {
        return ProfileMetric::where('user_id', $userId)
            ->whereYear('fecha_registro', $year)
            ->whereMonth('fecha_registro', $month)
            ->selectRaw('
                DAY(fecha_registro) as day,
                SUM(visitas_perfil) as visitas_perfil,
                SUM(ganancias_suscripciones) as ganancias_suscripciones,
                SUM(nuevas_suscripciones) as nuevas_suscripciones,
                SUM(renovaciones) as renovaciones
            ')
            ->groupBy('day')
            ->orderBy('day', 'asc')
            ->get();
    }

    public function getGlobalYearlyMetrics(int $startYear, int $endYear): Collection
    {
        return ProfileMetric::whereYear('fecha_registro', '>=', $startYear)
            ->whereYear('fecha_registro', '<=', $endYear)
            ->selectRaw('
                YEAR(fecha_registro) as year,
                SUM(visitas_perfil) as visitas_perfil,
                SUM(ganancias_suscripciones) as ganancias_suscripciones,
                SUM(nuevas_suscripciones) as nuevas_suscripciones,
                SUM(renovaciones) as renovaciones,
                COUNT(DISTINCT user_id) as usuarios_activos
            ')
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();
    }

    public function getGlobalMonthlyMetrics(int $year): Collection
    {
        return ProfileMetric::whereYear('fecha_registro', $year)
            ->selectRaw('
                MONTH(fecha_registro) as month,
                SUM(visitas_perfil) as visitas_perfil,
                SUM(ganancias_suscripciones) as ganancias_suscripciones,
                SUM(nuevas_suscripciones) as nuevas_suscripciones,
                SUM(renovaciones) as renovaciones,
                COUNT(DISTINCT user_id) as usuarios_activos
            ')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
    }

    public function getGlobalDailyMetrics(int $year, int $month): Collection
    {
        return ProfileMetric::whereYear('fecha_registro', $year)
            ->whereMonth('fecha_registro', $month)
            ->selectRaw('
                DAY(fecha_registro) as day,
                SUM(visitas_perfil) as visitas_perfil,
                SUM(ganancias_suscripciones) as ganancias_suscripciones,
                SUM(nuevas_suscripciones) as nuevas_suscripciones,
                SUM(renovaciones) as renovaciones,
                COUNT(DISTINCT user_id) as usuarios_activos
            ')
            ->groupBy('day')
            ->orderBy('day', 'asc')
            ->get();
    }

    public function getUsersWithMetrics(): Collection
    {
        return User::leftJoin('profile_metrics', 'users.id', '=', 'profile_metrics.user_id')
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->selectRaw('
                users.id,
                users.name,
                users.email,
                users.created_at,
                COALESCE(SUM(profile_metrics.visitas_perfil), 0) as total_visitas,
                COALESCE(SUM(profile_metrics.ganancias_suscripciones), 0) as total_ganancias,
                COALESCE(SUM(profile_metrics.nuevas_suscripciones), 0) as total_nuevas_suscripciones,
                COALESCE(SUM(profile_metrics.renovaciones), 0) as total_renovaciones
            ')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at')
            ->orderBy('total_ganancias', 'desc')
            ->get();
    }
}