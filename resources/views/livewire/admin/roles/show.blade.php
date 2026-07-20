<?php
use function Livewire\Volt\{layout};
layout('components.layouts.admin');
?>

@php
    $id = $id ?? request()->route('id');
    $role = \App\Models\Role::with(['permissions', 'users'])->findOrFail($id);
@endphp

<div>
    <div class="p-6 max-w-3xl">
        <x-admin.page-header
            title="Role Details"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Roles','url'=>route('admin.roles.index')],['label'=>$role->name]]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.roles.edit', $role) }}" variant="secondary">Edit Role</x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Role Information</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-xs text-gray-500">Name</dt>
                        <dd class="text-sm font-bold text-gray-900 mt-0.5">{{ $role->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Users Assigned</dt>
                        <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $role->users->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Permissions</dt>
                        <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $role->permissions->count() }}</dd>
                    </div>
                    @if($role->description)
                    <div class="sm:col-span-3">
                        <dt class="text-xs text-gray-500">Description</dt>
                        <dd class="text-sm text-gray-700 mt-0.5">{{ $role->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            @if($role->permissions->count())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Permissions ({{ $role->permissions->count() }})</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($role->permissions as $perm)
                        <span class="px-2.5 py-1 bg-purple-50 text-purple-700 text-xs font-medium rounded-full border border-purple-200">{{ $perm->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($role->users->count())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Users with this Role ({{ $role->users->count() }})</h3>
                <div class="space-y-2">
                    @foreach($role->users->take(10) as $u)
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $u->name }}</p>
                                <p class="text-xs text-gray-400">{{ $u->email }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
