<?php

use App\Models\RestaurantTable;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'statusFilter' => '', 'perPage' => 15]);

$tables = computed(function () {
    return RestaurantTable::query()
        ->withCount('orders')
        ->when($this->search, fn($q) => $q->where('table_number', 'like', "%{$this->search}%")
            ->orWhere('location', 'like', "%{$this->search}%"))
        ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
        ->orderBy('table_number')
        ->paginate($this->perPage);
});

$delete = function (int $id) {
    RestaurantTable::findOrFail($id)->delete();
    session()->flash('success', 'Table deleted.');
};

$toggleStatus = function (int $id) {
    $table = RestaurantTable::findOrFail($id);
    $table->update(['status' => $table->status === 'available' ? 'occupied' : 'available']);
};

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Restaurant Tables"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Tables']]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.tables.create') }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Table
                </x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-5">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search by table number or location..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
                <select wire:model.live="statusFilter" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="reserved">Reserved</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
        </div>

        <x-admin.table :headers="['Table #','Capacity','Location','Status','Orders','Actions']">
            @forelse($this->tables as $table)
                @php
                    $statusColors = ['available'=>'green','occupied'=>'red','reserved'=>'yellow','maintenance'=>'gray'];
                @endphp
                <tr class="hover:bg-gray-50 transition" wire:key="table-{{ $table->id }}">
                    <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ $table->table_number }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $table->capacity }} seats</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $table->location ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <button wire:click="toggleStatus({{ $table->id }})" class="focus:outline-none">
                            <x-admin.badge :label="ucfirst($table->status)" :color="$statusColors[$table->status] ?? 'gray'"/>
                        </button>
                    </td>
                    <td class="px-4 py-3"><x-admin.badge :label="$table->orders_count" color="blue"/></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.tables.show', $table) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">View</a>
                            <a href="{{ route('admin.tables.edit', $table) }}" class="text-orange-600 hover:text-orange-800 text-xs font-medium">Edit</a>
                            <button wire:click="delete({{ $table->id }})" wire:confirm="Delete this table?" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">No tables found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->tables->links() }}</div>
    </div>
</div>



