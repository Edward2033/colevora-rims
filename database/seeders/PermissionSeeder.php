<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'view_users', 'description' => 'View user accounts'],
            ['name' => 'create_users', 'description' => 'Create new user accounts'],
            ['name' => 'edit_users', 'description' => 'Edit existing user accounts'],
            ['name' => 'delete_users', 'description' => 'Delete user accounts'],

            // Role Management
            ['name' => 'view_roles', 'description' => 'View roles'],
            ['name' => 'create_roles', 'description' => 'Create new roles'],
            ['name' => 'edit_roles', 'description' => 'Edit existing roles'],
            ['name' => 'delete_roles', 'description' => 'Delete roles'],

            // Employee Management
            ['name' => 'view_employees', 'description' => 'View employee records'],
            ['name' => 'create_employees', 'description' => 'Create new employee records'],
            ['name' => 'edit_employees', 'description' => 'Edit existing employee records'],
            ['name' => 'delete_employees', 'description' => 'Delete employee records'],

            // Menu Management
            ['name' => 'view_menu', 'description' => 'View menu items'],
            ['name' => 'create_menu', 'description' => 'Create new menu items'],
            ['name' => 'edit_menu', 'description' => 'Edit existing menu items'],
            ['name' => 'delete_menu', 'description' => 'Delete menu items'],

            // Order Management
            ['name' => 'view_orders', 'description' => 'View orders'],
            ['name' => 'create_orders', 'description' => 'Create new orders'],
            ['name' => 'edit_orders', 'description' => 'Edit existing orders'],
            ['name' => 'delete_orders', 'description' => 'Delete orders'],
            ['name' => 'process_orders', 'description' => 'Process and fulfill orders'],

            // Inventory Management
            ['name' => 'view_inventory', 'description' => 'View inventory items'],
            ['name' => 'create_inventory', 'description' => 'Add new inventory items'],
            ['name' => 'edit_inventory', 'description' => 'Edit existing inventory items'],
            ['name' => 'delete_inventory', 'description' => 'Delete inventory items'],

            // Reports
            ['name' => 'view_reports', 'description' => 'View system reports'],
            ['name' => 'generate_reports', 'description' => 'Generate custom reports'],

            // Audit Logs
            ['name' => 'view_audit_logs', 'description' => 'View audit logs'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    /**
     * Assign permissions to roles.
     */
    private function assignPermissionsToRoles(): void
    {
        $administrator = Role::where('name', 'Administrator')->first();
        if ($administrator) {
            $administrator->permissions()->sync(Permission::all());
        }

        $manager = Role::where('name', 'Manager')->first();
        if ($manager) {
            $manager->permissions()->sync(
                Permission::whereIn('name', [
                    'view_users', 'view_employees', 'create_employees', 'edit_employees',
                    'view_menu', 'create_menu', 'edit_menu', 'delete_menu',
                    'view_orders', 'create_orders', 'edit_orders', 'process_orders',
                    'view_inventory', 'create_inventory', 'edit_inventory',
                    'view_reports', 'generate_reports',
                ])->pluck('id')
            );
        }

        $chef = Role::where('name', 'Chef')->first();
        if ($chef) {
            $chef->permissions()->sync(
                Permission::whereIn('name', [
                    'view_menu', 'view_orders', 'process_orders', 'view_inventory',
                ])->pluck('id')
            );
        }

        $waiter = Role::where('name', 'Waiter')->first();
        if ($waiter) {
            $waiter->permissions()->sync(
                Permission::whereIn('name', [
                    'view_menu', 'view_orders', 'create_orders', 'edit_orders',
                ])->pluck('id')
            );
        }

        $cashier = Role::where('name', 'Cashier')->first();
        if ($cashier) {
            $cashier->permissions()->sync(
                Permission::whereIn('name', [
                    'view_orders', 'edit_orders', 'process_orders',
                ])->pluck('id')
            );
        }

        $receptionist = Role::where('name', 'Receptionist')->first();
        if ($receptionist) {
            $receptionist->permissions()->sync(
                Permission::whereIn('name', [
                    'view_orders', 'create_orders',
                ])->pluck('id')
            );
        }

        $inventoryOfficer = Role::where('name', 'Inventory Officer')->first();
        if ($inventoryOfficer) {
            $inventoryOfficer->permissions()->sync(
                Permission::whereIn('name', [
                    'view_inventory', 'create_inventory', 'edit_inventory', 'delete_inventory',
                ])->pluck('id')
            );
        }

        $customer = Role::where('name', 'Customer')->first();
        if ($customer) {
            $customer->permissions()->sync(
                Permission::whereIn('name', [
                    'view_menu', 'view_orders', 'create_orders',
                ])->pluck('id')
            );
        }
    }
}
