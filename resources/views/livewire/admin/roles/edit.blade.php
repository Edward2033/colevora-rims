<?php

use App\Models\Role;
use App\Models\Permission;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['roleId' => null, 'name' => '', 'description' => '', 'selectedPermissions' => []]);

$permissions = computed(fn() => Permission::orderBy('name')->get());

mount(function (int $id) {
    $role = Role::with('permissions')->findOrFail($id);
    $this->roleId = $role->id;
    $this->name = $role->name;
    $this->description = $role->description ?? '';
    $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
});

$save = function () {
    $this->validate([
        'name'        => "required|string|max:100|unique:roles,name,{$this->roleId}",
        'description' => 'nullable|string|max:255',
    ]);

    $role = Role::findOrFail($this->roleId);
    $role->update(['name' => $this->name, 'description' => $this->description]);
    $role->permissions()->sync($this->selectedPermissions);

    session()->flash('success', 'Role updated successfully.');
    $this->redirect(route('admin.roles.index'), navigate: false);
};

?>

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Edit Role"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Roles','url'=>route('admin.roles.index')],['label'=>'Edit']]"
        />

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form wire:submit="save" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Role Name <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="name"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('name') border-red-400 @enderror">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Description</label>
                    <textarea wire:model="description" rows="2"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3">
                        @foreach($this->permissions as $perm)
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-gray-900">
                                <input type="checkbox" wire:model="selectedPermissions" value="{{ $perm->id }}"
                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                {{ $perm->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.btn type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Update Role</span>
                        <span wire:loading>Updating...</span>
                    </x-admin.btn>
                    <x-admin.btn href="{{ route('admin.roles.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>



