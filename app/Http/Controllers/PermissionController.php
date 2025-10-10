<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view permissions')->only(['index', 'show']);
        $this->middleware('permission:create permissions')->only(['store']);
        $this->middleware('permission:edit permissions')->only(['update']);
        $this->middleware('permission:delete permissions')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        $permissions = Permission::paginate(15);
        
        return response()->json([
            'data' => $permissions->items(),
            'meta' => [
                'current_page' => $permissions->currentPage(),
                'last_page' => $permissions->lastPage(),
                'per_page' => $permissions->perPage(),
                'total' => $permissions->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string|max:500'
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => 'web'
        ]);

        return response()->json([
            'message' => 'Permiso creado exitosamente',
            'data' => $permission
        ], 201);
    }

    public function show(Permission $permission): JsonResponse
    {
        return response()->json([
            'data' => $permission->load('roles')
        ]);
    }

    public function update(Request $request, Permission $permission): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('permissions')->ignore($permission->id)
            ],
            'description' => 'nullable|string|max:500'
        ]);

        $permission->update($validated);

        return response()->json([
            'message' => 'Permiso actualizado exitosamente',
            'data' => $permission
        ]);
    }

    public function destroy(Permission $permission): JsonResponse
    {
        // Prevenir eliminación de permisos del sistema
        $systemPermissions = [
            'view events', 'create events', 'edit events', 'delete events',
            'view users', 'create users', 'edit users', 'delete users',
            'manage roles', 'manage permissions'
        ];

        if (in_array($permission->name, $systemPermissions)) {
            return response()->json([
                'message' => 'No se puede eliminar un permiso del sistema'
            ], 422);
        }

        $permission->delete();

        return response()->json([
            'message' => 'Permiso eliminado exitosamente'
        ], 200);
    }

    public function getRolesWithPermissions(): JsonResponse
    {
        $roles = Role::with('permissions')->get();
        
        return response()->json([
            'data' => $roles
        ]);
    }

    public function syncPermissionsToRole(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role->syncPermissions($validated['permissions']);

        return response()->json([
            'message' => 'Permisos sincronizados exitosamente',
            'data' => $role->load('permissions')
        ]);
    }
}