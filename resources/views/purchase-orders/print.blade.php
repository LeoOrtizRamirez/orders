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
                margin: 0;
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
                width: 100% !important;
                height: 297mm;
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
            margin: 1rem auto;
            padding: 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .content-padding {
            padding: 1.5rem 2rem;
        }

        table { border-collapse: collapse; width: 100%; }
        th { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.75rem 0.5rem; }
        td { font-size: 0.8rem; padding: 0.5rem; border-bottom: 1px solid #f3f4f6; }
        
        .label { font-size: 0.6rem; font-weight: 800; text-transform: uppercase; color: #9ca3af; margin-bottom: 0.1rem; }
    </style>
</head>
<body class="antialiased">
    <div class="no-print fixed top-4 right-4 flex gap-2">
        <button onclick="window.print()" class="bg-slate-900 hover:bg-black text-white font-bold py-2 px-6 rounded shadow transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimir
        </button>
        <button onclick="window.close()" class="bg-white border border-gray-300 text-gray-700 font-bold py-2 px-6 rounded shadow hover:bg-gray-50">
            Cerrar
        </button>
    </div>

    <div class="page">
        <!-- Cabecera Corporativa Negra (para logo blanco) -->
        <div class="bg-slate-950 text-white px-8 py-6 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <img src="/assets/images/logo-white.png" alt="Logo" class="h-12 w-auto object-contain">
                <div class="border-l border-slate-700 h-10 ml-2"></div>
                <div class="ml-4">
                    <h1 class="text-xl font-extrabold tracking-widest leading-none">ORDEN DE COMPRA</h1>
                    <p class="text-[0.5rem] font-medium text-slate-400 mt-1 tracking-[0.5em] uppercase">Control de Suministros e Inventario</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[0.6rem] font-bold text-slate-500 uppercase tracking-[0.2em]">Referencia Interna</p>
                <p class="text-xl font-mono font-black tracking-tighter">#{{ $purchaseOrder->order_number }}</p>
            </div>
        </div>

        <div class="content-padding flex-grow">
            <!-- Bloque de Información -->
            <div class="grid grid-cols-3 gap-10 mb-8 pb-8 border-b border-slate-100">
                <div>
                    <div class="label">Proveedor Autorizado</div>
                    <div class="text-[0.85rem] font-bold text-slate-900 leading-tight uppercase">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</div>
                    <div class="text-[0.75rem] text-slate-500 mt-1">{{ $purchaseOrder->supplier->contact_person ?? '' }}</div>
                    <div class="text-[0.7rem] text-slate-400 mt-0.5">{{ $purchaseOrder->supplier->email ?? '' }}</div>
                </div>
                <div>
                    <div class="label">Cronología</div>
                    <div class="flex justify-between text-[0.75rem] mb-1">
                        <span class="text-slate-400 font-medium">F. Emisión:</span>
                        <span class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between text-[0.75rem]">
                        <span class="text-slate-400 font-medium">F. Entrega:</span>
                        <span class="font-bold text-slate-700">{{ $purchaseOrder->expected_delivery_date ? \Carbon\Carbon::parse($purchaseOrder->expected_delivery_date)->format('d/m/Y') : '-' }}</span>
                    </div>
                </div>
                <div>
                    <div class="label">Estado de Gestión</div>
                    <div class="inline-block px-2 py-0.5 bg-slate-900 text-white text-[0.6rem] font-black uppercase rounded tracking-wider">
                        {{ $purchaseOrder->status }}
                    </div>
                    <div class="text-[0.6rem] text-slate-400 mt-2 italic font-medium">
                        Sistema: {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>

            <!-- Tabla de Ítems -->
            <div class="mb-10">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                            <th class="text-left w-10">ID</th>
                            <th class="text-left">Descripción de Materiales / Productos</th>
                            <th class="text-left w-32">SKU / Ref.</th>
                            <th class="text-center w-24">Cantidad</th>
                            <th class="text-center w-16">Und.</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700">
                        @foreach($purchaseOrder->items as $index => $item)
                        <tr>
                            <td class="text-slate-300 font-mono text-[0.65rem]">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="font-bold text-slate-800">{{ $item->product->name ?? 'N/A' }}</div>
                                @foreach($item->itemNotes as $note)
                                    <div class="text-[0.65rem] text-slate-400 italic mt-1 bg-slate-50 p-1 rounded border-l-2 border-slate-200">
                                        Obs: {{ $note->note }}
                                    </div>
                                @endforeach
                            </td>
                            <td class="text-slate-500 font-mono text-[0.7rem] uppercase tracking-tighter">{{ $item->product->sku ?? '-' }}</td>
                            <td class="text-center font-black text-slate-900 text-md">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-center text-slate-400 text-[0.65rem] font-bold uppercase">{{ $item->product->unit ?? 'un' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Notas de la Orden -->
            @if($purchaseOrder->notes && $purchaseOrder->notes->count() > 0)
            <div class="mb-12">
                <div class="label mb-2 border-b border-slate-100 pb-1">Comentarios y Observaciones de Oficina</div>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($purchaseOrder->notes as $note)
                        <div class="text-[0.7rem] text-slate-600 leading-relaxed bg-gray-50/50 p-2 rounded border border-gray-100">
                            <span class="font-black text-slate-800 uppercase text-[0.55rem] block mb-1 border-b border-gray-200 pb-0.5">
                                {{ $note->author->name ?? 'Usuario' }} · {{ \Carbon\Carbon::parse($note->created_at)->format('d/m/Y') }}
                            </span>
                            "{{ $note->note }}"
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Área de Firmas -->
            <div class="mt-auto grid grid-cols-2 gap-40 pt-16 border-t border-slate-50">
                <div class="text-center">
                    <div class="border-t-2 border-slate-900 pt-3">
                        <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Solicitado Por</p>
                        <p class="text-sm font-black text-slate-900 uppercase">Administrator</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="border-t-2 border-slate-900 pt-3">
                        <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Autorizado Por</p>
                        <p class="text-sm font-black text-slate-900 uppercase">Jhon Carlos Prado</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie de Página -->
        <div class="bg-slate-50 px-8 py-3 flex justify-between items-center text-[0.5rem] text-slate-400 font-bold uppercase tracking-[0.3em]">
            <div>Documento Oficial de la Empresa</div>
            <div class="text-slate-300 tracking-normal font-mono italic">PO-REF: {{ $purchaseOrder->id }}</div>
            <div>Página 01 / 01</div>
        </div>
    </div>
</body>
</html>
