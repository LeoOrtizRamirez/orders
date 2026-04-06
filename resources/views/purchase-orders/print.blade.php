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
                margin: 0.5cm;
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
            }
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            -webkit-print-color-adjust: exact;
        }

        .page {
            background: white;
            max-width: 210mm;
            min-height: 297mm;
            margin: 1rem auto;
            padding: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        table { border-collapse: collapse; width: 100%; }
        th { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; }
        td { font-size: 0.85rem; padding: 0.5rem; border-bottom: 1px solid #f3f4f6; }
        
        .compact-text { font-size: 0.8rem; line-height: 1.25; }
        .label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #6b7280; margin-bottom: 0.1rem; }
    </style>
</head>
<body class="antialiased">
    <div class="no-print fixed top-4 right-4 flex gap-2">
        <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 px-6 rounded shadow transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimir
        </button>
        <button onclick="window.close()" class="bg-white border border-gray-300 text-gray-700 font-bold py-2 px-6 rounded shadow hover:bg-gray-50">
            Cerrar
        </button>
    </div>

    <div class="page">
        <!-- Header Compacto -->
        <div class="flex justify-between items-stretch mb-6 border-b-2 border-slate-800 pb-4">
            <div class="flex items-center gap-4">
                <div class="bg-slate-800 p-2 rounded-lg flex items-center justify-center h-16 w-16">
                    <img src="/assets/images/logo-white.png" alt="Logo" class="max-h-full max-w-full object-contain">
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tighter text-slate-800 leading-none">ORDEN DE COMPRA</h1>
                    <p class="text-[0.6rem] font-bold text-slate-500 mt-1 tracking-[0.3em]">SISTEMA DE GESTIÓN INTERNA</p>
                </div>
            </div>
            <div class="text-right flex flex-col justify-end">
                <p class="text-xs font-bold text-slate-400">DOCUMENTO Nº</p>
                <p class="text-xl font-mono font-black text-slate-800">{{ $purchaseOrder->order_number }}</p>
            </div>
        </div>

        <!-- Info Principal -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="col-span-1">
                <div class="label">Proveedor</div>
                <div class="compact-text font-bold text-slate-900 leading-tight">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</div>
                <div class="compact-text text-slate-500">{{ $purchaseOrder->supplier->contact_person ?? '' }}</div>
                <div class="compact-text text-slate-500">{{ $purchaseOrder->supplier->email ?? '' }}</div>
            </div>
            <div class="col-span-1 border-l border-gray-100 pl-4">
                <div class="label">Detalles de Fecha</div>
                <div class="flex justify-between compact-text">
                    <span class="text-slate-500">Emisión:</span>
                    <span class="font-semibold">{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between compact-text">
                    <span class="text-slate-500">Entrega:</span>
                    <span class="font-semibold">{{ $purchaseOrder->expected_delivery_date ? \Carbon\Carbon::parse($purchaseOrder->expected_delivery_date)->format('d/m/Y') : '-' }}</span>
                </div>
                <div class="flex justify-between compact-text">
                    <span class="text-slate-500">Estado:</span>
                    <span class="font-bold text-slate-800 uppercase text-[0.7rem]">{{ $purchaseOrder->status }}</span>
                </div>
            </div>
            <div class="col-span-1 border-l border-gray-100 pl-4">
                <div class="label">Responsables</div>
                <div class="compact-text"><span class="text-slate-500 italic text-[0.7rem]">Solicitado:</span> {{ $purchaseOrder->creator->name ?? 'N/A' }}</div>
                @if($purchaseOrder->approver)
                    <div class="compact-text"><span class="text-slate-500 italic text-[0.7rem]">Autorizado:</span> {{ $purchaseOrder->approver->name }}</div>
                @endif
                <div class="compact-text mt-1 text-[0.65rem] text-slate-400 italic">Impreso: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <div class="mb-6">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-y border-slate-200">
                        <th class="py-2 px-3 text-left w-12 text-slate-400">#</th>
                        <th class="py-2 px-2 text-left text-slate-600">Descripción del Producto</th>
                        <th class="py-2 px-2 text-left text-slate-600">SKU</th>
                        <th class="py-2 px-2 text-center text-slate-600 w-24">Cantidad</th>
                        <th class="py-2 px-2 text-center text-slate-600 w-20">Unidad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($purchaseOrder->items as $index => $item)
                    <tr>
                        <td class="text-slate-300 font-mono text-[0.7rem] px-3">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="py-2">
                            <div class="font-semibold text-slate-800">{{ $item->product->name ?? 'Producto no encontrado' }}</div>
                            @if($item->itemNotes && $item->itemNotes->count() > 0)
                                <div class="mt-0.5 space-y-0.5">
                                    @foreach($item->itemNotes as $note)
                                        <div class="text-[0.65rem] text-slate-400 flex gap-1">
                                            <span class="font-bold shrink-0">Nota:</span>
                                            <span class="italic leading-none">"{{ $note->note }}"</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="text-slate-500 font-mono text-[0.7rem] uppercase tracking-tighter">{{ $item->product->sku ?? '-' }}</td>
                        <td class="text-center font-bold text-slate-900">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-center text-slate-500 text-[0.7rem] uppercase">{{ $item->product->unit ?? 'un' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Observaciones -->
        @if($purchaseOrder->notes && $purchaseOrder->notes->count() > 0)
        <div class="mb-8 p-3 bg-slate-50 rounded-lg border border-slate-100">
            <div class="label text-slate-400 border-b border-slate-200 pb-1 mb-2">Observaciones Generales</div>
            <div class="space-y-2">
                @foreach($purchaseOrder->notes as $note)
                    <div class="text-[0.75rem] leading-snug">
                        <span class="font-bold text-slate-700">{{ $note->author->name ?? '' }}:</span>
                        <span class="text-slate-600 italic">"{{ $note->note }}"</span>
                        <span class="text-[0.6rem] text-slate-400 ml-1">({{ \Carbon\Carbon::parse($note->created_at)->format('d/m/Y H:i') }})</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Firmas -->
        <div class="mt-auto pt-10 grid grid-cols-2 gap-24">
            <div class="text-center">
                <div class="border-t border-slate-300 pt-2 px-10">
                    <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Solicitado Por</p>
                    <p class="text-[0.8rem] font-bold text-slate-700 mt-1">{{ $purchaseOrder->creator->name ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="text-center">
                <div class="border-t border-slate-300 pt-2 px-10">
                    <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Autorizado Por</p>
                    <p class="text-[0.8rem] font-bold text-slate-700 mt-1">{{ $purchaseOrder->approver->name ?? 'Firma Autorizada' }}</p>
                </div>
            </div>
        </div>

        <!-- Pie de Página -->
        <div class="mt-12 pt-4 border-t border-gray-100 flex justify-between items-center text-[0.55rem] text-slate-400 uppercase tracking-widest font-medium">
            <div>Documento Generado Automáticamente</div>
            <div>Página 01 de 01</div>
            <div>ID: {{ $purchaseOrder->id }}</div>
        </div>
    </div>
</body>
</html>
