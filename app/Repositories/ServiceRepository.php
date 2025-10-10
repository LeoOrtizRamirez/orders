<?php
// app/Repositories/ServiceRepository.php

namespace App\Repositories;

use App\Models\Service;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceRepository
{
    public function __construct(private Service $model) {}

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->withCount('videos'); // Agregar withCount

        // Filtrar por estado activo
        if (isset($filters['active'])) {
            $query->where('is_active', $filters['active']);
        }

        // Buscar por nombre
        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        return $query->ordered()->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id): ?Service
    {
        return $this->model->withCount('videos')->with('videos')->find($id);
    }

    public function findBySlug(string $slug): ?Service
    {
        return $this->model->withCount('videos')->with('activeVideos')->where('slug', $slug)->first();
    }

    public function create(array $data): Service
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $service = $this->findById($id);
        
        if (!$service) {
            return false;
        }

        return $service->update($data);
    }

    public function delete(int $id): bool
    {
        $service = $this->findById($id);
        
        if (!$service) {
            return false;
        }

        return $service->delete();
    }

    public function getActiveServices()
    {
        return $this->model->active()->withCount('videos')->ordered()->get();
    }
}