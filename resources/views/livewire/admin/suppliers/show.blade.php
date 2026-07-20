<?php
use function Livewire\Volt\{layout};
layout('components.layouts.admin');
?>

@php
    $id = $id ?? request()->route('id');
    $supplier = \App\Models\Supplier::with(['inventoryItems', 'purchases'])->findOrFail($id);
@endphp

<div>
    <div class="p-6 max-w-4xl">
        <x-admin.page-header
            title="Supplier Details"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Suppliers','url'=>route('admin.suppliers.index')],['label'=>$supplier->name]]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.suppliers.edit', $supplier) }}" variant="secondary">Edit</x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Contact Info</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs text-gray-500">Name</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $supplier->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Company</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $supplier->company_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Phone</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $supplier->phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Email</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $supplier->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Address</dt>
                        <dd class="text-sm text-gray-700">{{ $supplier->address ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Status</dt>
                        <dd class="mt-0.5">
                            <x-admin.badge :label="ucfirst($supplier->status)" :color="$supplier->status === 'active' ? 'green' : 'gray'"/>
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Inventory Items ({{ $supplier->inventoryItems->count() }})</h3>
                    @if($supplier->inventoryItems->count())
                        <div class="space-y-2">
                            @foreach($supplier->inventoryItems->take(8) as $item)
                                <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                                    <span class="text-sm text-gray-700">{{ $item->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $item->quantity }} {{ $item->unit }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400">No inventory items linked.</p>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Recent Purchases ({{ $supplier->purchases->count() }})</h3>
                    @if($supplier->purchases->count())
                        <div class="space-y-2">
                            @foreach($supplier->purchases->take(5) as $purchase)
                                <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                                    <span class="text-sm font-mono text-gray-700">{{ $purchase->purchase_number }}</span>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-medium text-gray-900">${{ number_format($purchase->total_amount, 2) }}</span>
                                        <x-admin.badge :label="ucfirst($purchase->status)" :color="$purchase->status === 'completed' ? 'green' : ($purchase->status === 'pending' ? 'yellow' : 'blue')"/>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400">No purchases yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
