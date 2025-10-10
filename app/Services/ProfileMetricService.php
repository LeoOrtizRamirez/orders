<?php
// app/Services/ProfileMetricService.php

namespace App\Services;

use App\Repositories\ProfileMetricRepository;
use App\Models\User;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ProfileMetricService
{
    public function __construct(
        private ProfileMetricRepository $profileMetricRepository
    ) {}

    public function getMetricsForCharts(int $userId, string $period = 'monthly', int $year = null, int $month = null): array
    {
        $year = $year ?? date('Y');
        
        if ($period === 'yearly') {
            return $this->getYearlyMetrics($userId, $year);
        } else {
            return $this->getMonthlyMetrics($userId, $year, $month);
        }
    }

    private function getYearlyMetrics(int $userId, int $year): array
    {
        $yearlyData = $this->profileMetricRepository->getYearlyMetrics($userId, $year - 4, $year);
        
        $years = [];
        $visitas = [];
        $ganancias = [];
        $nuevasSuscripciones = [];
        $renovaciones = [];

        for ($i = $year - 4; $i <= $year; $i++) {
            $years[] = (string)$i;
            $yearData = $yearlyData->firstWhere('year', $i);
            
            $visitas[] = (int) ($yearData->visitas_perfil ?? 0);
            $ganancias[] = (float) ($yearData->ganancias_suscripciones ?? 0);
            $nuevasSuscripciones[] = (int) ($yearData->nuevas_suscripciones ?? 0);
            $renovaciones[] = (int) ($yearData->renovaciones ?? 0);
        }

        return [
            'period' => 'yearly',
            'scope' => 'individual',
            'labels' => $years,
            'datasets' => [
                'visitas' => $visitas,
                'nuevas_suscripciones' => $nuevasSuscripciones,
                'ganancias' => $ganancias,
                'renovaciones' => $renovaciones
            ],
            'summary' => $this->calculateSummary($visitas, $ganancias, $nuevasSuscripciones, $renovaciones),
            'has_data' => $yearlyData->isNotEmpty()
        ];
    }

    private function getMonthlyMetrics(int $userId, int $year, int $month = null): array
    {
        if ($month) {
            return $this->getDailyMetrics($userId, $year, $month);
        }

        $monthlyData = $this->profileMetricRepository->getMonthlyMetrics($userId, $year);
        
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $visitas = array_fill(0, 12, 0);
        $ganancias = array_fill(0, 12, 0);
        $nuevasSuscripciones = array_fill(0, 12, 0);
        $renovaciones = array_fill(0, 12, 0);

        foreach ($monthlyData as $data) {
            $monthIndex = $data->month - 1;
            if ($monthIndex >= 0 && $monthIndex < 12) {
                $visitas[$monthIndex] = (int) ($data->visitas_perfil ?? 0);
                $ganancias[$monthIndex] = (float) ($data->ganancias_suscripciones ?? 0);
                $nuevasSuscripciones[$monthIndex] = (int) ($data->nuevas_suscripciones ?? 0);
                $renovaciones[$monthIndex] = (int) ($data->renovaciones ?? 0);
            }
        }

        return [
            'period' => 'monthly',
            'scope' => 'individual',
            'labels' => $months,
            'datasets' => [
                'visitas' => $visitas,
                'nuevas_suscripciones' => $nuevasSuscripciones,
                'ganancias' => $ganancias,
                'renovaciones' => $renovaciones
            ],
            'summary' => $this->calculateSummary($visitas, $ganancias, $nuevasSuscripciones, $renovaciones),
            'has_data' => $monthlyData->isNotEmpty()
        ];
    }

    private function getDailyMetrics(int $userId, int $year, int $month): array
    {
        $dailyData = $this->profileMetricRepository->getDailyMetrics($userId, $year, $month);
        
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $days = range(1, $daysInMonth);
        
        $visitas = array_fill(0, $daysInMonth, 0);
        $ganancias = array_fill(0, $daysInMonth, 0);
        $nuevasSuscripciones = array_fill(0, $daysInMonth, 0);
        $renovaciones = array_fill(0, $daysInMonth, 0);

        foreach ($dailyData as $data) {
            $dayIndex = $data->day - 1;
            if ($dayIndex >= 0 && $dayIndex < $daysInMonth) {
                $visitas[$dayIndex] = (int) ($data->visitas_perfil ?? 0);
                $ganancias[$dayIndex] = (float) ($data->ganancias_suscripciones ?? 0);
                $nuevasSuscripciones[$dayIndex] = (int) ($data->nuevas_suscripciones ?? 0);
                $renovaciones[$dayIndex] = (int) ($data->renovaciones ?? 0);
            }
        }

        return [
            'period' => 'daily',
            'scope' => 'individual',
            'labels' => $days,
            'datasets' => [
                'visitas' => $visitas,
                'nuevas_suscripciones' => $nuevasSuscripciones,
                'ganancias' => $ganancias,
                'renovaciones' => $renovaciones
            ],
            'summary' => $this->calculateSummary($visitas, $ganancias, $nuevasSuscripciones, $renovaciones),
            'has_data' => $dailyData->isNotEmpty()
        ];
    }

    public function getUsersWithMetrics(): Collection
    {
        $users = $this->profileMetricRepository->getUsersWithMetrics();
        
        return $users->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'total_visitas' => (int) $user->total_visitas,
                'total_ganancias' => (float) $user->total_ganancias,
                'total_nuevas_suscripciones' => (int) $user->total_nuevas_suscripciones,
                'total_renovaciones' => (int) $user->total_renovaciones,
                'member_since' => Carbon::parse($user->created_at)->format('M Y')
            ];
        });
    }

    public function getGlobalMetrics(string $period = 'monthly', int $year = null, int $month = null): array
    {
        $year = $year ?? date('Y');
        
        if ($period === 'yearly') {
            return $this->getGlobalYearlyMetrics($year);
        } else {
            return $this->getGlobalMonthlyMetrics($year, $month);
        }
    }

    private function getGlobalYearlyMetrics(int $year): array
    {
        $yearlyData = $this->profileMetricRepository->getGlobalYearlyMetrics($year - 4, $year);
        
        $years = [];
        $visitas = [];
        $ganancias = [];
        $nuevasSuscripciones = [];
        $renovaciones = [];
        $usuariosActivos = [];

        for ($i = $year - 4; $i <= $year; $i++) {
            $years[] = (string)$i;
            $yearData = $yearlyData->firstWhere('year', $i);
            
            $visitas[] = (int) ($yearData->visitas_perfil ?? 0);
            $ganancias[] = (float) ($yearData->ganancias_suscripciones ?? 0);
            $nuevasSuscripciones[] = (int) ($yearData->nuevas_suscripciones ?? 0);
            $renovaciones[] = (int) ($yearData->renovaciones ?? 0);
            $usuariosActivos[] = (int) ($yearData->usuarios_activos ?? 0);
        }

        return [
            'period' => 'yearly',
            'scope' => 'global',
            'labels' => $years,
            'datasets' => [
                'visitas' => $visitas,
                'nuevas_suscripciones' => $nuevasSuscripciones,
                'ganancias' => $ganancias,
                'renovaciones' => $renovaciones,
                'usuarios_activos' => $usuariosActivos
            ],
            'summary' => $this->calculateGlobalSummary($visitas, $ganancias, $nuevasSuscripciones, $renovaciones, $usuariosActivos),
            'has_data' => $yearlyData->isNotEmpty()
        ];
    }

    private function getGlobalMonthlyMetrics(int $year, int $month = null): array
    {
        if ($month) {
            return $this->getGlobalDailyMetrics($year, $month);
        }

        $monthlyData = $this->profileMetricRepository->getGlobalMonthlyMetrics($year);
        
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $visitas = array_fill(0, 12, 0);
        $ganancias = array_fill(0, 12, 0);
        $nuevasSuscripciones = array_fill(0, 12, 0);
        $renovaciones = array_fill(0, 12, 0);
        $usuariosActivos = array_fill(0, 12, 0);

        foreach ($monthlyData as $data) {
            $monthIndex = $data->month - 1;
            if ($monthIndex >= 0 && $monthIndex < 12) {
                $visitas[$monthIndex] = (int) ($data->visitas_perfil ?? 0);
                $ganancias[$monthIndex] = (float) ($data->ganancias_suscripciones ?? 0);
                $nuevasSuscripciones[$monthIndex] = (int) ($data->nuevas_suscripciones ?? 0);
                $renovaciones[$monthIndex] = (int) ($data->renovaciones ?? 0);
                $usuariosActivos[$monthIndex] = (int) ($data->usuarios_activos ?? 0);
            }
        }

        return [
            'period' => 'monthly',
            'scope' => 'global',
            'labels' => $months,
            'datasets' => [
                'visitas' => $visitas,
                'nuevas_suscripciones' => $nuevasSuscripciones,
                'ganancias' => $ganancias,
                'renovaciones' => $renovaciones,
                'usuarios_activos' => $usuariosActivos
            ],
            'summary' => $this->calculateGlobalSummary($visitas, $ganancias, $nuevasSuscripciones, $renovaciones, $usuariosActivos),
            'has_data' => $monthlyData->isNotEmpty()
        ];
    }

    private function getGlobalDailyMetrics(int $year, int $month): array
    {
        $dailyData = $this->profileMetricRepository->getGlobalDailyMetrics($year, $month);
        
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $days = range(1, $daysInMonth);
        
        $visitas = array_fill(0, $daysInMonth, 0);
        $ganancias = array_fill(0, $daysInMonth, 0);
        $nuevasSuscripciones = array_fill(0, $daysInMonth, 0);
        $renovaciones = array_fill(0, $daysInMonth, 0);
        $usuariosActivos = array_fill(0, $daysInMonth, 0);

        foreach ($dailyData as $data) {
            $dayIndex = $data->day - 1;
            if ($dayIndex >= 0 && $dayIndex < $daysInMonth) {
                $visitas[$dayIndex] = (int) ($data->visitas_perfil ?? 0);
                $ganancias[$dayIndex] = (float) ($data->ganancias_suscripciones ?? 0);
                $nuevasSuscripciones[$dayIndex] = (int) ($data->nuevas_suscripciones ?? 0);
                $renovaciones[$dayIndex] = (int) ($data->renovaciones ?? 0);
                $usuariosActivos[$dayIndex] = (int) ($data->usuarios_activos ?? 0);
            }
        }

        return [
            'period' => 'daily',
            'scope' => 'global',
            'labels' => $days,
            'datasets' => [
                'visitas' => $visitas,
                'nuevas_suscripciones' => $nuevasSuscripciones,
                'ganancias' => $ganancias,
                'renovaciones' => $renovaciones,
                'usuarios_activos' => $usuariosActivos
            ],
            'summary' => $this->calculateGlobalSummary($visitas, $ganancias, $nuevasSuscripciones, $renovaciones, $usuariosActivos),
            'has_data' => $dailyData->isNotEmpty()
        ];
    }

    private function calculateSummary(array $visitas, array $ganancias, array $nuevasSuscripciones, array $renovaciones): array
    {
        return [
            'total_visitas' => array_sum($visitas),
            'total_ganancias' => array_sum($ganancias),
            'total_nuevas_suscripciones' => array_sum($nuevasSuscripciones),
            'total_renovaciones' => array_sum($renovaciones),
            'avg_visitas' => count($visitas) > 0 ? array_sum($visitas) / count($visitas) : 0,
            'avg_ganancias' => count($ganancias) > 0 ? array_sum($ganancias) / count($ganancias) : 0
        ];
    }

    private function calculateGlobalSummary(array $visitas, array $ganancias, array $nuevasSuscripciones, array $renovaciones, array $usuariosActivos): array
    {
        $totalUsuariosActivos = array_sum($usuariosActivos);
        
        return [
            'total_visitas' => array_sum($visitas),
            'total_ganancias' => array_sum($ganancias),
            'total_nuevas_suscripciones' => array_sum($nuevasSuscripciones),
            'total_renovaciones' => array_sum($renovaciones),
            'total_usuarios_activos' => $totalUsuariosActivos,
            'avg_visitas_por_usuario' => $totalUsuariosActivos > 0 ? array_sum($visitas) / $totalUsuariosActivos : 0,
            'avg_ganancias_por_usuario' => $totalUsuariosActivos > 0 ? array_sum($ganancias) / $totalUsuariosActivos : 0
        ];
    }
}