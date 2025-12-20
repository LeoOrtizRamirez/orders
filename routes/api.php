<?php

use App\Http\Controllers\UserNoteController;
use App\Http\Controllers\ProfileMetricController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderStatusController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas públicas de autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas con Sanctum
Route::middleware(['auth:sanctum'])->group(function () {

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);

    // Reports
    Route::get('/reports/sales', [ReportController::class, 'salesReport'])->middleware('permission:view reports');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Información del usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles', 'permissions');
    });
    
    // Rutas de eventos con permisos específicos
    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index'])->middleware('permission:view events');
        Route::post('/', [EventController::class, 'store'])->middleware('permission:create events');
        Route::post('/import', [EventController::class, 'import'])->middleware('permission:create events');
        Route::get('/template', [EventController::class, 'downloadTemplate'])->middleware('permission:create events');
        Route::get('/{event}', [EventController::class, 'show'])->middleware('permission:view events');
        Route::put('/{event}', [EventController::class, 'update'])->middleware('permission:edit events');
        Route::delete('/{event}', [EventController::class, 'destroy'])->middleware('permission:delete events');
    });

    // Rutas de usuarios con permisos específicos
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store'])->middleware('permission:create users');
        Route::get('/{user}', [UserController::class, 'show']);
        Route::post('/{user}', [UserController::class, 'update'])->middleware('permission:edit users');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:delete users');
    });

    // Rutas de administración (solo para usuarios con permisos de administración)
    Route::prefix('admin')->group(function () {
        
        // Obtener roles disponibles
        Route::get('/roles', function () {
            $roles = \Spatie\Permission\Models\Role::all();
            return response()->json(['data' => $roles]);
        })->middleware('permission:view roles');
        
        // Rutas de roles
        Route::prefix('roles')->group(function () {
            Route::get('/permissions', [RoleController::class, 'getPermissions']);
            Route::get('/', [RoleController::class, 'index']);
            Route::post('/', [RoleController::class, 'store']);
            Route::get('/{role}', [RoleController::class, 'show']);
            Route::put('/{role}', [RoleController::class, 'update']);
            Route::delete('/{role}', [RoleController::class, 'destroy']);
        });

        // Rutas de permisos
        Route::prefix('permissions')->middleware('permission:manage permissions')->group(function () {
            Route::get('/roles', [PermissionController::class, 'getRolesWithPermissions']);
            Route::post('/roles/{role}/sync', [PermissionController::class, 'syncPermissionsToRole']);
            Route::apiResource('/', PermissionController::class)->except(['create', 'edit']);
            Route::put('/{permission}', [PermissionController::class, 'update']); 
        });
    });

    // Ruta para obtener los permisos del usuario actual
    Route::get('/user/permissions', function (Request $request) {
        return response()->json([
            'permissions' => $request->user()->getAllPermissions()->pluck('name'),
            'roles' => $request->user()->getRoleNames()
        ]);
    });


    // Rutas para Services
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->middleware('permission:view services');
        Route::post('/', [ServiceController::class, 'store'])->middleware('permission:create services');
        Route::get('/{service}', [ServiceController::class, 'show'])->middleware('permission:view services');
        Route::put('/{service}', [ServiceController::class, 'update'])->middleware('permission:edit services');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->middleware('permission:delete services');
        
        Route::get('/active/list', [ServiceController::class, 'activeServices'])->middleware('permission:view services');
        Route::put('/{id}/toggle-status', [ServiceController::class, 'toggleStatus'])->middleware('permission:edit services');
        Route::get('/slug/{slug}', [ServiceController::class, 'getServiceBySlug']); // Público o Auth genérico
    });

    // Rutas para Videos
    Route::prefix('videos')->group(function () {
        Route::get('/', [VideoController::class, 'index'])->middleware('permission:view videos');
        Route::post('/', [VideoController::class, 'store'])->middleware('permission:create videos');
        Route::get('/{video}', [VideoController::class, 'show'])->middleware('permission:view videos');
        Route::put('/{video}', [VideoController::class, 'update'])->middleware('permission:edit videos');
        Route::delete('/{video}', [VideoController::class, 'destroy'])->middleware('permission:delete videos');

        Route::get('/service/{serviceId}', [VideoController::class, 'byService'])->middleware('permission:view videos');
        Route::get('/free/list', [VideoController::class, 'freeVideos']); // Probablemente público o básico
        Route::put('/{id}/toggle-status', [VideoController::class, 'toggleStatus'])->middleware('permission:edit videos');
    });

    // Ruta principal para métricas
    Route::get('/profile-metrics', [ProfileMetricController::class, 'getUserMetrics'])
        ->name('profile-metrics.index');
    
    // Rutas de métricas solo para admin/manager
    Route::prefix('profile-metrics')->middleware('permission:manage profile_metrics')->group(function () {
        Route::get('/users', [ProfileMetricController::class, 'getUsersList'])
            ->name('profile-metrics.users');
        Route::get('/global', [ProfileMetricController::class, 'getGlobalMetrics'])
            ->name('profile-metrics.global');
    });

    Route::prefix('user-notes')->group(function () {
        Route::get('/', [UserNoteController::class, 'index'])->middleware('permission:view user_notes');
        Route::post('/', [UserNoteController::class, 'store'])->middleware('permission:create user_notes');
        Route::put('/{noteId}', [UserNoteController::class, 'update'])->middleware('permission:edit user_notes');
        Route::delete('/{noteId}', [UserNoteController::class, 'destroy'])->middleware('permission:delete user_notes');
    });

    // Rutas de productos
    Route::prefix('products')->group(function () {
        // Rutas específicas primero
        Route::get('/active/list', [ProductController::class, 'activeProducts'])->middleware('permission:view products');
        Route::get('/low-stock', [ProductController::class, 'lowStockProducts'])->middleware('permission:view products');
        Route::get('/categories', [ProductController::class, 'categories'])->middleware('permission:view products');
        Route::get('/units', [ProductController::class, 'units'])->middleware('permission:view products');
        Route::put('/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->middleware('permission:edit products');
        Route::get('/sku/{sku}', [ProductController::class, 'getProductBySku'])->middleware('permission:view products');
        Route::get('/{id}/pending-orders', [ProductController::class, 'pendingOrders'])->middleware('permission:view products');

        // Import/Export (ya tenían middleware, se mantienen)
        Route::post('/import', [ProductController::class, 'importProductsCsv'])->middleware('permission:create products');
        Route::get('/template', [ProductController::class, 'downloadProductsCsvTemplate'])->middleware('permission:create products');

        // CRUD estándar
        Route::get('/', [ProductController::class, 'index'])->middleware('permission:view products');
        Route::post('/', [ProductController::class, 'store'])->middleware('permission:create products');
        Route::get('/{product}', [ProductController::class, 'show'])->middleware('permission:view products');
        Route::put('/{product}', [ProductController::class, 'update'])->middleware('permission:edit products');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->middleware('permission:delete products');
    });

    // Rutas de proveedores
    Route::prefix('suppliers')->group(function () {
        Route::get('/active/list', [SupplierController::class, 'activeSuppliers'])->middleware('permission:view suppliers');
        Route::get('/for-select', [SupplierController::class, 'forSelect'])->middleware('permission:view suppliers');
        Route::put('/{id}/toggle-status', [SupplierController::class, 'toggleStatus'])->middleware('permission:toggle_supplier_status');
        
        // CRUD estándar
        Route::get('/', [SupplierController::class, 'index'])->middleware('permission:view suppliers');
        Route::post('/', [SupplierController::class, 'store'])->middleware('permission:create suppliers');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->middleware('permission:view suppliers');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->middleware('permission:edit suppliers');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->middleware('permission:delete suppliers');
    });

    // Rutas de órdenes de compra
    Route::prefix('purchase-orders')->group(function () {
        // Rutas específicas
        Route::prefix('{purchaseOrder}')->group(function () {
            Route::post('/split', [PurchaseOrderController::class, 'split'])->middleware('permission:create purchase_orders');
            Route::put('/status', [PurchaseOrderStatusController::class, 'updateStatus'])->middleware('permission:change_order_status');
        });

        // CRUD estándar
        Route::get('/', [PurchaseOrderController::class, 'index'])->middleware('permission:view purchase_orders');
        Route::post('/', [PurchaseOrderController::class, 'store'])->middleware('permission:create purchase_orders');
        Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->middleware('permission:view purchase_orders');
        Route::put('/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->middleware('permission:edit purchase_orders');
        Route::delete('/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->middleware('permission:delete purchase_orders');
    });
});
