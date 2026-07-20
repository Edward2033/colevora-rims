<?php

use App\Models\Role;
use App\Models\User;

test('guests are redirected when accessing admin dashboard', function () {
    $response = $this->get('/admin/dashboard');
    $response->assertRedirect();
});

test('admin users can visit the admin dashboard', function () {
    $user = User::factory()->create(['user_type' => 'admin']);
    $role = Role::firstOrCreate(['name' => 'Administrator'], ['description' => 'Administrator']);
    $user->roles()->syncWithoutDetaching([$role->id]);

    $this->actingAs($user);
    $response = $this->get('/admin/dashboard');
    $response->assertStatus(200);
});
