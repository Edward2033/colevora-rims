<?php

use App\Models\Employee;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state([
    'empId' => null, 'job_title' => '', 'department' => '',
    'hire_date' => '', 'employment_status' => 'active', 'approval_status' => 'approved',
]);

mount(function (int $id) {
    $emp = Employee::findOrFail($id);
    $this->empId = $emp->id;
    $this->job_title = $emp->job_title ?? '';
    $this->department = $emp->department ?? '';
    $this->hire_date = $emp->hire_date?->format('Y-m-d') ?? '';
    $this->employment_status = $emp->employment_status;
    $this->approval_status = $emp->approval_status;
});

$save = function () {
    $this->validate([
        'job_title'         => 'nullable|string|max:100',
        'department'        => 'nullable|string|max:100',
        'hire_date'         => 'nullable|date',
        'employment_status' => 'required|in:active,inactive,on_leave,terminated',
        'approval_status'   => 'required|in:pending,approved,rejected',
    ]);

    Employee::findOrFail($this->empId)->update([
        'job_title'         => $this->job_title,
        'department'        => $this->department,
        'hire_date'         => $this->hire_date ?: null,
        'employment_status' => $this->employment_status,
        'approval_status'   => $this->approval_status,
    ]);

    session()->flash('success', 'Employee updated successfully.');
    $this->redirect(route('admin.employees.index'), navigate: false);
};

?>

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Edit Employee"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Employees','url'=>route('admin.employees.index')],['label'=>'Edit']]"
        />

        <div class="glass-card rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <form wire:submit="save" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Job Title</label>
                        <input type="text" wire:model="job_title"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
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
                        <span wire:loading.remove>Update Employee</span>
                        <span wire:loading>Updating...</span>
                    </x-admin.btn>
                    <x-admin.btn href="{{ route('admin.employees.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>



