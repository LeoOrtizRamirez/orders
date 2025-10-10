<?php

namespace App\Repositories;

use App\Models\Video;
use Illuminate\Pagination\LengthAwarePaginator;

class VideoRepository
{
    public function __construct(private Video $model) {}

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with('service');

        // Filtrar por servicio
        if (isset($filters['service_id'])) {
            $query->where('service_id', $filters['service_id']);
        }

        // Filtrar por estado activo
        if (isset($filters['active'])) {
            $query->where('is_active', $filters['active']);
        }

        // Filtrar por gratuitos
        if (isset($filters['free'])) {
            $query->where('is_free', $filters['free']);
        }

        return $query->ordered()->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id): ?Video
    {
        return $this->model->with('service')->find($id);
    }

    public function findBySlug(string $slug): ?Video
    {
        return $this->model->with('service')->where('slug', $slug)->first();
    }

    public function create(array $data): Video
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $video = $this->findById($id);
        
        if (!$video) {
            return false;
        }

        return $video->update($data);
    }

    public function delete(int $id): bool
    {
        $video = $this->findById($id);
        
        if (!$video) {
            return false;
        }

        return $video->delete();
    }

    public function getVideosByService(int $serviceId)
    {
        return $this->model->where('service_id', $serviceId)
            ->active()
            ->ordered()
            ->get();
    }

    public function getFreeVideos()
    {
        return $this->model->with('service')
            ->active()
            ->free()
            ->ordered()
            ->get();
    }
}