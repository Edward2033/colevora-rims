<?php

use function Livewire\Volt\{layout};

layout('components.layouts.admin');

?>

@php
    $id = $id ?? request()->route('id');
    $employee = \App\Models\Employee::with(['user', 'creator'])->findOrFail($id);
    $statusColors = ['active'=>'green','inactive'=>'gray','on_leave'=>'yellow','terminated'=>'red'];
    $approvalColors = ['approved'=>'green','pending'=>'yellow','rejected'=>'red'];
@endphp

<div>
    <div class="p-6 max-w-3xl">
        <x-admin.page-header
            title="Employee Details"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Employees','url'=>route('admin.employees.index')],['label'=>$employee->user?->name ?? 'Employee']]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.employees.edit', $employee) }}" variant="secondary">Edit</x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-2xl mx-auto mb-4">
                    {{ strtoupper(substr($employee->user?->name ?? 'E', 0, 1)) }}
                </div>
                <h2 class="text-lg font-bold text-gray-900">{{ $employee->user?->name ?? 'Unknown' }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $employee->user?->email ?? '' }}</p>
                <p class="text-xs font-mono text-gray-400 mt-1">{{ $employee->employee_number }}</p>
                <div class="mt-3 flex justify-center gap-2 flex-wrap">
                    <x-admin.badge :label="ucfirst(str_replace('_',' ',$employee->employment_status))" :color="$statusColors[$employee->employment_status] ?? 'gray'"/>
                    <x-admin.badge :label="ucfirst($employee->approval_status)" :color="$approvalColors[$employee->approval_status] ?? 'gray'"/>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Employment Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs text-gray-500">Job Title</dt>
                        <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->job_title ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Department</dt>
                        <dd class="text-sm font-medium text-gray-900 mt-0.5 capitalize">{{ $employee->department ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Hire Date</dt>
                        <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->hire_date?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Added By</dt>
                        <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->creator?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Record Created</dt>
                        <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Last Updated</dt>
                        <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $employee->updated_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
