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

            // Permisos del sistema
            [
                'name' => 'manage permissions',
                'description' => 'Permite gestionar permisos'
            ],
            [
                'name' => 'view dashboard',
                'description' => 'Permite ver el dashboard'
            ],

            // ========== PERMISOS PARA PRODUCTOS ==========
            [
                'name' => 'view products',
                'description' => 'Permite ver productos'
            ],
            [
                'name' => 'create products',
                'description' => 'Permite crear productos'
            ],
            [
                'name' => 'edit products',
                'description' => 'Permite editar productos'
            ],
            [
                'name' => 'delete products',
                'description' => 'Permite eliminar productos'
            ],
            [
                'name' => 'manage products',
                'description' => 'Permite gestionar productos'
            ],

            // ========== PERMISOS PARA ÓRDENES DE COMPRA ==========
            [
                'name' => 'view purchase_orders',
                'description' => 'Permite ver órdenes de compra'
            ],
            [
                'name' => 'create purchase_orders',
                'description' => 'Permite crear órdenes de compra'
            ],
            [
                'name' => 'edit purchase_orders',
                'description' => 'Permite editar órdenes de compra'
            ],
            [
                'name' => 'delete purchase_orders',
                'description' => 'Permite eliminar órdenes de compra'
            ],
            [
                'name' => 'approve purchase_orders',
                'description' => 'Permite aprobar órdenes de compra'
            ],
            [
                'name' => 'receive purchase_orders',
                'description' => 'Permite recibir órdenes de compra'
            ],
            [
                'name' => 'manage purchase_orders',
                'description' => 'Permite gestionar órdenes de compra'
            ],

            // ========== PERMISOS ESPECÍFICOS PARA ÓRDENES ==========
            [
                'name' => 'change_order_status',
                'description' => 'Permite cambiar el estado de las órdenes'
            ],
            [
                'name' => 'assign_order_driver',
                'description' => 'Permite asignar conductor a órdenes'
            ],
            [
                'name' => 'view_order_history',
                'description' => 'Permite ver el historial de órdenes'
            ],
            [
                'name' => 'export_orders',
                'description' => 'Permite exportar órdenes'
            ],
            [
                'name' => 'view_order_reports',
                'description' => 'Permite ver reportes de órdenes'
            ],
            [
                'name' => 'manage_order_settings',
                'description' => 'Permite gestionar configuraciones de órdenes'
            ],
            [
                'name' => 'bulk_order_actions',
                'description' => 'Permite realizar acciones masivas en órdenes'
            ],
            [
                'name' => 'override_order_restrictions',
                'description' => 'Permite sobreescribir restricciones de órdenes'
            ],

            // ========== PERMISOS PARA PROVEEDORES ==========
            [
                'name' => 'view suppliers',
                'description' => 'Permite ver proveedores'
            ],
            [
                'name' => 'create suppliers',
                'description' => 'Permite crear proveedores'
            ],
            [
                'name' => 'edit suppliers',
                'description' => 'Permite editar proveedores'
            ],
            [
                'name' => 'delete suppliers',
                'description' => 'Permite eliminar proveedores'
            ],
            [
                'name' => 'manage suppliers',
                'description' => 'Permite gestionar proveedores'
            ],

            // ========== PERMISOS ESPECÍFICOS PARA PROVEEDORES ==========
            [
                'name' => 'toggle_supplier_status',
                'description' => 'Permite activar/desactivar proveedores'
            ],
            [
                'name' => 'view_supplier_details',
                'description' => 'Permite ver detalles completos de proveedores'
            ],
            [
                'name' => 'export_suppliers',
                'description' => 'Permite exportar listas de proveedores'
            ],
            [
                'name' => 'import_suppliers',
                'description' => 'Permite importar proveedores'
            ],
            [
                'name' => 'view_supplier_reports',
                'description' => 'Permite ver reportes de proveedores'
            ],
            [
                'name' => 'manage_supplier_categories',
                'description' => 'Permite gestionar categorías de proveedores'
            ],
            [
                'name' => 'view_supplier_purchase_orders',
                'description' => 'Permite ver órdenes de compra por proveedor'
            ],
            [
                'name' => 'manage_supplier_contacts',
                'description' => 'Permite gestionar contactos de proveedores'
            ],
            [
                'name' => 'view_supplier_performance',
                'description' => 'Permite ver rendimiento de proveedores'
            ],
            [
                'name' => 'manage_supplier_evaluations',
                'description' => 'Permite gestionar evaluaciones de proveedores'
            ],
            [
                'name' => 'bulk_supplier_actions',
                'description' => 'Permite realizar acciones masivas en proveedores'
            ],
            [
                'name' => 'override_supplier_restrictions',
                'description' => 'Permite sobreescribir restricciones de proveedores'
            ],

            // ========== PERMISOS PARA SERVICIOS Y VÍDEOS ==========
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
            [
                'name' => 'manage services',
                'description' => 'Permite gestionar servicios'
            ],

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
            [
                'name' => 'manage videos',
                'description' => 'Permite gestionar vídeos'
            ],

            // ========== PERMISOS PARA EVENTOS ==========
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
                'name' => 'manage events',
                'description' => 'Permite gestionar eventos'
            ],

            // ========== PERMISOS PARA REPORTES ==========
            [
                'name' => 'view reports',
                'description' => 'Permite ver reportes'
            ],
            [
                'name' => 'generate reports',
                'description' => 'Permite generar reportes'
            ],
            [
                'name' => 'export reports',
                'description' => 'Permite exportar reportes'
            ],

            // ========== PERMISOS PARA INVENTARIO ==========
            [
                'name' => 'view inventory',
                'description' => 'Permite ver inventario'
            ],
            [
                'name' => 'manage inventory',
                'description' => 'Permite gestionar inventario'
            ],
            [
                'name' => 'adjust inventory',
                'description' => 'Permite ajustar inventario'
            ],

            // ========== PERMISOS PARA CONFIGURACIÓN ==========
            [
                'name' => 'view settings',
                'description' => 'Permite ver configuración'
            ],
            [
                'name' => 'manage settings',
                'description' => 'Permite gestionar configuración'
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
            ],
            [
                'name' => 'manager',
                'description' => 'Gerente con permisos de gestión',
                'is_system' => false
            ],
            [
                'name' => 'purchaser',
                'description' => 'Comprador con permisos de órdenes de compra',
                'is_system' => false
            ],
            [
                'name' => 'viewer',
                'description' => 'Usuario con permisos de solo lectura',
                'is_system' => false
            ],
            [
                'name' => 'order_operator',
                'description' => 'Operador con permisos específicos para gestión de órdenes',
                'is_system' => false
            ],
            // ========== ROL ESPECÍFICO PARA PROVEEDORES ==========
            [
                'name' => 'supplier_manager',
                'description' => 'Gestor especializado en proveedores',
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

        // Rol Manager - Permisos de gestión
        $managerRole = Role::where('name', 'manager')->first();
        $managerRole->givePermissionTo([
            'view dashboard',
            'view users',
            'view products',
            'view purchase_orders',
            'view suppliers',
            'view services',
            'view videos',
            'view events',
            'view reports',
            'view inventory',
            'view settings',
            
            'create products',
            'edit products',
            'manage products',
            
            'create purchase_orders',
            'edit purchase_orders',
            'approve purchase_orders',
            'receive purchase_orders',
            'manage purchase_orders',
            
            // Permisos de órdenes para manager
            'change_order_status',
            'assign_order_driver',
            'view_order_history',
            'export_orders',
            'view_order_reports',
            'manage_order_settings',
            'bulk_order_actions',
            'override_order_restrictions',
            
            'create suppliers',
            'edit suppliers',
            'manage suppliers',
            
            // Permisos de proveedores para manager
            'toggle_supplier_status',
            'view_supplier_details',
            'export_suppliers',
            'import_suppliers',
            'view_supplier_reports',
            'manage_supplier_categories',
            'view_supplier_purchase_orders',
            'manage_supplier_contacts',
            'view_supplier_performance',
            'bulk_supplier_actions',
            
            'create services',
            'edit services',
            'manage services',
            
            'create videos',
            'edit videos',
            'manage videos',
            
            'create events',
            'edit events',
            'manage events',
            
            'generate reports',
            'export reports',
            
            'manage inventory',
            'adjust inventory',
            
            'manage settings'
        ]);

        // Rol Purchaser - Permisos de compras
        $purchaserRole = Role::where('name', 'purchaser')->first();
        $purchaserRole->givePermissionTo([
            'view dashboard',
            'view products',
            'view purchase_orders',
            'view suppliers',
            'view inventory',
            
            'create purchase_orders',
            'edit purchase_orders',
            'manage purchase_orders',
            
            // Permisos específicos de órdenes para purchaser
            'change_order_status',
            'view_order_history',
            'export_orders',
            'view_order_reports',
            'bulk_order_actions',
            
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            
            // Permisos de proveedores para purchaser
            'toggle_supplier_status',
            'view_supplier_details',
            'export_suppliers',
            'view_supplier_purchase_orders',
            'view_supplier_performance',
            'bulk_supplier_actions',
            
            'view products',
            'create products',
            'edit products',
            
            'view inventory',
            'manage inventory'
        ]);

        // ========== ROL SUPPLIER MANAGER ==========
        $supplierManagerRole = Role::where('name', 'supplier_manager')->first();
        $supplierManagerRole->givePermissionTo([
            'view dashboard',
            'view suppliers',
            'view products',
            'view purchase_orders',
            'view inventory',
            
            // Permisos completos de proveedores
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            'manage suppliers',
            'toggle_supplier_status',
            'view_supplier_details',
            'export_suppliers',
            'import_suppliers',
            'view_supplier_reports',
            'manage_supplier_categories',
            'view_supplier_purchase_orders',
            'manage_supplier_contacts',
            'view_supplier_performance',
            'manage_supplier_evaluations',
            'bulk_supplier_actions',
            'override_supplier_restrictions',
            
            // Permisos relacionados
            'view products',
            'view purchase_orders',
            'view_order_history',
            'view_order_reports',
            'view inventory'
        ]);

        // Rol Order Operator - Permisos de operaciones de órdenes
        $orderOperatorRole = Role::where('name', 'order_operator')->first();
        $orderOperatorRole->givePermissionTo([
            'view dashboard',
            'view purchase_orders',
            'view products',
            'view suppliers',
            'view inventory',
            
            'create purchase_orders',
            'edit purchase_orders',
            
            // Permisos específicos de operaciones de órdenes
            'change_order_status',
            'assign_order_driver',
            'view_order_history',
            'export_orders',
            'view_order_reports',
            'bulk_order_actions',
            
            'view products',
            'view suppliers',
            'view_supplier_details',
            'view_supplier_purchase_orders',
            'view inventory'
        ]);

        // Rol User - Permisos básicos
        $userRole = Role::where('name', 'user')->first();
        $userRole->givePermissionTo([
            'view dashboard',
            'view products',
            'view services',
            'view videos',
            'view events'
        ]);

        // Rol Viewer - Solo lectura
        $viewerRole = Role::where('name', 'viewer')->first();
        $viewerRole->givePermissionTo([
            'view dashboard',
            'view products',
            'view purchase_orders',
            'view suppliers',
            'view services',
            'view videos',
            'view events',
            'view reports',
            'view inventory',
            'view_order_history',
            'view_order_reports',
            'view_supplier_details',
            'view_supplier_purchase_orders',
            'view_supplier_performance'
        ]);

        $this->command->info('Permissions assigned to roles successfully.');
    }

    /**
     * Create admin user if doesn't exist and assign admin role.
     */
    private function createAdminUser(): void
    {
        $adminEmail = 'leoordev@gmail.com';
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