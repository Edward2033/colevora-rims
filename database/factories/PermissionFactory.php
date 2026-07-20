<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = ['view', 'create', 'edit', 'delete', 'manage'];
        $resources = ['users', 'roles', 'employees', 'menu', 'orders', 'inventory', 'reports'];

        return [
            'name' => fake()->unique()->randomElement($actions).'_'.fake()->randomElement($resources),
            'description' => fake()->sentence(),
        ];
    }
}
