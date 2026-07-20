<?php

use App\Models\InventoryItem;
use App\Models\InventoryCategory;
use App\Models\Supplier;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'categoryFilter' => '', 'statusFilter' => '', 'lowStockOnly' => false, 'perPage' => 15]);

$items = computed(function () {
    return InventoryItem::query()
        ->with(['category', 'supplier'])
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
        ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
        ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
        ->when($this->lowStockOnly, fn($q) => $q->whereRaw('quantity <= minimum_quantity'))
        ->latest()
        ->paginate($this->perPage);
});

$categories = computed(fn() => InventoryCategory::orderBy('name')->get());

$delete = function (int $id) {
    InventoryItem::findOrFail($id)->delete();
    session()->flash('success', 'Inventory item deleted.');
};

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Inventory Items"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Inventory Items']]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.inventory.items.create') }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="glass-card rounded-xl border border-white/[0.08] p-4 mb-5">
            <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
                <div class="flex-1 min-w-48">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search items..."
                        class="w-full px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
                <select wire:model.live="categoryFilter" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="" class="bg-gray-900">All Categories</option>
                    @foreach($this->categories as $cat)
                        <option value="{{ $cat->id }}" class="bg-gray-900">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="statusFilter" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="" class="bg-gray-900">All Status</option>
                    <option value="active" class="bg-gray-900">Active</option>
                    <option value="inactive" class="bg-gray-900">Inactive</option>
                </select>
                <label class="flex items-center gap-2 text-sm text-gray-300 cursor-pointer px-3 py-2 border border-white/10 bg-white/5 rounded-lg hover:bg-white/10">
                    <input type="checkbox" wire:model.live="lowStockOnly" class="rounded border-gray-600 text-amber-500 focus:ring-amber-500">
                    Low Stock Only
                </label>
            </div>
        </div>

        <x-admin.table :headers="['#','Item','Category','Supplier','Stock','Min Qty','Cost','Status','Actions']">
            @forelse($this->items as $item)
                @php $isLow = $item->quantity <= $item->minimum_quantity; @endphp
                <tr class="hover:bg-amber-500/5 transition {{ $isLow ? 'bg-red-500/10' : '' }}" wire:key="item-{{ $item->id }}">
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $item->id }}</td>
                    <td class="px-4 py-3">
                        <p class="text-sm font-medium text-white">{{ $item->name }}</p>
                        @if($isLow)
                            <p class="text-xs text-red-400 font-medium">⚠ Low Stock</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-300">{{ $item->category?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-300">{{ $item->supplier?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="text-sm font-semibold {{ $isLow ? 'text-red-400' : 'text-white' }}">
                            {{ number_format($item->quantity, 2) }} {{ $item->unit }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ number_format($item->minimum_quantity, 2) }} {{ $item->unit }}</td>
                    <td class="px-4 py-3 text-sm text-gray-300">${{ number_format($item->cost_price, 2) }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :label="ucfirst($item->status)" :color="$item->status === 'active' ? 'green' : 'gray'"/>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.inventory.items.show', $item) }}" class="text-blue-400 hover:text-blue-300 text-xs font-medium">View</a>
                            <a href="{{ route('admin.inventory.items.edit', $item) }}" class="text-amber-400 hover:text-amber-300 text-xs font-medium">Edit</a>
                            <button wire:click="delete({{ $item->id }})" wire:confirm="Delete this item?" class="text-red-400 hover:text-red-300 text-xs font-medium">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-12 text-center text-gray-400 text-sm">No inventory items found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->items->links() }}</div>
    </div>
</div>



