<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'description' => 'Full system access with all administrative privileges',
            ],
            [
                'name' => 'Manager',
                'description' => 'Restaurant management access including staff, inventory, and reports',
            ],
            [
                'name' => 'Chef',
                'description' => 'Kitchen management and order preparation access',
            ],
            [
                'name' => 'Waiter',
                'description' => 'Customer service and order taking access',
            ],
            [
                'name' => 'Cashier',
                'description' => 'Payment processing and billing access',
            ],
            [
                'name' => 'Receptionist',
                'description' => 'Front desk and reservation management access',
            ],
            [
                'name' => 'Inventory Officer',
                'description' => 'Inventory management and stock control access',
            ],
            [
                'name' => 'Customer',
                'description' => 'Customer portal access for orders and reservations',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
