<?php

namespace Database\Seeders;

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
        // Borre y agregue los módulos y permisos de su sistema
        $users = ['view-users', 'create-users', 'update-users', 'delete-users'];
        $products = ['view-products', 'create-products', 'update-products', 'delete-products'];

        // Todos los permisos
        $arrayOfPermissionNames = array_merge($users, $products);

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        Permission::insert($permissions->toArray());

        // Rol de super admin y permisos asignados
        $role = Role::create(['name' => 'super-admin']);
        $admin_permissions = array_merge(
            $users,
            $products,
        );
        $role->givePermissionTo($admin_permissions);

        // Otro rol y permisos asignados
        $role = Role::create(['name' => 'role_name']);
        $seller_permissions = array_merge(
            $products
        );
        $role->givePermissionTo($seller_permissions);
    }
}
