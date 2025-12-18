<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\PurchaseOrder; // Add this import

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        // 1. Validar fechas de entrada y filtros
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : now()->subDays(30);
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : now();
        $category = $request->query('category');
        $orderStatus = $request->query('order_status');

        // Helper para aplicar filtros comunes a consultas de Items
        $applyItemFilters = function($query) use ($startDate, $endDate, $category, $orderStatus) {
            $query->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                  ->whereBetween('purchase_orders.order_date', [$startDate, $endDate]);
            
            if ($orderStatus) {
                $query->where('purchase_orders.status', $orderStatus);
            }
            
            if ($category) {
                // Aseguramos join con products si no está ya hecho en la consulta principal
                if (!$query->getQuery()->joins || !collect($query->getQuery()->joins)->contains('table', 'products')) {
                     $query->join('products', 'purchase_order_items.product_id', '=', 'products.id');
                }
                $query->where('products.category', $category);
            }
        };

        // Helper para aplicar filtros a consultas de Ordenes (Top Suppliers, Creators, Status)
        $applyOrderFilters = function($query) use ($startDate, $endDate, $category, $orderStatus) {
            $query->whereBetween('purchase_orders.order_date', [$startDate, $endDate]);

            if ($orderStatus) {
                $query->where('purchase_orders.status', $orderStatus);
            }

            if ($category) {
                // Filtrar órdenes que tengan al menos un ítem de esta categoría
                $query->whereHas('items.product', function($q) use ($category) {
                    $q->where('category', $category);
                });
            }
        };

        // 2. Obtener la cantidad de productos vendidos a lo largo del tiempo
        $salesQuery = PurchaseOrderItem::query();
        $applyItemFilters($salesQuery);
        
        $salesOverTime = $salesQuery->select(
                DB::raw('DATE(purchase_orders.order_date) as date'),
                DB::raw('SUM(purchase_order_items.quantity) as total_quantity')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // 3. Obtener los productos más vendidos
        $topSoldQuery = PurchaseOrderItem::query()
            ->join('products', 'purchase_order_items.product_id', '=', 'products.id'); // Join explícito base
        
        // Re-aplicamos lógica manual similar al helper pero ajustada pq ya tenemos el join products
        $topSoldQuery->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                     ->whereBetween('purchase_orders.order_date', [$startDate, $endDate]);
        
        if ($orderStatus) $topSoldQuery->where('purchase_orders.status', $orderStatus);
        if ($category) $topSoldQuery->where('products.category', $category);

        $topSoldProducts = $topSoldQuery->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(purchase_order_items.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // 4. Calcular estadísticas de productos (Globales o filtradas por categoría)
        $statsQuery = Product::query();
        if ($category) {
            $statsQuery->where('category', $category);
        }
        // Clonamos para reutilizar en los counts
        $totalProducts = (clone $statsQuery)->count();
        $activeProducts = (clone $statsQuery)->where('is_active', true)->count();
        $lowStockCount = (clone $statsQuery)->where('is_active', true)
            ->whereColumn('stock', '<=', 'reorder_point')
            ->where('stock', '>', 0)
            ->count();
        $outOfStockCount = (clone $statsQuery)->where('is_active', true)
            ->where('stock', 0)
            ->count();

        // 6. Ventas por Categoría (Si se selecciona una categoría, mostrará 100% esa, pero útil para validar)
        $catSalesQuery = PurchaseOrderItem::query()
            ->join('products', 'purchase_order_items.product_id', '=', 'products.id');
        
        $catSalesQuery->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                      ->whereBetween('purchase_orders.order_date', [$startDate, $endDate]);
        
        if ($orderStatus) $catSalesQuery->where('purchase_orders.status', $orderStatus);
        // Nota: NO filtramos por categoría aquí para permitir ver la proporción global, 
        // A MENOS que el usuario quiera ver desglose dentro de la categoría (que no hay subcategorías).
        // Si aplicamos el filtro $category aquí, el gráfico será un solo color.
        // Decisión: Aplicar el filtro para ser consistentes con "Reporte Filtrado".
        if ($category) $catSalesQuery->where('products.category', $category);

        $salesByCategory = $catSalesQuery->select(
                'products.category',
                DB::raw('SUM(purchase_order_items.quantity) as total_quantity')
            )
            ->groupBy('products.category')
            ->get();
        
        // 7. Estado de Órdenes
        $ordersStatusQuery = PurchaseOrder::query();
        $applyOrderFilters($ordersStatusQuery);
        $ordersByStatus = $ordersStatusQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // 8. Top Clientes (Suppliers)
        $suppliersQuery = PurchaseOrder::query()
            ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id');
        $applyOrderFilters($suppliersQuery);
        $topSuppliers = $suppliersQuery->select('suppliers.name', DB::raw('count(*) as total_orders'))
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderBy('total_orders', 'desc')
            ->limit(5)
            ->get();

        // 9. Ranking de Usuarios (Staff)
        $creatorsQuery = PurchaseOrder::query()
            ->join('users', 'purchase_orders.created_by', '=', 'users.id');
        $applyOrderFilters($creatorsQuery);
        $topCreators = $creatorsQuery->select('users.name', DB::raw('count(*) as total_orders'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_orders', 'desc')
            ->limit(5)
            ->get();

        // 10. Devolver respuesta
        return response()->json([
            'sales_over_time' => $salesOverTime,
            'top_sold_products' => $topSoldProducts,
            'product_stats' => [
                'total' => $totalProducts,
                'active' => $activeProducts,
                'low_stock' => $lowStockCount,
                'out_of_stock' => $outOfStockCount,
            ],
            'sales_by_category' => $salesByCategory,
            'orders_by_status' => $ordersByStatus,
            'top_suppliers' => $topSuppliers,
            'top_creators' => $topCreators,
        ]);
    }
}
