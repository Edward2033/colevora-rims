<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsEmployee
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

        if (! $user->isEmployee()) {
            return redirect()->route('customer.dashboard');
        }

        // Enforce role-specific route access
        $roleName = $user->roles->first()?->name ?? '';
        $correctRoute = self::ROLE_ROUTES[$roleName] ?? null;

        if ($correctRoute) {
            $currentRoute = $request->route()?->getName();
            $allowedRoutes = [$correctRoute, 'settings.profile', 'settings.password', 'settings.appearance', 'logout'];

            if ($currentRoute && ! in_array($currentRoute, $allowedRoutes, true)) {
                return redirect()->route($correctRoute);
            }
        }

        return $next($request);
    }
}
