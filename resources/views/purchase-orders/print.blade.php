<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Compra - {{ $purchaseOrder->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        @media print {
            @page {
                size: A4;
                margin: 0.6cm;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
                background-color: white !important;
            }
            .no-print { display: none !important; }
            .page {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: 0 !important;
            }
            tr { page-break-inside: avoid; }
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .page {
            background: white;
            max-width: 210mm;
            min-height: 297mm;
            margin: 1rem auto;
            padding: 1rem 1.25rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        table { border-collapse: collapse; width: 100%; }
        th {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.25rem 0.4rem;
            color: #64748b;
            border-bottom: 1.5px solid #f1f5f9;
        }
        td {
            font-size: 0.7rem;
            padding: 0.18rem 0.4rem;
            border-bottom: 1px solid #f5f7fa;
            line-height: 1.15;
        }
        
        .label { 
            font-size: 0.6rem; 
            font-weight: 800; 
            text-transform: uppercase; 
            color: #94a3b8; 
            margin-bottom: 0.25rem;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body class="antialiased">
    <div class="no-print fixed top-4 right-4 flex gap-2">
        <button onclick="window.print()" class="bg-slate-900 hover:bg-black text-white font-bold py-2 px-6 rounded shadow transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimir Orden
        </button>
        <button onclick="window.close()" class="bg-white border border-slate-200 text-slate-600 font-bold py-2 px-6 rounded shadow hover:bg-slate-50 text-sm">
            Cerrar
        </button>
    </div>

    <div class="page">
        <!-- Encabezado Claro (Fondo Blanco) -->
        <div class="flex justify-between items-start mb-3">
            <div class="flex items-center gap-3">
                <div>
                    <img src="/assets/images/logo-white.png" alt="Logo" class="h-7 w-auto object-contain">
                </div>
                <div class="border-l border-slate-200 pl-3">
                    <h1 class="text-base font-black tracking-tight text-slate-900">ORDEN DE COMPRA</h1>
                    <p class="text-[0.5rem] font-bold text-slate-400 tracking-[0.25em] uppercase">Gestión de Operaciones e Inventario</p>
                </div>
            </div>
            <div class="text-right">
                <div class="label text-[0.5rem]">Número de Documento</div>
                <div class="text-base font-mono font-black text-slate-900 leading-none mt-0.5">#{{ $purchaseOrder->order_number }}</div>
            </div>
        </div>

        <!-- Información de la Orden -->
        <div class="grid grid-cols-3 gap-6 mb-3">
            <div class="col-span-1">
                <div class="label">Proveedor</div>
                <div class="text-sm font-bold text-slate-900 uppercase tracking-tight">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</div>
                <div class="text-xs text-slate-500 mt-1">{{ $purchaseOrder->supplier->contact_person ?? '' }}</div>
                <div class="text-xs text-slate-400 mt-0.5 italic">{{ $purchaseOrder->supplier->email ?? '' }}</div>
            </div>
            <div class="col-span-1 border-l border-slate-100 pl-6">
                <div class="label">Detalle de Fechas</div>
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="text-slate-400">Emisión:</span>
                    <span class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400">Entrega:</span>
                    <span class="font-bold text-slate-700">{{ $purchaseOrder->expected_delivery_date ? \Carbon\Carbon::parse($purchaseOrder->expected_delivery_date)->format('d/m/Y') : '-' }}</span>
                </div>
            </div>
            <div class="col-span-1 border-l border-slate-100 pl-6">
                <div class="label">Estado Actual</div>
                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.6rem] font-black uppercase tracking-wider bg-slate-100 text-slate-800 border border-slate-200">
                    {{ $purchaseOrder->status }}
                </div>
                <div class="text-[0.55rem] text-slate-400 mt-2 font-medium">
                    Impreso: {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <div class="flex-grow">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="text-left w-10">Ref</th>
                        <th class="text-left">Descripción del Producto</th>
                        <th class="text-left w-32">SKU</th>
                        <th class="text-center w-24">Cantidad</th>
                        <th class="text-center w-20">Stock</th>
                        <th class="text-center w-16">Und</th>
                    </tr>
                </thead>
                <tbody class="text-slate-700">
                    @foreach($purchaseOrder->items as $index => $item)
                    @php
                        $stockValue = $item->product->stock ?? null;
                        $reorderPoint = $item->product->reorder_point ?? 0;
                        $stockColor = 'text-slate-400';
                        if ($stockValue !== null) {
                            if ($stockValue <= 0) {
                                $stockColor = 'text-red-600';
                            } elseif ($stockValue <= $reorderPoint) {
                                $stockColor = 'text-amber-500';
                            } else {
                                $stockColor = 'text-emerald-600';
                            }
                        }
                        $stockDisplay = $stockValue === null
                            ? '-'
                            : (fmod((float)$stockValue, 1) === 0.0
                                ? number_format($stockValue, 0)
                                : number_format($stockValue, 2));
                    @endphp
                    <tr>
                        <td class="text-slate-300 font-mono text-[0.6rem]">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="font-semibold text-slate-800 text-[0.72rem] leading-tight">{{ $item->product->name ?? 'N/A' }}</div>
                            @foreach($item->itemNotes as $note)
                                <div class="text-[0.55rem] text-slate-400 border-l-2 border-slate-100 pl-1.5 leading-tight">
                                    Nota: {{ $note->note }}
                                </div>
                            @endforeach
                        </td>
                        <td class="text-slate-500 font-mono text-[0.6rem] uppercase tracking-tighter">{{ $item->product->sku ?? '-' }}</td>
                        <td class="text-center font-black text-slate-900 text-[0.72rem]">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-center font-bold text-[0.72rem] {{ $stockColor }}">{{ $stockDisplay }}</td>
                        <td class="text-center text-slate-400 text-[0.55rem] font-bold uppercase">{{ $item->product->unit ?? 'un' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Observaciones -->
        @if($purchaseOrder->notes && $purchaseOrder->notes->count() > 0)
        <div class="mt-3 p-2 bg-slate-50/50 rounded-xl border border-slate-100">
            <div class="label mb-2">Observaciones Generales de la Orden</div>
            <div class="space-y-2">
                @foreach($purchaseOrder->notes as $note)
                    <div class="text-[0.7rem] text-slate-600 leading-relaxed">
                        <span class="font-bold text-slate-900 uppercase text-[0.55rem] mr-2 tracking-wider">{{ $note->author->name ?? 'Usuario' }}:</span>
                        "{{ $note->note }}"
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Pie de Página Minimalista -->
        <div class="pt-2 mt-3 border-t border-slate-100 flex justify-between items-center text-[0.5rem] text-slate-300 uppercase tracking-[0.2em]">
            <div>Documento Generado</div>
            <div class="font-mono text-[0.6rem] normal-case">ID: {{ $purchaseOrder->id }}</div>
            <div>Página 1 de 1</div>
        </div>
    </div>
</body>
</html>
