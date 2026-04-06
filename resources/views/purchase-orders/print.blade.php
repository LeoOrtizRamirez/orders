<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Compra - {{ $purchaseOrder->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f3f4f6;
            padding: 2rem;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
        }
        .page {
            background: white;
            max-width: 210mm;
            margin: 0 auto;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        @media print {
            .page {
                box-shadow: none;
                margin: 0;
                width: 100%;
                max-width: none;
            }
        }
    </style>
</head>
<body class="bg-gray-100 antialiased">
    <div class="no-print fixed top-4 right-4 flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg transition duration-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimir Orden
        </button>
        <button onclick="window.close()" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg transition duration-200">
            Cerrar
        </button>
    </div>

    <div class="page">
        <!-- Header -->
        <div class="flex justify-between items-start border-b-2 border-gray-100 pb-8 mb-8">
            <div class="flex items-center gap-6">
                <div class="bg-gray-50 p-3 rounded-2xl border border-gray-100">
                    <img src="/assets/images/logo.svg" alt="Logo" class="h-20 w-auto" onerror="this.src='/assets/images/favicon.png'; this.style.height='50px'">
                </div>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Orden de Compra</h1>
                    <p class="text-sm font-bold text-blue-600 uppercase tracking-widest">Documento de Control Interno</p>
                </div>
            </div>
            <div class="text-right">
                <div class="bg-gray-900 text-white px-4 py-2 rounded-lg inline-block mb-3">
                    <span class="text-xs uppercase font-bold block leading-none opacity-70">Número de Orden</span>
                    <span class="text-xl font-mono font-bold">#{{ $purchaseOrder->order_number }}</span>
                </div>
                <div class="space-y-1 text-sm text-gray-600">
                    <p><span class="font-bold">Fecha de Orden:</span> {{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('d/m/Y') }}</p>
                    <p><span class="font-bold">Fecha de Impresión:</span> {{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-12 mb-10">
            <!-- Proveedor -->
            <div>
                <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3 border-b border-gray-100 pb-1">Proveedor</h2>
                <div class="text-gray-900">
                    <p class="text-lg font-bold">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</p>
                    <p class="text-sm">{{ $purchaseOrder->supplier->contact_person ?? '' }}</p>
                    <p class="text-sm">{{ $purchaseOrder->supplier->email ?? '' }}</p>
                    <p class="text-sm">{{ $purchaseOrder->supplier->phone ?? '' }}</p>
                </div>
            </div>

            <!-- Detalles de Entrega -->
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3 border-b border-gray-200 pb-1">Detalles de Entrega</h2>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-medium">Estado:</span>
                        <span class="font-bold uppercase text-blue-700">{{ $purchaseOrder->status }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-medium">Entrega Estimada:</span>
                        <span class="font-bold">{{ $purchaseOrder->expected_delivery_date ? \Carbon\Carbon::parse($purchaseOrder->expected_delivery_date)->format('d/m/Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-medium">Creado por:</span>
                        <span class="font-bold">{{ $purchaseOrder->creator->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-10">
            <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 border-b border-gray-100 pb-1">Productos Solicitados</h2>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-900 text-white">
                        <th class="py-3 px-4 rounded-l-lg text-xs font-black uppercase tracking-widest">Descripción / Producto</th>
                        <th class="py-3 px-4 text-xs font-black uppercase tracking-widest">SKU</th>
                        <th class="py-3 px-4 text-center text-xs font-black uppercase tracking-widest">Cantidad</th>
                        <th class="py-3 px-4 rounded-r-lg text-center text-xs font-black uppercase tracking-widest">Unidad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($purchaseOrder->items as $item)
                    <tr>
                        <td class="py-4 px-4">
                            <div class="font-bold text-gray-900">{{ $item->product->name ?? 'Producto no encontrado' }}</div>
                            @if($item->itemNotes && $item->itemNotes->count() > 0)
                                <div class="mt-1 space-y-1">
                                    @foreach($item->itemNotes as $note)
                                        <div class="text-[10px] text-gray-500 italic flex gap-1">
                                            <span class="font-bold text-gray-700">{{ $note->author->name ?? '' }}:</span>
                                            <span>"{{ $note->note }}"</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-sm font-mono text-gray-600">{{ $item->product->sku ?? '-' }}</td>
                        <td class="py-4 px-4 text-center font-bold text-gray-900">{{ $item->quantity }}</td>
                        <td class="py-4 px-4 text-center text-sm text-gray-500">{{ $item->product->unit ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Notes Section -->
        @if($purchaseOrder->notes && $purchaseOrder->notes->count() > 0)
        <div class="mb-10 bg-blue-50/50 rounded-xl p-5 border border-blue-100">
            <h2 class="text-xs font-black uppercase tracking-widest text-blue-400 mb-3 border-b border-blue-100 pb-1">Observaciones Generales</h2>
            <div class="space-y-3">
                @foreach($purchaseOrder->notes as $note)
                    <div class="text-sm">
                        <p class="font-bold text-blue-900 text-xs mb-1">{{ $note->author->name ?? '' }} - {{ \Carbon\Carbon::parse($note->created_at)->format('d/m/Y H:i') }}</p>
                        <p class="text-gray-700 italic">"{{ $note->note }}"</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Footer / Signatures -->
        <div class="mt-20">
            <div class="grid grid-cols-2 gap-20">
                <div class="text-center">
                    <div class="border-t-2 border-gray-900 pt-3">
                        <p class="text-sm font-black uppercase tracking-tighter">Solicitado por</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $purchaseOrder->creator->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="border-t-2 border-gray-900 pt-3">
                        <p class="text-sm font-black uppercase tracking-tighter">Autorizado por</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $purchaseOrder->approver->name ?? 'Firma Autorizada' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-16 pt-8 border-t border-gray-100 text-center">
                <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em]">Este documento es una representación gráfica de una orden de compra digital.</p>
            </div>
        </div>
    </div>

    <script>
        // Auto-print when loaded (optional, but convenient)
        window.onload = function() {
            // window.print();
        }
    </script>
</body>
</html>
