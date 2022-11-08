<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos definidos por cada módulo
        $sellers = ['view-sellers', 'create-sellers', 'update-sellers', 'delete-sellers', 'set-online-status'];
        $stores = ['view-stores', 'create-stores', 'set-up-stores', 'update-stores', 'delete-stores'];
        $products = ['view-products', 'create-products', 'update-products', 'delete-products'];
        $customers = ['view-customers', 'create-customers', 'update-customers', 'delete-customers'];
        $orders = ['view-orders', 'create-orders', 'update-orders', 'delete-orders'];
        $chats = ['view-chats', 'answer-chats'];
        $notifications = ['manage-notifications'];

        // Todos los permisos
        $arrayOfPermissionNames = array_merge($sellers, $stores, $products, $customers, $orders, $chats, $notifications);

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        Permission::insert($permissions->toArray());

        // Rol de super admin y permisos asignados
        $role = Role::create(['name' => 'super-admin']);
        $admin_permissions = array_merge(
            array_diff($sellers, ['set-online-status']),
            $stores,
            $products,
            ['view-customers'],
            ["view-orders"],
            ["view-chats"],
            $notifications
        );
        $role->givePermissionTo($admin_permissions);

        // Rol de seller y permisos asignados
        $role = Role::create(['name' => 'seller']);
        $seller_permissions = array_merge(
            ['set-online-status'],
            $products,// Todos los permisos de productos deben ser para administrador, vendedores comunes sólo visualizan
            $customers,
            $orders,
            $chats,
            $notifications
        );
        $role->givePermissionTo($seller_permissions);
    }
}
