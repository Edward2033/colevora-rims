<?php
use function Livewire\Volt\{layout};
layout('components.layouts.admin');
?>

@php
    $id = $id ?? request()->route('id');
    $item = \App\Models\InventoryItem::with(['category', 'supplier', 'stockTransactions' => fn($q) => $q->latest()->limit(10)])->findOrFail($id);
    $isLow = $item->quantity <= $item->minimum_quantity;
@endphp

<div>
    <div class="p-6 max-w-4xl">
        <x-admin.page-header
            title="Inventory Item Details"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Inventory','url'=>route('admin.inventory.items.index')],['label'=>$item->name]]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.inventory.items.edit', $item) }}" variant="secondary">Edit</x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Item Info</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs text-gray-500">Name</dt>
                        <dd class="text-sm font-bold text-gray-900">{{ $item->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Category</dt>
                        <dd class="text-sm text-gray-700">{{ $item->category?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Supplier</dt>
                        <dd class="text-sm text-gray-700">{{ $item->supplier?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Unit</dt>
                        <dd class="text-sm text-gray-700">{{ $item->unit }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Cost Price</dt>
                        <dd class="text-sm font-semibold text-gray-900">${{ number_format($item->cost_price, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Status</dt>
                        <dd class="mt-0.5">
                            <x-admin.badge :label="ucfirst($item->status)" :color="$item->status === 'active' ? 'green' : 'gray'"/>
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center {{ $isLow ? 'border-red-300 bg-red-50' : '' }}">
                        <p class="text-xs text-gray-500 mb-1">Current Stock</p>
                        <p class="text-3xl font-bold {{ $isLow ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($item->quantity, 2) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $item->unit }}</p>
                        @if($isLow)
                            <p class="text-xs text-red-500 font-medium mt-2">⚠ Below minimum</p>
                        @endif
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
                        <p class="text-xs text-gray-500 mb-1">Minimum Quantity</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($item->minimum_quantity, 2) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $item->unit }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Recent Stock Transactions</h3>
                    @if($item->stockTransactions->count())
                        <div class="space-y-2">
                            @foreach($item->stockTransactions as $tx)
                                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 capitalize">{{ $tx->type }}</p>
                                        @if($tx->notes)
                                            <p class="text-xs text-gray-400">{{ $tx->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold {{ $tx->quantity >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $tx->quantity >= 0 ? '+' : '' }}{{ number_format($tx->quantity, 2) }} {{ $item->unit }}
                                        </p>
                                        <p class="text-xs text-gray-400">{{ $tx->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400">No transactions yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
