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
        // Limpiar datos existentes solo en entorno local
        if (app()->environment('local')) {
            $this->cleanupExistingData();
        }

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
            ['name' => 'view users', 'description' => 'Permite ver usuarios'],
            ['name' => 'create users', 'description' => 'Permite crear usuarios'],
            ['name' => 'edit users', 'description' => 'Permite editar usuarios'],
            ['name' => 'delete users', 'description' => 'Permite eliminar usuarios'],

            // Permisos de roles
            ['name' => 'view roles', 'description' => 'Permite ver roles'],
            ['name' => 'create roles', 'description' => 'Permite crear roles'],
            ['name' => 'edit roles', 'description' => 'Permite editar roles'],
            ['name' => 'delete roles', 'description' => 'Permite eliminar roles'],

            // Permisos de permisos
            ['name' => 'view permissions', 'description' => 'Permite ver permisos'],
            ['name' => 'create permissions', 'description' => 'Permite crear permisos'],
            ['name' => 'edit permissions', 'description' => 'Permite editar permisos'],
            ['name' => 'delete permissions', 'description' => 'Permite eliminar permisos'],

            // Permisos del sistema
            ['name' => 'manage permissions', 'description' => 'Permite gestionar permisos'],
            ['name' => 'view dashboard', 'description' => 'Permite ver el dashboard'],

            // PERMISOS PARA PRODUCTOS
            ['name' => 'view products', 'description' => 'Permite ver productos'],
            ['name' => 'create products', 'description' => 'Permite crear productos'],
            ['name' => 'edit products', 'description' => 'Permite editar productos'],
            ['name' => 'delete products', 'description' => 'Permite eliminar productos'],
            ['name' => 'manage products', 'description' => 'Permite gestionar productos'],

            // PERMISOS PARA ÓRDENES DE COMPRA
            ['name' => 'view purchase_orders', 'description' => 'Permite ver órdenes de compra'],
            ['name' => 'create purchase_orders', 'description' => 'Permite crear órdenes de compra'],
            ['name' => 'edit purchase_orders', 'description' => 'Permite editar órdenes de compra'],
            ['name' => 'delete purchase_orders', 'description' => 'Permite eliminar órdenes de compra'],
            ['name' => 'approve purchase_orders', 'description' => 'Permite aprobar órdenes de compra'],
            ['name' => 'receive purchase_orders', 'description' => 'Permite recibir órdenes de compra'],
            ['name' => 'manage purchase_orders', 'description' => 'Permite gestionar órdenes de compra'],

            // PERMISOS ESPECÍFICOS PARA ÓRDENES
            ['name' => 'change_order_status', 'description' => 'Permite cambiar el estado de las órdenes'],
            ['name' => 'assign_order_driver', 'description' => 'Permite asignar conductor a órdenes'],
            ['name' => 'view_order_history', 'description' => 'Permite ver el historial de órdenes'],
            ['name' => 'export_orders', 'description' => 'Permite exportar órdenes'],
            ['name' => 'view_order_reports', 'description' => 'Permite ver reportes de órdenes'],
            ['name' => 'manage_order_settings', 'description' => 'Permite gestionar configuraciones de órdenes'],
            ['name' => 'bulk_order_actions', 'description' => 'Permite realizar acciones masivas en órdenes'],
            ['name' => 'override_order_restrictions', 'description' => 'Permite sobreescribir restricciones de órdenes'],

            // PERMISOS PARA PROVEEDORES
            ['name' => 'view suppliers', 'description' => 'Permite ver proveedores'],
            ['name' => 'create suppliers', 'description' => 'Permite crear proveedores'],
            ['name' => 'edit suppliers', 'description' => 'Permite editar proveedores'],
            ['name' => 'delete suppliers', 'description' => 'Permite eliminar proveedores'],
            ['name' => 'manage suppliers', 'description' => 'Permite gestionar proveedores'],

            // PERMISOS ESPECÍFICOS PARA PROVEEDORES (usando guiones bajos)
            ['name' => 'toggle_supplier_status', 'description' => 'Permite activar/desactivar proveedores'],
            ['name' => 'view_supplier_details', 'description' => 'Permite ver detalles completos de proveedores'],
            ['name' => 'export_suppliers', 'description' => 'Permite exportar listas de proveedores'],
            ['name' => 'import_suppliers', 'description' => 'Permite importar proveedores'],
            ['name' => 'view_supplier_reports', 'description' => 'Permite ver reportes de proveedores'],
            ['name' => 'manage_supplier_categories', 'description' => 'Permite gestionar categorías de proveedores'],
            ['name' => 'view_supplier_purchase_orders', 'description' => 'Permite ver órdenes de compra por proveedor'],
            ['name' => 'manage_supplier_contacts', 'description' => 'Permite gestionar contactos de proveedores'],
            ['name' => 'view_supplier_performance', 'description' => 'Permite ver rendimiento de proveedores'],
            ['name' => 'manage_supplier_evaluations', 'description' => 'Permite gestionar evaluaciones de proveedores'],
            ['name' => 'bulk_supplier_actions', 'description' => 'Permite realizar acciones masivas en proveedores'],
            ['name' => 'override_supplier_restrictions', 'description' => 'Permite sobreescribir restricciones de proveedores'],

            // PERMISOS PARA REPORTES
            ['name' => 'view reports', 'description' => 'Permite ver reportes'],
            ['name' => 'generate reports', 'description' => 'Permite generar reportes'],
            ['name' => 'export reports', 'description' => 'Permite exportar reportes'],

            // PERMISOS PARA INVENTARIO
            ['name' => 'view inventory', 'description' => 'Permite ver inventario'],
            ['name' => 'manage inventory', 'description' => 'Permite gestionar inventario'],
            ['name' => 'adjust inventory', 'description' => 'Permite ajustar inventario'],

            // PERMISOS PARA CONFIGURACIÓN
            ['name' => 'view settings', 'description' => 'Permite ver configuración'],
            ['name' => 'manage settings', 'description' => 'Permite gestionar configuración']
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
            ['name' => 'admin', 'description' => 'Administrador del sistema con todos los permisos', 'is_system' => true],
            ['name' => 'user', 'description' => 'Usuario regular del sistema', 'is_system' => false],
            ['name' => 'manager', 'description' => 'Gerente con permisos de gestión', 'is_system' => false],
            ['name' => 'purchaser', 'description' => 'Comprador con permisos de órdenes de compra', 'is_system' => false],
            ['name' => 'viewer', 'description' => 'Usuario con permisos de solo lectura', 'is_system' => false],
            ['name' => 'order_operator', 'description' => 'Operador con permisos específicos para gestión de órdenes', 'is_system' => false],
            ['name' => 'supplier_manager', 'description' => 'Gestor especializado en proveedores', 'is_system' => false]
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
        $allPermissions = Permission::all()->pluck('name')->toArray();
        
        $this->command->info('Available permissions: ' . implode(', ', $allPermissions));

        // Rol Admin - Todos los permisos
        $adminRole = Role::where('name', 'admin')->first();
        $adminRole->givePermissionTo($allPermissions);

        // Asignar permisos a otros roles
        $this->assignPermissionsSafely('manager', $this->getManagerPermissions());
        $this->assignPermissionsSafely('purchaser', $this->getPurchaserPermissions());
        $this->assignPermissionsSafely('supplier_manager', $this->getSupplierManagerPermissions());
        $this->assignPermissionsSafely('order_operator', $this->getOrderOperatorPermissions());
        $this->assignPermissionsSafely('user', $this->getUserPermissions());
        $this->assignPermissionsSafely('viewer', $this->getViewerPermissions());

        $this->command->info('Permissions assigned to roles successfully.');
    }

    /**
     * Assign permissions safely to a role, checking if they exist.
     */
    private function assignPermissionsSafely(string $roleName, array $permissions): void
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->command->error("Role {$roleName} not found!");
            return;
        }

        $existingPermissions = [];
        foreach ($permissions as $permission) {
            if (Permission::where('name', $permission)->exists()) {
                $existingPermissions[] = $permission;
            } else {
                $this->command->warn("Permission {$permission} does not exist, skipping...");
            }
        }

        $role->givePermissionTo($existingPermissions);
        $this->command->info("Assigned " . count($existingPermissions) . " permissions to {$roleName} role");
    }

    private function getManagerPermissions(): array
    {
        return [
            'view dashboard',
            'view users',
            'view products',
            'view purchase_orders',
            'view suppliers',
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
            'generate reports',
            'export reports',
            'manage inventory',
            'adjust inventory',
            'manage settings'
        ];
    }

    private function getPurchaserPermissions(): array
    {
        return [
            'view dashboard',
            'view products',
            'view purchase_orders',
            'view suppliers',
            'view inventory',
            'create purchase_orders',
            'edit purchase_orders',
            'manage purchase_orders',
            'change_order_status',
            'view_order_history',
            'export_orders',
            'view_order_reports',
            'bulk_order_actions',
            'create suppliers',
            'edit suppliers',
            'toggle_supplier_status',
            'view_supplier_details',
            'export_suppliers',
            'view_supplier_purchase_orders',
            'view_supplier_performance',
            'bulk_supplier_actions',
            'create products',
            'edit products',
            'manage inventory'
        ];
    }

    private function getSupplierManagerPermissions(): array
    {
        return [
            'view dashboard',
            'view suppliers',
            'view products',
            'view purchase_orders',
            'view inventory',
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
            'view_order_history',
            'view_order_reports'
        ];
    }

    private function getOrderOperatorPermissions(): array
    {
        return [
            'view dashboard',
            'view purchase_orders',
            'view products',
            'view suppliers',
            'view inventory',
            'create purchase_orders',
            'edit purchase_orders',
            'change_order_status',
            'assign_order_driver',
            'view_order_history',
            'export_orders',
            'view_order_reports',
            'bulk_order_actions',
            'view_supplier_details',
            'view_supplier_purchase_orders'
        ];
    }

    private function getUserPermissions(): array
    {
        return [
            'view dashboard',
            'view products'
        ];
    }

    private function getViewerPermissions(): array
    {
        return [
            'view dashboard',
            'view products',
            'view purchase_orders',
            'view suppliers',
            'view reports',
            'view inventory',
            'view_order_history',
            'view_order_reports',
            'view_supplier_details',
            'view_supplier_purchase_orders',
            'view_supplier_performance'
        ];
    }

    private function createAdminUser(): void
    {
        $adminEmail = 'leoordev@gmail.com';
        $adminPassword = 'MguY!x0djuBh';

        $user = User::where('email', $adminEmail)->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Administrator',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
            ]);

            $this->command->info("Admin user created with email: {$adminEmail}");
            $this->command->info("Password: {$adminPassword}");
        } else {
            $this->command->info("Admin user already exists: {$adminEmail}");
        }

        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            $user->syncRoles([$adminRole]);
            $this->command->info("Admin role assigned to user: {$user->email}");
        } else {
            $this->command->error('Admin role not found!');
        }
    }

    private function cleanupExistingData(): void
    {
        \DB::table('model_has_permissions')->delete();
        \DB::table('model_has_roles')->delete();
        \DB::table('role_has_permissions')->delete();
        Permission::query()->delete();
        Role::query()->delete();
        
        $this->command->info('Existing permissions and roles cleaned up.');
    }
}