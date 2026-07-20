<?php

use App\Models\Supplier;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['name' => '', 'company_name' => '', 'phone' => '', 'email' => '', 'address' => '', 'status' => 'active']);

$save = function () {
    $this->validate([
        'name'         => 'required|string|max:150',
        'company_name' => 'nullable|string|max:150',
        'phone'        => 'nullable|string|max:20',
        'email'        => 'nullable|email|max:150',
        'address'      => 'nullable|string|max:500',
        'status'       => 'required|in:active,inactive',
    ]);

    Supplier::create([
        'name'         => $this->name,
        'company_name' => $this->company_name,
        'phone'        => $this->phone,
        'email'        => $this->email,
        'address'      => $this->address,
        'status'       => $this->status,
        'created_by'   => auth()->id(),
    ]);

    session()->flash('success', 'Supplier created successfully.');
    $this->redirect(route('admin.suppliers.index'), navigate: false);
};

?>

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Add Supplier"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Suppliers','url'=>route('admin.suppliers.index')],['label'=>'Create']]"
        />

        <div class="glass-card rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <form wire:submit="save" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Contact Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('name') border-red-400 @enderror"
                            placeholder="John Smith">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Company Name</label>
                        <input type="text" wire:model="company_name"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                            placeholder="ABC Supplies Ltd.">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Phone</label>
                        <input type="text" wire:model="phone"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                            placeholder="+1 234 567 8900">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Email</label>
                        <input type="email" wire:model="email"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('email') border-red-400 @enderror"
                            placeholder="supplier@example.com">
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Address</label>
                        <textarea wire:model="address" rows="2"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                            placeholder="Full address..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Status</label>
                        <select wire:model="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.btn type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Create Supplier</span>
                        <span wire:loading>Creating...</span>
                    </x-admin.btn>
                    <x-admin.btn href="{{ route('admin.suppliers.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>



