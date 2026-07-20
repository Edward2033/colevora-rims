<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCustomer
{
    private const ROLE_ROUTES = [
        'Chef'              => 'employee.chef.dashboard',
        'Waiter'            => 'employee.waiter.dashboard',
        'Cashier'           => 'employee.cashier.dashboard',
        'Receptionist'      => 'employee.receptionist.dashboard',
        'Inventory Officer' => 'employee.inventory-officer.dashboard',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isEmployee()) {
            $roleName = $user->roles->first()?->name ?? '';
            $route = self::ROLE_ROUTES[$roleName] ?? 'employee.chef.dashboard';
            return redirect()->route($route);
        }

        return $next($request);
    }
}
