<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->isAdmin()) {
            $roles = $user->roles->pluck('name');
            $map = [
                'Chef'              => 'employee.chef.dashboard',
                'Waiter'            => 'employee.waiter.dashboard',
                'Cashier'           => 'employee.cashier.dashboard',
                'Receptionist'      => 'employee.receptionist.dashboard',
                'Inventory Officer' => 'employee.inventory-officer.dashboard',
            ];
            foreach ($map as $role => $route) {
                if ($roles->contains($role)) {
                    return redirect()->route($route);
                }
            }
            return redirect()->route('customer.dashboard');
        }

        return $next($request);
    }
}
