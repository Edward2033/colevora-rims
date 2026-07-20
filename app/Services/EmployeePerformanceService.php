<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EmployeePerformanceService
{
    /**
     * Get all employees performance summary for reports page.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getEmployeePerformance(): array
    {
        // Primary: use Employee records if they exist
        $employees = Employee::with('user')->get();

        if ($employees->isNotEmpty()) {
            return $employees->map(function ($employee) {
                $orders = OrderAssignment::where('employee_id', $employee->id)->count();
                return [
                    'name'   => $employee->user?->name ?? 'Unknown',
                    'role'   => $employee->job_title ?? 'Staff',
                    'orders' => $orders,
                    'score'  => min(100, $orders * 5),
                ];
            })->toArray();
        }

        // Fallback: derive from role-based users
        $roleUsers = User::whereHas('roles', fn ($q) => $q->whereIn('name', [
            'Chef', 'Waiter', 'Cashier', 'Receptionist', 'Inventory Officer',
        ]))->with('roles')->get();

        return $roleUsers->map(function ($user) {
            $role = $user->roles->first()?->name ?? 'Staff';
            // Count orders handled based on role
            $orders = match ($role) {
                'Chef'   => Order::whereIn('status', ['preparing','ready','served','completed'])->count(),
                'Waiter' => Order::whereIn('status', ['served','completed'])->count(),
                'Cashier'=> Payment::completed()->count(),
                default  => 0,
            };
            return [
                'name'   => $user->name,
                'role'   => $role,
                'orders' => $orders,
                'score'  => min(100, $orders > 0 ? 60 : 0),
            ];
        })->toArray();
    }

    /**
     * Get orders grouped by employee for reports page.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getOrdersByEmployee(): array
    {
        return Employee::with('user')->get()->map(function ($employee) {
            return [
                'name'   => $employee->user?->name ?? 'Unknown',
                'orders' => OrderAssignment::where('employee_id', $employee->id)->count(),
            ];
        })->toArray();
    }

    /**
     * Get chef performance (orders prepared).
     *
     * @return array<string, mixed>
     */
    public function getChefPerformance(int $employeeId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $employee = Employee::with('user')->findOrFail($employeeId);

        $query = OrderAssignment::where('employee_id', $employeeId)
            ->whereHas('order', function ($q) {
                $q->whereIn('status', ['preparing', 'ready', 'served', 'completed']);
            });

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $assignments = $query->with(['order.items'])->get();
        $orders = $assignments->pluck('order')->unique('id');

        return [
            'employee' => $employee,
            'total_orders_prepared' => $orders->count(),
            'completed_orders' => $orders->where('status', 'completed')->count(),
            'total_items_prepared' => $orders->sum(fn ($order) => $order->items->sum('quantity')),
            'orders' => $orders,
        ];
    }

    /**
     * Get waiter performance (orders delivered).
     *
     * @return array<string, mixed>
     */
    public function getWaiterPerformance(int $employeeId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $employee = Employee::with('user')->findOrFail($employeeId);

        $query = Order::where('assigned_waiter_id', $employeeId);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $orders = $query->with(['items', 'payment'])->get();

        return [
            'employee' => $employee,
            'total_orders_handled' => $orders->count(),
            'delivered_orders' => $orders->whereIn('status', ['served', 'completed'])->count(),
            'total_sales' => $orders->sum('total_amount'),
            'average_order_value' => $orders->count() > 0 ? $orders->avg('total_amount') : 0,
            'orders' => $orders,
        ];
    }

    /**
     * Get cashier performance (payments processed).
     *
     * @return array<string, mixed>
     */
    public function getCashierPerformance(int $employeeId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $employee = Employee::with('user')->findOrFail($employeeId);
        $userId = $employee->user_id;

        $query = Payment::where('paid_by', $userId)
            ->completed();

        if ($startDate) {
            $query->where('paid_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('paid_at', '<=', $endDate);
        }

        $payments = $query->with(['order'])->get();

        return [
            'employee' => $employee,
            'total_payments_processed' => $payments->count(),
            'total_amount_collected' => $payments->sum('amount'),
            'average_transaction_value' => $payments->count() > 0 ? $payments->avg('amount') : 0,
            'payments_by_method' => $payments->groupBy('payment_method')->map(fn ($group) => [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ]),
            'payments' => $payments,
        ];
    }

    /**
     * Get overall employee performance summary.
     *
     * @return array<string, mixed>
     */
    public function getEmployeePerformanceSummary(int $employeeId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $employee = Employee::with('user')->findOrFail($employeeId);
        $jobTitle = strtolower($employee->job_title);

        $performance = [
            'employee' => $employee,
            'job_title' => $employee->job_title,
        ];

        if (str_contains($jobTitle, 'chef') || str_contains($jobTitle, 'cook')) {
            $performance = array_merge($performance, $this->getChefPerformance($employeeId, $startDate, $endDate));
        } elseif (str_contains($jobTitle, 'waiter') || str_contains($jobTitle, 'server')) {
            $performance = array_merge($performance, $this->getWaiterPerformance($employeeId, $startDate, $endDate));
        } elseif (str_contains($jobTitle, 'cashier')) {
            $performance = array_merge($performance, $this->getCashierPerformance($employeeId, $startDate, $endDate));
        }

        return $performance;
    }

    /**
     * Get top performing employees by role.
     */
    public function getTopPerformingEmployees(string $jobTitle, int $limit = 10): Collection
    {
        $employees = Employee::where('job_title', 'like', "%{$jobTitle}%")
            ->with('user')
            ->get();

        $performance = $employees->map(function ($employee) use ($jobTitle) {
            $data = ['employee' => $employee];

            if (str_contains(strtolower($jobTitle), 'chef')) {
                $result = $this->getChefPerformance($employee->id);
                $data['score'] = $result['total_orders_prepared'];
            } elseif (str_contains(strtolower($jobTitle), 'waiter')) {
                $result = $this->getWaiterPerformance($employee->id);
                $data['score'] = $result['delivered_orders'];
            } elseif (str_contains(strtolower($jobTitle), 'cashier')) {
                $result = $this->getCashierPerformance($employee->id);
                $data['score'] = $result['total_payments_processed'];
            } else {
                $data['score'] = 0;
            }

            return $data;
        });

        return $performance->sortByDesc('score')->take($limit)->values();
    }

    /**
     * Get employee performance comparison.
     *
     * @return array<string, mixed>
     */
    public function getEmployeePerformanceComparison(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $employees = Employee::with('user')->get();

        $chefs = [];
        $waiters = [];
        $cashiers = [];

        foreach ($employees as $employee) {
            $jobTitle = strtolower($employee->job_title);

            if (str_contains($jobTitle, 'chef') || str_contains($jobTitle, 'cook')) {
                $chefs[] = $this->getChefPerformance($employee->id, $startDate, $endDate);
            } elseif (str_contains($jobTitle, 'waiter') || str_contains($jobTitle, 'server')) {
                $waiters[] = $this->getWaiterPerformance($employee->id, $startDate, $endDate);
            } elseif (str_contains($jobTitle, 'cashier')) {
                $cashiers[] = $this->getCashierPerformance($employee->id, $startDate, $endDate);
            }
        }

        return [
            'chefs' => collect($chefs)->sortByDesc('total_orders_prepared')->values(),
            'waiters' => collect($waiters)->sortByDesc('delivered_orders')->values(),
            'cashiers' => collect($cashiers)->sortByDesc('total_payments_processed')->values(),
        ];
    }
}
