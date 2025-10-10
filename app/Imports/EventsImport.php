<?php

namespace App\Imports;

use App\Models\Event;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EventsImport implements ToCollection, WithHeadingRow
{
    protected $userId;
    protected $importedCount = 0;
    protected $skippedCount = 0;
    protected $errors = [];

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                // Normalizar los nombres de las columnas (diferentes formatos de CSV/Excel)
                $title = $row['titulo'] ?? $row['title'] ?? $row['evento'] ?? $row['nombre'] ?? null;
                $start = $row['inicio'] ?? $row['start'] ?? $row['fecha_inicio'] ?? $row['fecha_inicial'] ?? null;
                $end = $row['fin'] ?? $row['end'] ?? $row['fecha_fin'] ?? $row['fecha_final'] ?? null;
                $description = $row['descripcion'] ?? $row['description'] ?? $row['detalles'] ?? $row['comentarios'] ?? '';
                $type = $row['tipo'] ?? $row['type'] ?? $row['categoria'] ?? $row['category'] ?? 'primary';

                // Validar campos obligatorios
                if (!$title || !$start || !$end) {
                    $this->skippedCount++;
                    $this->errors[] = "Fila " . ($index + 2) . ": Faltan campos obligatorios (título, inicio o fin)";
                    continue;
                }

                // Convertir fechas a formato correcto
                try {
                    $startDate = Carbon::parse($start);
                    $endDate = Carbon::parse($end);
                    
                    if ($endDate <= $startDate) {
                        $this->skippedCount++;
                        $this->errors[] = "Fila " . ($index + 2) . ": La fecha de fin debe ser posterior a la fecha de inicio";
                        continue;
                    }
                } catch (\Exception $e) {
                    $this->skippedCount++;
                    $this->errors[] = "Fila " . ($index + 2) . ": Formato de fecha inválido";
                    continue;
                }

                // Validar tipo
                $validTypes = ['primary', 'info', 'success', 'danger'];
                if (!in_array($type, $validTypes)) {
                    $type = 'primary'; // Valor por defecto
                }

                // Crear el evento
                Event::create([
                    'user_id' => $this->userId,
                    'title' => $title,
                    'start' => $startDate,
                    'end' => $endDate,
                    'description' => $description,
                    'type' => $type
                ]);

                $this->importedCount++;

            } catch (\Exception $e) {
                $this->skippedCount++;
                $this->errors[] = "Fila " . ($index + 2) . ": Error - " . $e->getMessage();
            }
        }
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getSkippedCount()
    {
        return $this->skippedCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}