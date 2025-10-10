<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserOnly;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Storage;
use App\Helpers\StorageHelper;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::with('roles')->paginate(10);
        
        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Asignar rol
        $user->assignRole($validated['role']);

        return response()->json($user->load('roles'), 201);
    }

    public function show(User $user): JsonResponse
    {
        // Cargar las relaciones necesarias
        $user->load('roles', 'user_only');
        
        return response()->json($user);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'sometimes|exists:roles,name'
        ]);

        $updateData = [
            'name' => $validated['name'] ?? $user->name,
            'email' => $validated['email'] ?? $user->email,
            'phone' => $validated['phone'] ?? $user->phone,
        ];

        // Actualizar contraseña
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // Manejar imagen
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior
            if ($user->image) {
                Storage::delete('public/' . $user->image);
                StorageHelper::deleteFromPublic($user->image);
            }
            
            // Guardar nueva imagen
            $imagePath = $request->file('image')->store('users', 'public');
            $updateData['image'] = $imagePath;
            
            // Sincronizar con carpeta pública
            StorageHelper::syncToPublic($imagePath);
        }

        $user->update($updateData);
        
        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json($user->load('roles', 'user_only'));
    }

    public function destroy(User $user): JsonResponse
    {
        // Prevenir que el usuario se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Cannot delete your own account'], 422);
        }

        $user->delete();

        return response()->json(null, 204);
    }

    public function updateUserOnly(Request $request, $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        
        $validated = $request->validate([
            'user_name' => 'sometimes|string|max:255|unique:user_only,user_name,' . $user->id . ',user_id',
        ]);

        // Buscar o crear el registro user_only
        $userOnly = $user->user_only ?? new UserOnly();
        
        if (isset($validated['user_name'])) {
            $userOnly->user_name = $validated['user_name'];
        }
        
        $userOnly->user_password = $request['password'];
        
        $user->user_only()->save($userOnly);
        
        return response()->json($userOnly);
    }

}