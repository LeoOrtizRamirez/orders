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
                margin: 0.8cm;
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
        }

        .page {
            background: white;
            max-width: 210mm;
            min-height: 297mm;
            margin: 1rem auto;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        table { border-collapse: collapse; width: 100%; }
        th { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.5rem; }
        td { font-size: 0.8rem; padding: 0.4rem 0.5rem; border-bottom: 1px solid #f3f4f6; }
        
        .compact-text { font-size: 0.75rem; line-height: 1.2; }
        .label { font-size: 0.6rem; font-weight: 800; text-transform: uppercase; color: #9ca3af; margin-bottom: 0.1rem; }
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

    <div class="page flex flex-col">
        <!-- Header sin fondo de logo -->
        <div class="flex justify-between items-start mb-8 border-b border-slate-100 pb-6">
            <div class="flex items-center gap-5">
                <img src="/assets/images/logo.svg" alt="Logo" class="h-14 w-auto object-contain" onerror="this.src='/assets/images/logo-dark.png'">
                <div class="border-l border-slate-200 pl-5">
                    <h1 class="text-xl font-black tracking-tighter text-slate-800 leading-none">ORDEN DE COMPRA</h1>
                    <p class="text-[0.55rem] font-bold text-slate-400 mt-1 tracking-[0.4em] uppercase">Documento de Gestión Interna</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[0.6rem] font-bold text-slate-300 uppercase tracking-widest">Número de Orden</p>
                <p class="text-lg font-mono font-black text-slate-800 leading-none">#{{ $purchaseOrder->order_number }}</p>
            </div>
        </div>

        <!-- Info Principal Compacta -->
        <div class="grid grid-cols-3 gap-8 mb-8">
            <div>
                <div class="label">Proveedor</div>
                <div class="compact-text font-bold text-slate-900 leading-tight uppercase">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</div>
                <div class="compact-text text-slate-500 mt-0.5">{{ $purchaseOrder->supplier->contact_person ?? '' }}</div>
                <div class="compact-text text-slate-400 text-[0.65rem]">{{ $purchaseOrder->supplier->email ?? '' }}</div>
            </div>
            <div>
                <div class="label">Fechas del Documento</div>
                <div class="flex justify-between compact-text mb-0.5">
                    <span class="text-slate-400">Emisión:</span>
                    <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between compact-text">
                    <span class="text-slate-400">Entrega:</span>
                    <span class="font-semibold text-slate-700">{{ $purchaseOrder->expected_delivery_date ? \Carbon\Carbon::parse($purchaseOrder->expected_delivery_date)->format('d/m/Y') : '-' }}</span>
                </div>
            </div>
            <div>
                <div class="label">Estado y Registro</div>
                <div class="inline-block px-2 py-0.5 bg-slate-100 text-slate-700 text-[0.65rem] font-bold uppercase rounded mb-1">
                    {{ $purchaseOrder->status }}
                </div>
                <div class="compact-text text-[0.6rem] text-slate-400 italic">
                    Generado: {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <!-- Tabla de Productos Profesional -->
        <div class="mb-8">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-slate-800 text-slate-800">
                        <th class="text-left w-8">#</th>
                        <th class="text-left">Descripción del Producto</th>
                        <th class="text-left w-32">Referencia / SKU</th>
                        <th class="text-center w-20">Cant.</th>
                        <th class="text-center w-16">Und.</th>
                    </tr>
                </thead>
                <tbody class="text-slate-700">
                    @foreach($purchaseOrder->items as $index => $item)
                    <tr>
                        <td class="text-slate-300 font-mono text-[0.65rem]">{{ $index + 1 }}</td>
                        <td>
                            <div class="font-bold text-slate-800">{{ $item->product->name ?? 'N/A' }}</div>
                            @foreach($item->itemNotes as $note)
                                <div class="text-[0.65rem] text-slate-400 italic mt-0.5">Nota: "{{ $note->note }}"</div>
                            @endforeach
                        </td>
                        <td class="text-slate-500 font-mono text-[0.7rem]">{{ $item->product->sku ?? '-' }}</td>
                        <td class="text-center font-bold text-slate-900">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-center text-slate-500 text-[0.65rem] uppercase">{{ $item->product->unit ?? 'un' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Observaciones Generales -->
        @if($purchaseOrder->notes && $purchaseOrder->notes->count() > 0)
        <div class="mb-10 p-3 border-l-2 border-slate-200 bg-slate-50/50">
            <div class="label mb-1">Observaciones Generales</div>
            <div class="space-y-1.5">
                @foreach($purchaseOrder->notes as $note)
                    <div class="text-[0.7rem] text-slate-600 leading-relaxed">
                        <span class="font-bold text-slate-800 uppercase text-[0.6rem] mr-1">{{ $note->author->name ?? '' }}:</span>
                        "{{ $note->note }}"
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Firmas Personalizadas -->
        <div class="mt-auto grid grid-cols-2 gap-32 pb-10">
            <div class="text-center">
                <div class="border-t border-slate-200 pt-3">
                    <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-1">Solicitado Por</p>
                    <p class="text-sm font-bold text-slate-800">Administrator</p>
                </div>
            </div>
            <div class="text-center">
                <div class="border-t border-slate-200 pt-3">
                    <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-1">Autorizado Por</p>
                    <p class="text-sm font-bold text-slate-800">Jhon Carlos Prado</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="pt-5 border-t border-slate-100 flex justify-between items-center text-[0.5rem] text-slate-300 uppercase tracking-widest">
            <div>Documento de Control Interno v2.0</div>
            <div>Página 1 de 1</div>
        </div>
    </div>
</body>
</html>
