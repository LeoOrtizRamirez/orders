<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class ProductsStockImport implements ToCollection, WithHeadingRow
{
    private $updated = 0;
    private $failed = 0;
    private $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $sku = $row['sku'] ?? null;
            $stock = $row['stock'] ?? null;

            if (empty($sku) || !isset($stock)) {
                $this->failed++;
                $this->errors[] = ['row' => $row, 'error' => 'El SKU y el stock son obligatorios.'];
                continue;
            }

            if (!is_numeric($stock) || (int)$stock < 0) {
                $this->failed++;
                $this->errors[] = ['row' => $row, 'error' => 'El stock debe ser un número entero no negativo.'];
                continue;
            }

            try {
                $product = Product::where('sku', $sku)->first();

                if ($product) {
                    $product->stock = (int)$stock;
                    $product->save();
                    $this->updated++;
                } else {
                    $this->failed++;
                    $this->errors[] = ['row' => $row, 'error' => "Producto con SKU '{$sku}' no encontrado."];
                }
            } catch (\Exception $e) {
                $this->failed++;
                $this->errors[] = ['row' => $row, 'error' => $e->getMessage()];
                Log::error("Error al importar stock para SKU {$sku}: " . $e->getMessage());
            }
        }
    }

    public function getResults(): array
    {
        return [
            'updated' => $this->updated,
            'failed' => $this->failed,
            'errors' => $this->errors,
        ];
    }
}
