<?php

use App\Models\RestaurantTable;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['tableId' => null, 'table_number' => '', 'capacity' => 4, 'location' => '', 'status' => 'available']);

mount(function (int $id) {
    $table = RestaurantTable::findOrFail($id);
    $this->tableId = $table->id;
    $this->table_number = $table->table_number;
    $this->capacity = $table->capacity;
    $this->location = $table->location ?? '';
    $this->status = $table->status;
});

$save = function () {
    $this->validate([
        'table_number' => "required|string|max:20|unique:restaurant_tables,table_number,{$this->tableId}",
        'capacity'     => 'required|integer|min:1|max:50',
        'location'     => 'nullable|string|max:100',
        'status'       => 'required|in:available,occupied,reserved,maintenance',
    ]);

    RestaurantTable::findOrFail($this->tableId)->update([
        'table_number' => $this->table_number,
        'capacity'     => $this->capacity,
        'location'     => $this->location,
        'status'       => $this->status,
    ]);

    session()->flash('success', 'Table updated.');
    $this->redirect(route('admin.tables.index'), navigate: false);
};

?>

<div>
    <div class="p-6 max-w-xl">
        <x-admin.page-header
            title="Edit Table"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Tables','url'=>route('admin.tables.index')],['label'=>'Edit']]"
        />

        <div class="glass-card rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <form wire:submit="save" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Table Number <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="table_number"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('table_number') border-red-400 @enderror">
                        @error('table_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Capacity <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="capacity" min="1" max="50"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('capacity') border-red-400 @enderror">
                        @error('capacity')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Location</label>
                        <input type="text" wire:model="location"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Status</label>
                        <select wire:model="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="available">Available</option>
                            <option value="occupied">Occupied</option>
                            <option value="reserved">Reserved</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.btn type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Update Table</span>
                        <span wire:loading>Updating...</span>
                    </x-admin.btn>
                    <x-admin.btn href="{{ route('admin.tables.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>



