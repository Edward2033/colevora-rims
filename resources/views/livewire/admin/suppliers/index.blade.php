<?php

use App\Models\Supplier;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'statusFilter' => '', 'perPage' => 15]);

$suppliers = computed(function () {
    return Supplier::query()
        ->withCount(['inventoryItems', 'purchases'])
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
            ->orWhere('company_name', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%"))
        ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
        ->latest()
        ->paginate($this->perPage);
});

$delete = function (int $id) {
    Supplier::findOrFail($id)->delete();
    session()->flash('success', 'Supplier deleted.');
};

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Suppliers"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Suppliers']]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.suppliers.create') }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Supplier
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
                        placeholder="Search by name, company or email..."
                        class="w-full px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
                <select wire:model.live="statusFilter" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="" class="bg-gray-900">All Status</option>
                    <option value="active" class="bg-gray-900">Active</option>
                    <option value="inactive" class="bg-gray-900">Inactive</option>
                </select>
                <select wire:model.live="perPage" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="10" class="bg-gray-900">10 / page</option>
                    <option value="15" class="bg-gray-900">15 / page</option>
                    <option value="25" class="bg-gray-900">25 / page</option>
                </select>
            </div>
        </div>

        <x-admin.table :headers="['#','Supplier','Company','Contact','Items','Purchases','Status','Actions']">
            @forelse($this->suppliers as $supplier)
                <tr class="hover:bg-amber-500/5 transition" wire:key="supplier-{{ $supplier->id }}">
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $supplier->id }}</td>
                    <td class="px-4 py-3">
                        <p class="text-sm font-medium text-white">{{ $supplier->name }}</p>
                        @if($supplier->address)
                            <p class="text-xs text-gray-400 truncate max-w-xs">{{ $supplier->address }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-300">{{ $supplier->company_name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <p class="text-xs text-gray-300">{{ $supplier->phone ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $supplier->email ?? '—' }}</p>
                    </td>
                    <td class="px-4 py-3"><x-admin.badge :label="$supplier->inventory_items_count" color="blue"/></td>
                    <td class="px-4 py-3"><x-admin.badge :label="$supplier->purchases_count" color="purple"/></td>
                    <td class="px-4 py-3">
                        <x-admin.badge :label="ucfirst($supplier->status)" :color="$supplier->status === 'active' ? 'green' : 'gray'"/>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.suppliers.show', $supplier) }}" class="text-blue-400 hover:text-blue-300 text-xs font-medium">View</a>
                            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="text-amber-400 hover:text-amber-300 text-xs font-medium">Edit</a>
                            <button wire:click="delete({{ $supplier->id }})" wire:confirm="Delete this supplier?" class="text-red-400 hover:text-red-300 text-xs font-medium">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400 text-sm">No suppliers found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->suppliers->links() }}</div>
    </div>
</div>



