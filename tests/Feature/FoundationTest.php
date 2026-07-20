<?php

use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

test('users table has extended fields', function () {
    $user = User::factory()->create([
        'phone' => '1234567890',
        'profile_photo' => 'path/to/photo.jpg',
        'account_status' => 'active',
        'user_type' => 'employee',
        'otp_code' => '123456',
        'otp_expires_at' => now()->addMinutes(10),
    ]);

    expect($user->phone)->toBe('1234567890')
        ->and($user->profile_photo)->toBe('path/to/photo.jpg')
        ->and($user->account_status)->toBe('active')
        ->and($user->user_type)->toBe('employee')
        ->and($user->otp_code)->toBe('123456')
        ->and($user->otp_expires_at)->not->toBeNull();
});

test('user can have multiple roles', function () {
    $user = User::factory()->create();
    $role1 = Role::factory()->create(['name' => 'Manager']);
    $role2 = Role::factory()->create(['name' => 'Waiter']);

    $user->roles()->attach([$role1->id, $role2->id]);

    expect($user->roles)->toHaveCount(2)
        ->and($user->hasRole('Manager'))->toBeTrue()
        ->and($user->hasRole('Waiter'))->toBeTrue()
        ->and($user->hasRole('Chef'))->toBeFalse();
});

test('role can have multiple permissions', function () {
    $role = Role::factory()->create(['name' => 'Manager']);
    $permission1 = Permission::factory()->create(['name' => 'view_users']);
    $permission2 = Permission::factory()->create(['name' => 'create_users']);

    $role->permissions()->attach([$permission1->id, $permission2->id]);

    expect($role->permissions)->toHaveCount(2)
        ->and($role->hasPermission('view_users'))->toBeTrue()
        ->and($role->hasPermission('create_users'))->toBeTrue()
        ->and($role->hasPermission('delete_users'))->toBeFalse();
});

test('user can check permissions through roles', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create(['name' => 'Manager']);
    $permission = Permission::factory()->create(['name' => 'view_users']);

    $role->permissions()->attach($permission);
    $user->roles()->attach($role);

    expect($user->hasPermission('view_users'))->toBeTrue()
        ->and($user->hasPermission('delete_users'))->toBeFalse();
});

test('employee belongs to user', function () {
    $user = User::factory()->create();
    $employee = Employee::factory()->create([
        'user_id' => $user->id,
        'employee_number' => 'EMP1234',
    ]);

    expect($employee->user->id)->toBe($user->id)
        ->and($user->employee->id)->toBe($employee->id);
});

test('audit log belongs to user', function () {
    $user = User::factory()->create();
    $auditLog = AuditLog::factory()->create([
        'user_id' => $user->id,
        'action' => 'login',
    ]);

    expect($auditLog->user->id)->toBe($user->id)
        ->and($user->auditLogs)->toHaveCount(1)
        ->and($user->auditLogs->first()->action)->toBe('login');
});

test('roles are seeded correctly', function () {
    $this->seed([RoleSeeder::class]);

    $roles = Role::all();

    expect($roles)->toHaveCount(8)
        ->and(Role::where('name', 'Administrator')->exists())->toBeTrue()
        ->and(Role::where('name', 'Manager')->exists())->toBeTrue()
        ->and(Role::where('name', 'Chef')->exists())->toBeTrue()
        ->and(Role::where('name', 'Waiter')->exists())->toBeTrue()
        ->and(Role::where('name', 'Cashier')->exists())->toBeTrue()
        ->and(Role::where('name', 'Receptionist')->exists())->toBeTrue()
        ->and(Role::where('name', 'Inventory Officer')->exists())->toBeTrue()
        ->and(Role::where('name', 'Customer')->exists())->toBeTrue();
});

test('permissions are seeded correctly', function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $permissions = Permission::all();

    expect($permissions->count())->toBeGreaterThan(0)
        ->and(Permission::where('name', 'view_users')->exists())->toBeTrue()
        ->and(Permission::where('name', 'create_users')->exists())->toBeTrue()
        ->and(Permission::where('name', 'view_roles')->exists())->toBeTrue()
        ->and(Permission::where('name', 'view_employees')->exists())->toBeTrue();
});

test('administrator role has all permissions', function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $adminRole = Role::where('name', 'Administrator')->first();
    $totalPermissions = Permission::count();

    expect($adminRole->permissions)->toHaveCount($totalPermissions);
});

test('user soft deletes work', function () {
    $user = User::factory()->create();
    $userId = $user->id;

    $user->delete();

    expect(User::find($userId))->toBeNull()
        ->and(User::withTrashed()->find($userId))->not->toBeNull();
});
