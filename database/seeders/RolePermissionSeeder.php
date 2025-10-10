<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createPermissions();
        $this->createRoles();
        $this->assignPermissionsToRoles();
        $this->createAdminUser();
        
        $this->command->info('Roles and permissions seeded successfully.');
    }

    /**
     * Create the system permissions with descriptions.
     */
    private function createPermissions(): void
    {
        $permissions = [
            // Permisos de eventos
            [
                'name' => 'view events',
                'description' => 'Permite ver eventos'
            ],
            [
                'name' => 'create events',
                'description' => 'Permite crear eventos'
            ],
            [
                'name' => 'edit events',
                'description' => 'Permite editar eventos'
            ],
            [
                'name' => 'delete events',
                'description' => 'Permite eliminar eventos'
            ],
            [
                'name' => 'import events',
                'description' => 'Permite importar eventos'
            ],

            // Permisos de usuarios
            [
                'name' => 'view users',
                'description' => 'Permite ver usuarios'
            ],
            [
                'name' => 'create users',
                'description' => 'Permite crear usuarios'
            ],
            [
                'name' => 'edit users',
                'description' => 'Permite editar usuarios'
            ],
            [
                'name' => 'delete users',
                'description' => 'Permite eliminar usuarios'
            ],

            // Permisos de roles
            [
                'name' => 'view roles',
                'description' => 'Permite ver roles'
            ],
            [
                'name' => 'create roles',
                'description' => 'Permite crear roles'
            ],
            [
                'name' => 'edit roles',
                'description' => 'Permite editar roles'
            ],
            [
                'name' => 'delete roles',
                'description' => 'Permite eliminar roles'
            ],

            // Permisos de permisos
            [
                'name' => 'view permissions',
                'description' => 'Permite ver permisos'
            ],
            [
                'name' => 'create permissions',
                'description' => 'Permite crear permisos'
            ],
            [
                'name' => 'edit permissions',
                'description' => 'Permite editar permisos'
            ],
            [
                'name' => 'delete permissions',
                'description' => 'Permite eliminar permisos'
            ],

            // Permisos de servicios (NUEVOS)
            [
                'name' => 'view services',
                'description' => 'Permite ver servicios'
            ],
            [
                'name' => 'create services',
                'description' => 'Permite crear servicios'
            ],
            [
                'name' => 'edit services',
                'description' => 'Permite editar servicios'
            ],
            [
                'name' => 'delete services',
                'description' => 'Permite eliminar servicios'
            ],

            // Permisos de vídeos (NUEVOS)
            [
                'name' => 'view videos',
                'description' => 'Permite ver vídeos'
            ],
            [
                'name' => 'create videos',
                'description' => 'Permite crear vídeos'
            ],
            [
                'name' => 'edit videos',
                'description' => 'Permite editar vídeos'
            ],
            [
                'name' => 'delete videos',
                'description' => 'Permite eliminar vídeos'
            ],

            // Permisos del sistema
            [
                'name' => 'manage permissions',
                'description' => 'Permite gestionar permisos'
            ],
            [
                'name' => 'view dashboard',
                'description' => 'Permite ver el dashboard'
            ]
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
        }

        $this->command->info('Permissions created successfully.');
    }

    /**
     * Create the system roles with descriptions.
     */
    private function createRoles(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrador del sistema con todos los permisos',
                'is_system' => true
            ],
            [
                'name' => 'user',
                'description' => 'Usuario regular del sistema',
                'is_system' => false
            ]
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        $this->command->info('Roles created successfully.');
    }

    /**
     * Assign permissions to roles.
     */
    private function assignPermissionsToRoles(): void
    {
        // Rol Admin - Todos los permisos
        $adminRole = Role::where('name', 'admin')->first();
        $adminRole->givePermissionTo(Permission::all());

        // Rol User - Permisos básicos de eventos
        $userRole = Role::where('name', 'user')->first();
        $userRole->givePermissionTo([
            'view events',
            'create events',
            'edit events',
            'delete events',
            'view dashboard',
            'view services', // Puede ver servicios
            'view videos'    // Puede ver vídeos
        ]);

        $this->command->info('Permissions assigned to roles successfully.');
    }

    /**
     * Create admin user if doesn't exist and assign admin role.
     */
    private function createAdminUser(): void
    {
        $adminEmail = 'admin@blupage.com';
        $adminPassword = 'MguY!x0djuBh';

        // Buscar usuario admin o crear uno nuevo
        $user = User::where('email', $adminEmail)->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Administrator',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(), // Verificar email automáticamente
            ]);

            $this->command->info("Admin user created with email: {$adminEmail}");
            $this->command->info("Password: {$adminPassword}");
        } else {
            $this->command->info("Admin user already exists: {$adminEmail}");
        }

        // Asignar rol de admin
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            // Remover roles existentes y asignar solo admin
            $user->syncRoles([$adminRole]);
            $this->command->info("Admin role assigned to user: {$user->email}");
        } else {
            $this->command->error('Admin role not found!');
        }
    }

    /**
     * Alternative method: Assign admin role to first user or create one.
     */
    private function assignAdminRole(): void
    {
        $user = User::first();
        
        if ($user) {
            $adminRole = Role::where('name', 'admin')->first();
            $user->assignRole($adminRole);
            
            $this->command->info("Admin role assigned to existing user: {$user->email}");
        } else {
            $this->createAdminUser();
        }
    }

    /**
     * Clean up existing permissions and roles (optional).
     * Use with caution - only for fresh installations.
     */
    private function cleanupExistingData(): void
    {
        if (app()->environment('local')) {
            Permission::query()->delete();
            Role::query()->delete();
            $this->command->info('Existing permissions and roles cleaned up.');
        }
    }
}