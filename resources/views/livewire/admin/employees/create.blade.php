<?php

use App\Models\Employee;
use App\Models\User;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state([
    'user_id' => '', 'employee_number' => '', 'job_title' => '',
    'department' => '', 'hire_date' => '', 'employment_status' => 'active',
    'approval_status' => 'approved',
]);

$availableUsers = computed(fn() =>
    User::where('user_type', 'employee')
        ->whereDoesntHave('employee')
        ->orderBy('name')
        ->get()
);

$save = function () {
    $this->validate([
        'user_id'           => 'required|exists:users,id',
        'employee_number'   => 'required|string|max:50|unique:employees,employee_number',
        'job_title'         => 'nullable|string|max:100',
        'department'        => 'nullable|string|max:100',
        'hire_date'         => 'nullable|date',
        'employment_status' => 'required|in:active,inactive,on_leave,terminated',
        'approval_status'   => 'required|in:pending,approved,rejected',
    ]);

    Employee::create([
        'user_id'           => $this->user_id,
        'employee_number'   => $this->employee_number,
        'job_title'         => $this->job_title,
        'department'        => $this->department,
        'hire_date'         => $this->hire_date ?: null,
        'employment_status' => $this->employment_status,
        'approval_status'   => $this->approval_status,
        'created_by'        => auth()->id(),
    ]);

    session()->flash('success', 'Employee created successfully.');
    $this->redirect(route('admin.employees.index'), navigate: false);
};

?>

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Add Employee"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Employees','url'=>route('admin.employees.index')],['label'=>'Create']]"
        />

        <div class="glass-card rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <form wire:submit="save" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">User Account <span class="text-red-500">*</span></label>
                        <select wire:model="user_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('user_id') border-red-400 @enderror">
                            <option value="">Select user...</option>
                            @foreach($this->availableUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-400">Only users with type "employee" who don't have an employee record are shown.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Employee Number <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="employee_number"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('employee_number') border-red-400 @enderror"
                            placeholder="EMP-001">
                        @error('employee_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Job Title</label>
                        <input type="text" wire:model="job_title"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                            placeholder="e.g. Head Chef">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Department</label>
                        <select wire:model="department" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="">Select department...</option>
                            <option value="kitchen">Kitchen</option>
                            <option value="service">Service</option>
                            <option value="cashier">Cashier</option>
                            <option value="management">Management</option>
                            <option value="inventory">Inventory</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Hire Date</label>
                        <input type="date" wire:model="hire_date"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Employment Status</label>
                        <select wire:model="employment_status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="on_leave">On Leave</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Approval Status</label>
                        <select wire:model="approval_status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.btn type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Create Employee</span>
                        <span wire:loading>Creating...</span>
                    </x-admin.btn>
                    <x-admin.btn href="{{ route('admin.employees.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>



