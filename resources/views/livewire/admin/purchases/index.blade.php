<?php

use App\Models\Purchase;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'statusFilter' => '', 'perPage' => 15]);

$purchases = computed(function () {
    return Purchase::query()
        ->with(['supplier', 'creator'])
        ->withCount('items')
        ->when($this->search, fn($q) => $q->where('purchase_number', 'like', "%{$this->search}%")
            ->orWhereHas('supplier', fn($q2) => $q2->where('name', 'like', "%{$this->search}%")))
        ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
        ->latest()
        ->paginate($this->perPage);
});

$delete = function (int $id) {
    Purchase::findOrFail($id)->delete();
    session()->flash('success', 'Purchase deleted.');
};

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Purchase Orders"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Purchases']]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.purchases.create') }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Purchase
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
                        placeholder="Search by PO# or supplier..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
                <select wire:model.live="statusFilter" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="received">Received</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <select wire:model.live="perPage" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="10">10 / page</option>
                    <option value="15">15 / page</option>
                    <option value="25">25 / page</option>
                </select>
            </div>
        </div>

        <x-admin.table :headers="['PO #','Supplier','Items','Total','Status','Created By','Date','Actions']">
            @forelse($this->purchases as $purchase)
                @php
                    $statusColors = ['pending'=>'yellow','approved'=>'blue','received'=>'purple','completed'=>'green','cancelled'=>'red'];
                @endphp
                <tr class="hover:bg-gray-50 transition" wire:key="purchase-{{ $purchase->id }}">
                    <td class="px-4 py-3 text-xs font-mono font-medium text-gray-900">{{ $purchase->purchase_number }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $purchase->supplier?->name ?? '—' }}</td>
                    <td class="px-4 py-3"><x-admin.badge :label="$purchase->items_count" color="blue"/></td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">${{ number_format($purchase->total_amount, 2) }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :label="ucfirst($purchase->status)" :color="$statusColors[$purchase->status] ?? 'gray'"/>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $purchase->creator?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $purchase->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.purchases.show', $purchase) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">View</a>
                            @if($purchase->status === 'pending')
                                <a href="{{ route('admin.purchases.edit', $purchase) }}" class="text-orange-600 hover:text-orange-800 text-xs font-medium">Edit</a>
                                <button wire:click="delete({{ $purchase->id }})" wire:confirm="Delete this purchase?" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400 text-sm">No purchases found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->purchases->links() }}</div>
    </div>
</div>



