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
            <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="glass-card rounded-xl border border-white/[0.08] p-4 mb-5">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Search roles..."
                class="w-full sm:w-80 px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
        </div>

        <x-admin.table :headers="['#','Role Name','Description','Users','Permissions','Actions']">
            @forelse($this->roles as $role)
                <tr class="hover:bg-amber-500/5 transition" wire:key="role-{{ $role->id }}">
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $role->id }}</td>
                    <td class="px-4 py-3 font-semibold text-white text-sm">{{ $role->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-400">{{ Str::limit($role->description ?? '—', 60) }}</td>
                    <td class="px-4 py-3"><x-admin.badge :label="$role->users_count" color="blue"/></td>
                    <td class="px-4 py-3"><x-admin.badge :label="$role->permissions_count" color="purple"/></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.roles.show', $role) }}" class="text-blue-400 hover:text-blue-300 text-xs font-medium">View</a>
                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-amber-400 hover:text-amber-300 text-xs font-medium">Edit</a>
                            <button wire:click="delete({{ $role->id }})" wire:confirm="Delete this role?" class="text-red-400 hover:text-red-300 text-xs font-medium">Delete</button>
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



