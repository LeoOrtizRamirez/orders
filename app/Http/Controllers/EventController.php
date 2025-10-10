<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EventsImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view events')->only(['index', 'show']);
        $this->middleware('permission:create events')->only(['store', 'import']);
        $this->middleware('permission:edit events')->only(['update']);
        $this->middleware('permission:delete events')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        if ($request->user()->hasRole('admin')) {
            $events = Event::with('user')->orderBy('start', 'asc')->paginate(10);
        } else {
            $events = Event::where('user_id', $request->user()->id)
                         ->orderBy('start', 'asc')
                         ->paginate(10);
        }
        
        return response()->json($events);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'type' => 'required|in:primary,info,success,danger'
        ]);

        $validated['user_id'] = $request->user()->id;
        
        $event = Event::create($validated);
        
        return response()->json($event, 201);
    }

    public function show(Event $event): JsonResponse
    {
        // Verificar que el usuario puede ver este evento
        if (!$this->canAccessEvent($event)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        return response()->json($event);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        // Verificar permisos
        if (!$this->canAccessEvent($event)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start' => 'sometimes|date',
            'end' => 'sometimes|date|after:start',
            'type' => 'sometimes|in:primary,info,success,danger'
        ]);

        $event->update($validated);
        
        return response()->json($event);
    }

    public function destroy(Event $event): JsonResponse
    {
        // Verificar permisos
        if (!$this->canAccessEvent($event)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $event->delete();
        return response()->json(null, 204);
    }

     public function import(Request $request): JsonResponse
    {
        // Validar permisos
        if (!$request->user()->can('create events')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Validar archivo
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,xlsx,xls|max:2048',
            'overwrite' => 'sometimes|string' // Cambiado a string para manejar mejor la conversión
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ], 422);
        }

        try {
            // Convertir el valor de overwrite a booleano correctamente
            $overwrite = false;
            if ($request->has('overwrite')) {
                $overwriteValue = $request->input('overwrite');
                $overwrite = ($overwriteValue === 'true' || $overwriteValue === '1' || $overwriteValue === true);
            }
            
            // Si se seleccionó sobrescribir, eliminar eventos existentes del usuario
            if ($overwrite) {
                Event::where('user_id', $request->user()->id)->delete();
            }

            // Importar eventos
            $import = new EventsImport($request->user()->id);
            Excel::import($import, $request->file('file'));
            
            return response()->json([
                'success' => true,
                'imported' => $import->getImportedCount(),
                'skipped' => $import->getSkippedCount(),
                'message' => 'Eventos importados correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['Error al importar eventos: ' . $e->getMessage()]
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        // Validar permisos
        if (!auth()->user()->can('create events')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $filePath = storage_path('app/templates/events_import_template.xlsx');
        
        // Crear directorio si no existe
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        // Crear el archivo de plantilla
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Encabezados
        $sheet->setCellValue('A1', 'titulo');
        $sheet->setCellValue('B1', 'inicio');
        $sheet->setCellValue('C1', 'fin');
        $sheet->setCellValue('D1', 'descripcion');
        $sheet->setCellValue('E1', 'tipo');
        
        // Formato de fechas para las celdas de ejemplo
        $sheet->getStyle('B2:B5')->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
        $sheet->getStyle('C2:C5')->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
        
        // Algunos datos de ejemplo
        $sheet->setCellValue('A2', 'Reunión de equipo');
        $sheet->setCellValue('B2', '2025-09-15 09:00:00');
        $sheet->setCellValue('C2', '2025-09-15 10:30:00');
        $sheet->setCellValue('D2', 'Reunión semanal del equipo de desarrollo');
        $sheet->setCellValue('E2', 'primary');
        
        $sheet->setCellValue('A3', 'Viaje de negocios');
        $sheet->setCellValue('B3', '2025-09-20 08:00:00');
        $sheet->setCellValue('C3', '2025-09-22 18:00:00');
        $sheet->setCellValue('D3', 'Viaje a conferencia anual');
        $sheet->setCellValue('E3', 'info');
        
        $sheet->setCellValue('A4', 'Cita médica');
        $sheet->setCellValue('B4', '2025-09-25 11:00:00');
        $sheet->setCellValue('C4', '2025-09-25 12:00:00');
        $sheet->setCellValue('D4', 'Control médico anual');
        $sheet->setCellValue('E4', 'success');
        
        $sheet->setCellValue('A5', 'Entrega de proyecto');
        $sheet->setCellValue('B5', '2025-09-30 17:00:00');
        $sheet->setCellValue('C5', '2025-09-30 18:00:00');
        $sheet->setCellValue('D5', 'Entrega final del proyecto');
        $sheet->setCellValue('E5', 'danger');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        
        return response()->download($filePath, 'plantilla_importacion_eventos.xlsx');
    }

    private function canAccessEvent(Event $event): bool
    {
        $user = auth()->user();
        
        // Admin puede acceder a todos los eventos
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Usuario normal solo puede acceder a sus propios eventos
        return $event->user_id === $user->id;
    }
}