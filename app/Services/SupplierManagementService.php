<?php

namespace App\Services;

use App\Repositories\SupplierRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierManagementService
{
    public function __construct(private SupplierRepository $supplierRepository) {}

    public function getAllSuppliers(array $filters = []): LengthAwarePaginator
    {
        return $this->supplierRepository->getAll($filters);
    }

    public function getSupplier(int $id)
    {
        $supplier = $this->supplierRepository->findById($id);
        
        if (!$supplier) {
            throw new Exception('Proveedor no encontrado', 404);
        }

        return $supplier;
    }

    public function createSupplier(array $data)
    {
        return $this->supplierRepository->create($data);
    }

    public function updateSupplier(int $id, array $data)
    {
        $supplier = $this->getSupplier($id);

        $updated = $this->supplierRepository->update($id, $data);
        
        if (!$updated) {
            throw new Exception('Error al actualizar el proveedor', 500);
        }

        return $this->getSupplier($id);
    }

    public function deleteSupplier(int $id)
    {
        $supplier = $this->getSupplier($id);
        
        // Verificar si tiene órdenes de compra asociadas
        if ($supplier->purchase_orders_count > 0) {
            throw new Exception('No se puede eliminar el proveedor porque tiene órdenes de compra asociadas', 400);
        }

        $deleted = $this->supplierRepository->delete($id);
        
        if (!$deleted) {
            throw new Exception('Error al eliminar el proveedor', 500);
        }

        return true;
    }

    public function toggleSupplierStatus(int $id)
    {
        $supplier = $this->getSupplier($id);
        
        $newStatus = !$supplier->is_active;
        
        $this->supplierRepository->update($id, ['is_active' => $newStatus]);
        
        return $this->getSupplier($id);
    }

    public function getActiveSuppliers()
    {
        return $this->supplierRepository->getActiveSuppliers();
    }

    public function getSuppliersForSelect()
    {
        return $this->supplierRepository->getSuppliersForSelect();
    }
}