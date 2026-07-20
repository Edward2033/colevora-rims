<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class DashboardRedirectController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isEmployee()) {
            $map = [
                'Chef'              => 'employee.chef.dashboard',
                'Waiter'            => 'employee.waiter.dashboard',
                'Cashier'           => 'employee.cashier.dashboard',
                'Receptionist'      => 'employee.receptionist.dashboard',
                'Inventory Officer' => 'employee.inventory-officer.dashboard',
            ];
            $roleName = $user->roles->first()?->name ?? '';
            return redirect()->route($map[$roleName] ?? 'employee.chef.dashboard');
        }

        return redirect()->route('customer.dashboard');
    }
}
