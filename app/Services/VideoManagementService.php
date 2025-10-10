<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Support\Str;
use Exception;

class VideoManagementService
{
    public function __construct(
        private VideoRepository $videoRepository,
        private ServiceRepository $serviceRepository
    ) {}

    public function getAllVideos(array $filters = [])
    {
        return $this->videoRepository->getAll($filters);
    }

    public function getVideo(int $id)
    {
        $video = $this->videoRepository->findById($id);
        
        if (!$video) {
            throw new Exception('Vídeo no encontrado', 404);
        }

        return $video;
    }

    public function getVideoBySlug(string $slug)
    {
        $video = $this->videoRepository->findBySlug($slug);
        
        if (!$video) {
            throw new Exception('Vídeo no encontrado', 404);
        }

        return $video;
    }

    public function createVideo(array $data)
    {
        // Verificar que el servicio existe
        $service = $this->serviceRepository->findById($data['service_id']);
        if (!$service) {
            throw new Exception('Servicio no encontrado', 404);
        }

        // Generar slug automáticamente si no se proporciona
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Validar slug único
        $existingVideo = $this->videoRepository->findBySlug($data['slug']);
        if ($existingVideo) {
            throw new Exception('El slug ya está en uso', 400);
        }

        return $this->videoRepository->create($data);
    }

    public function updateVideo(int $id, array $data)
    {
        $video = $this->getVideo($id);

        // Si se actualiza el servicio, verificar que existe
        if (isset($data['service_id'])) {
            $service = $this->serviceRepository->findById($data['service_id']);
            if (!$service) {
                throw new Exception('Servicio no encontrado', 404);
            }
        }

        // Si se actualiza el título y no se proporciona slug, generar uno nuevo
        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
            
            // Validar slug único (excluyendo el actual)
            $existingVideo = $this->videoRepository->findBySlug($data['slug']);
            if ($existingVideo && $existingVideo->id != $id) {
                throw new Exception('El slug ya está en uso', 400);
            }
        }

        $updated = $this->videoRepository->update($id, $data);
        
        if (!$updated) {
            throw new Exception('Error al actualizar el vídeo', 500);
        }

        return $this->getVideo($id);
    }

    public function deleteVideo(int $id)
    {
        $video = $this->getVideo($id);
        
        $deleted = $this->videoRepository->delete($id);
        
        if (!$deleted) {
            throw new Exception('Error al eliminar el vídeo', 500);
        }

        return true;
    }

    public function toggleVideoStatus(int $id)
    {
        $video = $this->getVideo($id);
        
        $newStatus = !$video->is_active;
        
        return $this->updateVideo($id, ['is_active' => $newStatus]);
    }

    public function getVideosByService(int $serviceId)
    {
        $service = $this->serviceRepository->findById($serviceId);
        if (!$service) {
            throw new Exception('Servicio no encontrado', 404);
        }

        return $this->videoRepository->getVideosByService($serviceId);
    }

    public function getFreeVideos()
    {
        return $this->videoRepository->getFreeVideos();
    }
}