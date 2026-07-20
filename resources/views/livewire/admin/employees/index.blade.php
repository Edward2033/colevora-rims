<?php

use App\Models\Employee;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'deptFilter' => '', 'statusFilter' => '', 'perPage' => 15]);

$employees = computed(function () {
    return Employee::query()
        ->with('user')
        ->when($this->search, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->orWhere('employee_number', 'like', "%{$this->search}%"))
        ->when($this->deptFilter, fn($q) => $q->where('department', $this->deptFilter))
        ->when($this->statusFilter, fn($q) => $q->where('employment_status', $this->statusFilter))
        ->latest()
        ->paginate($this->perPage);
});

$delete = function (int $id) {
    Employee::findOrFail($id)->delete();
    session()->flash('success', 'Employee record deleted.');
};

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Employees"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Employees']]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.employees.create') }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Employee
                </x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <!-- Filters -->
        <div class="glass-card rounded-xl border border-white/[0.08] p-4 mb-5">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search by name, email or employee #..."
                        class="w-full px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
                <select wire:model.live="deptFilter" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="" class="bg-gray-900">All Departments</option>
                    <option value="kitchen" class="bg-gray-900">Kitchen</option>
                    <option value="service" class="bg-gray-900">Service</option>
                    <option value="cashier" class="bg-gray-900">Cashier</option>
                    <option value="management" class="bg-gray-900">Management</option>
                    <option value="inventory" class="bg-gray-900">Inventory</option>
                </select>
                <select wire:model.live="statusFilter" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="" class="bg-gray-900">All Status</option>
                    <option value="active" class="bg-gray-900">Active</option>
                    <option value="inactive" class="bg-gray-900">Inactive</option>
                    <option value="on_leave" class="bg-gray-900">On Leave</option>
                    <option value="terminated" class="bg-gray-900">Terminated</option>
                </select>
                <select wire:model.live="perPage" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="10" class="bg-gray-900">10 / page</option>
                    <option value="15" class="bg-gray-900">15 / page</option>
                    <option value="25" class="bg-gray-900">25 / page</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <x-admin.table :headers="['#','Employee','Job Title','Department','Status','Hire Date','Actions']">
            @forelse($this->employees as $emp)
                @php
                    $statusColors = ['active'=>'green','inactive'=>'gray','on_leave'=>'yellow','terminated'=>'red'];
                @endphp
                <tr class="hover:bg-amber-500/5 transition" wire:key="emp-{{ $emp->id }}">
                    <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $emp->employee_number }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400 font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($emp->user?->name ?? 'E', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">{{ $emp->user?->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-400">{{ $emp->user?->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-300">{{ $emp->job_title ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-300 capitalize">{{ $emp->department ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :label="ucfirst(str_replace('_',' ',$emp->employment_status))" :color="$statusColors[$emp->employment_status] ?? 'gray'"/>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $emp->hire_date?->format('M d, Y') ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.employees.show', $emp) }}" class="text-blue-400 hover:text-blue-300 text-xs font-medium">View</a>
                            <a href="{{ route('admin.employees.edit', $emp) }}" class="text-amber-400 hover:text-amber-300 text-xs font-medium">Edit</a>
                            <button wire:click="delete({{ $emp->id }})" wire:confirm="Delete this employee record?" class="text-red-400 hover:text-red-300 text-xs font-medium">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">No employees found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->employees->links() }}</div>
    </div>
</div>



