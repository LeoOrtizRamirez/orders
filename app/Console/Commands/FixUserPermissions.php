<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixUserPermissions extends Command
{
    protected $signature = 'permissions:fix-user {email}';
    protected $description = 'Fix user permissions and assign admin role';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario con email {$email} no encontrado.");
            return 1;
        }

        $this->info("Usuario: {$user->email}");
        $this->info("Roles actuales: " . implode(', ', $user->getRoleNames()->toArray()));
        
        // Crear o obtener rol admin
        $adminRole = Role::firstOrCreate(['name' => 'admin'], [
            'description' => 'Administrador del sistema',
            'is_system' => true
        ]);

        // Crear permisos si no existen
        $requiredPermissions = [
            'view roles', 'manage roles', 'view permissions', 'manage permissions',
            'view users', 'create users', 'edit users', 'delete users',
            'view events', 'create events', 'edit events', 'delete events'
        ];

        foreach ($requiredPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Asignar todos los permisos al rol admin
        $adminRole->syncPermissions(Permission::all());
        
        // Asignar rol admin al usuario
        $user->syncRoles([$adminRole]);
        
        $this->info("Rol admin asignado a: {$user->email}");
        $this->info("Nuevos roles: " . implode(', ', $user->getRoleNames()->toArray()));
        
        // Verificar permisos específicos
        $this->info("Puede view roles: " . ($user->can('view roles') ? 'Sí' : 'No'));
        $this->info("Puede manage roles: " . ($user->can('manage roles') ? 'Sí' : 'No'));
        $this->info("Puede manage roles: " . ($user->can('create users') ? 'Sí' : 'No'));
        $this->info("Puede manage roles: " . ($user->can('edit users') ? 'Sí' : 'No'));
        $this->info("Puede manage roles: " . ($user->can('delete users') ? 'Sí' : 'No'));
        
        return 0;
    }
}