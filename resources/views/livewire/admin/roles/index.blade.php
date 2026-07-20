<?php

use App\Models\Role;
use App\Models\Permission;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'perPage' => 15]);

$roles = computed(function () {
    return Role::query()
        ->withCount(['users', 'permissions'])
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
        ->latest()
        ->paginate($this->perPage);
});

$delete = function (int $id) {
    $role = Role::findOrFail($id);
    $role->permissions()->detach();
    $role->users()->detach();
    $role->delete();
    session()->flash('success', 'Role deleted.');
};

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Roles & Permissions"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Roles']]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.roles.create') }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Role
                </x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-5">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Search roles..."
                class="w-full sm:w-80 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
        </div>

        <x-admin.table :headers="['#','Role Name','Description','Users','Permissions','Actions']">
            @forelse($this->roles as $role)
                <tr class="hover:bg-gray-50 transition" wire:key="role-{{ $role->id }}">
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $role->id }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-900 text-sm">{{ $role->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ Str::limit($role->description ?? '—', 60) }}</td>
                    <td class="px-4 py-3"><x-admin.badge :label="$role->users_count" color="blue"/></td>
                    <td class="px-4 py-3"><x-admin.badge :label="$role->permissions_count" color="purple"/></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.roles.show', $role) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">View</a>
                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-orange-600 hover:text-orange-800 text-xs font-medium">Edit</a>
                            <button wire:click="delete({{ $role->id }})" wire:confirm="Delete this role?" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">No roles found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->roles->links() }}</div>
    </div>
</div>



