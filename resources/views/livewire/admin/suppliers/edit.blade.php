<?php
use function Livewire\Volt\{layout};
layout('components.layouts.admin');
?>

@php
    $id = $id ?? request()->route('id');
    $supplier = \App\Models\Supplier::findOrFail($id);
@endphp

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Edit Supplier"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Suppliers','url'=>route('admin.suppliers.index')],['label'=>'Edit']]"
        />

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="glass-card rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Contact Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $supplier->name) }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('name') border-red-400 @enderror">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $supplier->company_name) }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $supplier->email) }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('email') border-red-400 @enderror">
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Address</label>
                        <textarea name="address" rows="2"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('address', $supplier->address) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="active" {{ old('status', $supplier->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $supplier->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.btn type="submit">Update Supplier</x-admin.btn>
                    <x-admin.btn href="{{ route('admin.suppliers.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>
