<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        // 1. Validar fechas de entrada
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : now()->subDays(30);
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : now();

        // 2. Obtener la cantidad de productos vendidos a lo largo del tiempo
        $salesOverTime = PurchaseOrderItem::query()
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->whereBetween('purchase_orders.order_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(purchase_orders.order_date) as date'),
                DB::raw('SUM(purchase_order_items.quantity) as total_quantity')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // 3. Obtener los productos más vendidos
        $topSoldProducts = PurchaseOrderItem::query()
            ->join('products', 'purchase_order_items.product_id', '=', 'products.id')
            ->whereHas('purchaseOrder', function($q) use ($startDate, $endDate) {
                $q->whereBetween('order_date', [$startDate, $endDate]);
            })
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(purchase_order_items.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // 4. Obtener todos los productos con su stock actual
        $allProducts = Product::query()
            ->select('id', 'name', 'sku', 'stock')
            ->orderBy('name', 'asc')
            ->get();

        // 5. Devolver una respuesta JSON unificada
        return response()->json([
            'sales_over_time' => $salesOverTime,
            'top_sold_products' => $topSoldProducts,
            'product_stock' => $allProducts,
        ]);
    }
}
