<?php

use function Livewire\Volt\{layout};

layout('components.layouts.admin');

?>

@php
    $id = $id ?? request()->route('id');
    $user = \App\Models\User::with(['roles', 'employee', 'orders'])->findOrFail($id);
    $typeColors = ['admin'=>'purple','employee'=>'blue','customer'=>'green'];
    $statusColors = ['active'=>'green','inactive'=>'gray','suspended'=>'red'];
@endphp

<div>
    <div class="p-6 max-w-4xl">
        <x-admin.page-header
            title="User Details"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Users','url'=>route('admin.users.index')],['label'=>$user->name]]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.users.edit', $user) }}" variant="secondary">Edit User</x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="w-20 h-20 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-2xl mx-auto mb-4">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>
                <div class="mt-3 flex justify-center gap-2">
                    <x-admin.badge :label="ucfirst($user->user_type)" :color="$typeColors[$user->user_type] ?? 'gray'"/>
                    <x-admin.badge :label="ucfirst($user->account_status ?? 'active')" :color="$statusColors[$user->account_status ?? 'active'] ?? 'gray'"/>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Account Information</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs text-gray-500">Phone</dt>
                            <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->phone ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Email Verified</dt>
                            <dd class="text-sm font-medium mt-0.5">
                                @if($user->email_verified_at)
                                    <span class="text-green-600">✓ {{ $user->email_verified_at->format('M d, Y') }}</span>
                                @else
                                    <span class="text-red-500">Not verified</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Joined</dt>
                            <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Total Orders</dt>
                            <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->orders->count() }}</dd>
                        </div>
                    </dl>
                </div>

                @if($user->roles->count())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Assigned Roles</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->roles as $role)
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($user->employee)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Employee Record</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs text-gray-500">Employee #</dt>
                            <dd class="text-sm font-medium text-gray-900 mt-0.5 font-mono">{{ $user->employee->employee_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Job Title</dt>
                            <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->employee->job_title ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Department</dt>
                            <dd class="text-sm font-medium text-gray-900 mt-0.5 capitalize">{{ $user->employee->department ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Status</dt>
                            <dd class="text-sm font-medium text-gray-900 mt-0.5 capitalize">{{ str_replace('_', ' ', $user->employee->employment_status) }}</dd>
                        </div>
                    </dl>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
