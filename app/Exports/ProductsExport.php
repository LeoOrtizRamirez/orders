<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Product::query()->withCount('purchaseOrderItems');

        // Calcular Stock Comprometido (Requerido en órdenes activas antes de facturación)
        $query->withSum(['purchaseOrderItems as committed_stock' => function($q) {
            $q->whereHas('purchaseOrder', function($orderQuery) {
                $orderQuery->whereIn('status', [
                    'nuevo pedido', 
                    'disponibilidad', 
                    'preparar pedido', 
                    'en preparación'
                ]);
            });
        }], 'quantity');

        if (isset($this->filters['active'])) {
            $query->where('is_active', $this->filters['active']);
        }

        if (isset($this->filters['search'])) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->filters['search']}%")
                  ->orWhere('sku', 'like', "%{$this->filters['search']}%")
                  ->orWhere('description', 'like', "%{$this->filters['search']}%");
            });
        }

        if (isset($this->filters['category'])) {
            $query->where('category', $this->filters['category']);
        }

        if (isset($this->filters['unit'])) {
            $query->where('unit', $this->filters['unit']);
        }

        if (isset($this->filters['stock_status'])) {
            if ($this->filters['stock_status'] === 'low') {
                $query->whereColumn('stock', '<=', 'reorder_point')
                      ->where('stock', '>', 0);
            } elseif ($this->filters['stock_status'] === 'out') {
                $query->where('stock', 0);
            }
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Producto',
            'Categoría',
            'Unidad',
            'Stock Físico',
            'Stock Requerido',
            'Stock Disponible',
            'Estado',
        ];
    }

    public function map($product): array
    {
        $committed = (float)($product->committed_stock ?? 0);
        $available = $product->stock - $committed;

        return [
            $product->sku,
            $product->name,
            $product->category->value ?? $product->category,
            $product->unit->value ?? $product->unit,
            $product->stock,
            $committed,
            $available,
            $product->is_active ? 'Activo' : 'Inactivo',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
