<?php

use App\Models\InventoryItem;
use App\Models\InventoryCategory;
use App\Models\Supplier;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state([
    'name' => '', 'category_id' => '', 'supplier_id' => '',
    'unit' => '', 'quantity' => 0, 'minimum_quantity' => 0,
    'cost_price' => 0, 'status' => 'active',
]);

$categories = computed(fn() => InventoryCategory::orderBy('name')->get());
$suppliers = computed(fn() => Supplier::active()->orderBy('name')->get());

$save = function () {
    $this->validate([
        'name'             => 'required|string|max:150',
        'category_id'      => 'nullable|exists:inventory_categories,id',
        'supplier_id'      => 'nullable|exists:suppliers,id',
        'unit'             => 'required|string|max:50',
        'quantity'         => 'required|numeric|min:0',
        'minimum_quantity' => 'required|numeric|min:0',
        'cost_price'       => 'required|numeric|min:0',
        'status'           => 'required|in:active,inactive',
    ]);

    InventoryItem::create([
        'name'             => $this->name,
        'category_id'      => $this->category_id ?: null,
        'supplier_id'      => $this->supplier_id ?: null,
        'unit'             => $this->unit,
        'quantity'         => $this->quantity,
        'minimum_quantity' => $this->minimum_quantity,
        'cost_price'       => $this->cost_price,
        'status'           => $this->status,
    ]);

    session()->flash('success', 'Inventory item created successfully.');
    $this->redirect(route('admin.inventory.items.index'), navigate: false);
};

?>

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Add Inventory Item"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Inventory','url'=>route('admin.inventory.items.index')],['label'=>'Create']]"
        />

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form wire:submit="save" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Item Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('name') border-red-400 @enderror"
                            placeholder="e.g. Tomatoes">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Category</label>
                        <select wire:model="category_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="">Select category...</option>
                            @foreach($this->categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Supplier</label>
                        <select wire:model="supplier_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="">Select supplier...</option>
                            @foreach($this->suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Unit <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="unit"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('unit') border-red-400 @enderror"
                            placeholder="kg, liters, pieces...">
                        @error('unit')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Cost Price ($) <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="cost_price" step="0.01" min="0"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('cost_price') border-red-400 @enderror">
                        @error('cost_price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Current Quantity <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="quantity" step="0.01" min="0"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('quantity') border-red-400 @enderror">
                        @error('quantity')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Minimum Quantity <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="minimum_quantity" step="0.01" min="0"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('minimum_quantity') border-red-400 @enderror">
                        @error('minimum_quantity')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
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
                        <span wire:loading.remove>Create Item</span>
                        <span wire:loading>Creating...</span>
                    </x-admin.btn>
                    <x-admin.btn href="{{ route('admin.inventory.items.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>



