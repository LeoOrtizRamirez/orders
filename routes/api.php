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

    // Rutas de usuario only
    Route::prefix('user-only')->group(function () {
        Route::put('/{user}', [UserController::class, 'updateUserOnly'])->middleware('permission:edit users');
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


    Route::apiResource('services', ServiceController::class);
    // Rutas adicionales para Services
    Route::prefix('services')->group(function () {
        Route::get('/active/list', [ServiceController::class, 'activeServices']);
        Route::put('/{id}/toggle-status', [ServiceController::class, 'toggleStatus']);
        Route::get('/slug/{slug}', [ServiceController::class, 'getServiceBySlug']);
    });

    Route::apiResource('videos', VideoController::class);
    // Rutas adicionales para Videos
    Route::prefix('videos')->group(function () {
        Route::get('/service/{serviceId}', [VideoController::class, 'byService']);
        Route::get('/free/list', [VideoController::class, 'freeVideos']);
        Route::put('/{id}/toggle-status', [VideoController::class, 'toggleStatus']);
    });

    // Ruta principal para métricas
    Route::get('/profile-metrics', [ProfileMetricController::class, 'getUserMetrics'])
        ->name('profile-metrics.index');
    
    // Rutas solo para admin
    Route::prefix('profile-metrics')->group(function () {
        Route::get('/users', [ProfileMetricController::class, 'getUsersList'])
            ->name('profile-metrics.users');
        Route::get('/global', [ProfileMetricController::class, 'getGlobalMetrics'])
            ->name('profile-metrics.global');
    });

    Route::prefix('user-notes')->group(function () {
        Route::get('/user/{userId}', [UserNoteController::class, 'index']);
        Route::post('/user/{userId}', [UserNoteController::class, 'store']);
        Route::put('/user/{userId}/{noteId}', [UserNoteController::class, 'update']);
        Route::delete('/user/{userId}/{noteId}', [UserNoteController::class, 'destroy']);
    });

    // Rutas de productos
    Route::prefix('products')->group(function () {
        Route::get('/active/list', [ProductController::class, 'activeProducts']);
        Route::get('/low-stock', [ProductController::class, 'lowStockProducts']);
        Route::get('/categories', [ProductController::class, 'categories']);
        Route::put('/{id}/toggle-status', [ProductController::class, 'toggleStatus']);
        Route::get('/sku/{sku}', [ProductController::class, 'getProductBySku']);
        // New routes for CSV import/export
        Route::post('/import', [ProductController::class, 'importProductsCsv'])->middleware('permission:create products');
        Route::get('/template', [ProductController::class, 'downloadProductsCsvTemplate'])->middleware('permission:create products');
    });
    Route::apiResource('products', ProductController::class);

    // Rutas de proveedores
    Route::get('/suppliers/active/list', [SupplierController::class, 'activeSuppliers']);
    Route::get('/suppliers/for-select', [SupplierController::class, 'forSelect']);
    Route::put('/suppliers/{id}/toggle-status', [SupplierController::class, 'toggleStatus']);
    Route::apiResource('suppliers', SupplierController::class);

    // Rutas de órdenes de compra (todas unificadas y ordenadas por especificidad)
    Route::prefix('purchase-orders')->group(function () {
        // Rutas más específicas primero
        Route::get('/pending/list', [PurchaseOrderController::class, 'pending']);
        Route::get('/kanban', [PurchaseOrderController::class, 'kanbanIndex'])->middleware('permission:view purchase_orders');

        // Rutas con {purchaseOrder} para PurchaseOrderStatusController
        Route::prefix('{purchaseOrder}')->group(function () {
            Route::put('/status', [PurchaseOrderStatusController::class, 'updateStatus']);
            Route::post('/submit', [PurchaseOrderStatusController::class, 'submit']);
            Route::post('/approve', [PurchaseOrderStatusController::class, 'approve']);
            Route::post('/reject', [PurchaseOrderStatusController::class, 'reject']);
            Route::post('/mark-ordered', [PurchaseOrderStatusController::class, 'markOrdered']);
            Route::post('/receive', [PurchaseOrderStatusController::class, 'receive']);
            Route::post('/cancel', [PurchaseOrderStatusController::class, 'cancel']);
            Route::post('/reopen', [PurchaseOrderStatusController::class, 'reopen']);
        });

        // Rutas con {id} para PurchaseOrderController (estas deben ir después de las anteriores si usan {id})
        Route::put('/{id}/approve', [PurchaseOrderController::class, 'approve']);
        Route::put('/{id}/reject', [PurchaseOrderController::class, 'reject']);
        Route::put('/{id}/mark-ordered', [PurchaseOrderController::class, 'markAsOrdered']);
        Route::put('/{id}/receive', [PurchaseOrderController::class, 'receive']);
    });

    // apiResource debe ir al final para no interceptar las rutas más específicas
    Route::apiResource('purchase-orders', PurchaseOrderController::class);
});
