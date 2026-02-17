<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view roles')->only(['index', 'show']);
        $this->middleware('permission:create roles')->only(['store']);
        $this->middleware('permission:edit roles')->only(['update']);
        $this->middleware('permission:delete roles')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $query = Role::with('permissions');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $roles = $query->paginate($perPage);
        
        return response()->json([
            'data' => $roles->items(),
            'meta' => [
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'message' => 'Rol creado exitosamente',
            'data' => $role->load('permissions')
        ], 201);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'data' => $role->load('permissions')
        ]);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        // No permitir modificar roles del sistema
        if ($role->is_system) {
            return response()->json(['error' => 'No se pueden modificar roles del sistema'], 422);
        }

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($role->id)
            ],
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id' // Validar por ID
        ]);

        if (isset($validated['name'])) {
            $role->update(['name' => $validated['name']]);
        }

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'message' => 'Rol actualizado exitosamente',
            'data' => $role->load('permissions')
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        // Prevenir eliminación de roles del sistema
        if (in_array($role->name, ['admin', 'user'])) {
            return response()->json([
                'message' => 'No se puede eliminar un rol del sistema'
            ], 422);
        }

        $role->delete();

        return response()->json([
            'message' => 'Rol eliminado exitosamente'
        ], 200);
    }

    public function getPermissions(): JsonResponse
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode(' ', $permission->name)[0];
        });

        return response()->json([
            'data' => $permissions
        ]);
    }
}