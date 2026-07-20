<?php
// Role-based dashboard redirect
$user = auth()->user();
if ($user->isAdmin()) {
    return redirect()->route('admin.dashboard');
} elseif ($user->isEmployee()) {
    $roleName = $user->roles->first()?->name ?? '';
    $map = [
        'Chef'              => 'employee.chef.dashboard',
        'Waiter'            => 'employee.waiter.dashboard',
        'Cashier'           => 'employee.cashier.dashboard',
        'Receptionist'      => 'employee.waiter.dashboard',
        'Inventory Officer' => 'employee.inventory-officer.dashboard',
    ];
    return redirect()->route($map[$roleName] ?? 'employee.chef.dashboard');
} else {
    return redirect()->route('customer.dashboard');
}
