<?php

namespace App\Services;

use App\Repositories\ServiceRepository;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceManagementService
{
    public function __construct(private ServiceRepository $serviceRepository) {}

    public function getAllServices(array $filters = []): LengthAwarePaginator
    {
        return $this->serviceRepository->getAll($filters);
    }

    public function getService(int $id)
    {
        $service = $this->serviceRepository->findById($id);
        
        if (!$service) {
            throw new Exception('Servicio no encontrado', 404);
        }

        // Cargar relación de vídeos para la respuesta
        $service->load('videos');

        return $service;
    }

    public function getServiceBySlug(string $slug)
    {
        $service = $this->serviceRepository->findBySlug($slug);
        
        if (!$service) {
            throw new Exception('Servicio no encontrado', 404);
        }

        // Cargar relación de vídeos activos
        $service->load(['videos' => function($query) {
            $query->where('is_active', true)->ordered();
        }]);

        return $service;
    }

    public function createService(array $data)
    {
        // Generar slug automáticamente si no se proporciona
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Validar slug único
        $existingService = $this->serviceRepository->findBySlug($data['slug']);
        if ($existingService) {
            throw new Exception('El slug ya está en uso', 400);
        }

        $service = $this->serviceRepository->create($data);
        
        // Cargar relación de vídeos para la respuesta
        $service->load('videos');

        return $service;
    }

    public function updateService(int $id, array $data)
    {
        $service = $this->getService($id);

        // Si se actualiza el nombre y no se proporciona slug, generar uno nuevo
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
            
            // Validar slug único (excluyendo el actual)
            $existingService = $this->serviceRepository->findBySlug($data['slug']);
            if ($existingService && $existingService->id != $id) {
                throw new Exception('El slug ya está en uso', 400);
            }
        }

        $updated = $this->serviceRepository->update($id, $data);
        
        if (!$updated) {
            throw new Exception('Error al actualizar el servicio', 500);
        }

        return $this->getService($id);
    }

    public function deleteService(int $id)
    {
        $service = $this->getService($id);
        
        // Verificar si tiene vídeos asociados
        if ($service->videos()->count() > 0) {
            throw new Exception('No se puede eliminar el servicio porque tiene vídeos asociados', 400);
        }

        $deleted = $this->serviceRepository->delete($id);
        
        if (!$deleted) {
            throw new Exception('Error al eliminar el servicio', 500);
        }

        return true;
    }

    public function toggleServiceStatus(int $id)
    {
        $service = $this->getService($id);
        
        $newStatus = !$service->is_active;
        
        $this->serviceRepository->update($id, ['is_active' => $newStatus]);
        
        return $this->getService($id);
    }

    public function getActiveServices()
    {
        return $this->serviceRepository->getActiveServices();
    }
}