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
            <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="glass-card rounded-xl border border-white/[0.08] p-4 mb-5">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search by table number or location..."
                        class="w-full px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
                <select wire:model.live="statusFilter" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="" class="bg-gray-900">All Status</option>
                    <option value="available" class="bg-gray-900">Available</option>
                    <option value="occupied" class="bg-gray-900">Occupied</option>
                    <option value="reserved" class="bg-gray-900">Reserved</option>
                    <option value="maintenance" class="bg-gray-900">Maintenance</option>
                </select>
            </div>
        </div>

        <x-admin.table :headers="['Table #','Capacity','Location','Status','Orders','Actions']">
            @forelse($this->tables as $table)
                @php
                    $statusColors = ['available'=>'green','occupied'=>'red','reserved'=>'yellow','maintenance'=>'gray'];
                @endphp
                <tr class="hover:bg-amber-500/5 transition" wire:key="table-{{ $table->id }}">
                    <td class="px-4 py-3 text-sm font-bold text-white">{{ $table->table_number }}</td>
                    <td class="px-4 py-3 text-sm text-gray-300">{{ $table->capacity }} seats</td>
                    <td class="px-4 py-3 text-sm text-gray-300">{{ $table->location ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <button wire:click="toggleStatus({{ $table->id }})" class="focus:outline-none">
                            <x-admin.badge :label="ucfirst($table->status)" :color="$statusColors[$table->status] ?? 'gray'"/>
                        </button>
                    </td>
                    <td class="px-4 py-3"><x-admin.badge :label="$table->orders_count" color="blue"/></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.tables.show', $table) }}" class="text-blue-400 hover:text-blue-300 text-xs font-medium">View</a>
                            <a href="{{ route('admin.tables.edit', $table) }}" class="text-amber-400 hover:text-amber-300 text-xs font-medium">Edit</a>
                            <button wire:click="delete({{ $table->id }})" wire:confirm="Delete this table?" class="text-red-400 hover:text-red-300 text-xs font-medium">Delete</button>
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



