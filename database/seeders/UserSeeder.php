<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@colevora.com',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'role' => 'Administrator',
            ],
            [
                'name' => 'Restaurant Manager',
                'email' => 'manager@colevora.com',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'role' => 'Manager',
            ],
            [
                'name' => 'Head Chef',
                'email' => 'chef@colevora.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'role' => 'Chef',
            ],
            [
                'name' => 'Senior Waiter',
                'email' => 'waiter@colevora.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'role' => 'Waiter',
            ],
            [
                'name' => 'Main Cashier',
                'email' => 'cashier@colevora.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'role' => 'Cashier',
            ],
            [
                'name' => 'Inventory Manager',
                'email' => 'inventory@colevora.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'role' => 'Inventory Officer',
            ],
            [
                'name' => 'Test Customer',
                'email' => 'customer@colevora.com',
                'password' => Hash::make('password'),
                'user_type' => 'customer',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'role' => 'Customer',
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);

            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Assign role
            $role = Role::where('name', $roleName)->first();
            if ($role && ! $user->roles()->where('role_id', $role->id)->exists()) {
                $user->roles()->attach($role->id);
            }
        }
    }
}
