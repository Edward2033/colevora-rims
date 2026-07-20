<?php
use function Livewire\Volt\{layout};
layout('components.layouts.admin');
?>

@php
    $id = $id ?? request()->route('id');
    $purchase = \App\Models\Purchase::with(['supplier', 'creator', 'items.inventoryItem'])->findOrFail($id);
    $statusColors = ['pending'=>'yellow','approved'=>'blue','received'=>'purple','completed'=>'green','cancelled'=>'red'];
@endphp

<div>
    <div class="p-6 max-w-4xl">
        <x-admin.page-header
            title="Purchase Order"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Purchases','url'=>route('admin.purchases.index')],['label'=>$purchase->purchase_number]]"
        >
            <x-slot:actions>
                <x-admin.badge :label="ucfirst($purchase->status)" :color="$statusColors[$purchase->status] ?? 'gray'"/>
            </x-slot:actions>
        </x-admin.page-header>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-4">Items</h3>
                    <div class="space-y-2">
                        @foreach($purchase->items as $item)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $item->inventoryItem?->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->quantity }} {{ $item->inventoryItem?->unit }} × ${{ number_format($item->unit_price, 2) }}</p>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">${{ number_format($item->subtotal, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-200 flex justify-end">
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Total</p>
                            <p class="text-xl font-bold text-gray-900">${{ number_format($purchase->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>

                @if(!in_array($purchase->status, ['completed', 'cancelled']))
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h3 class="font-semibold text-gray-900 mb-3">Actions</h3>
                        <div class="flex flex-wrap gap-2">
                            @if($purchase->status === 'pending')
                                <form method="POST" action="{{ route('admin.purchases.update', $purchase) }}" class="inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="action" value="approve">
                                    <x-admin.btn type="submit" variant="success" size="sm">Approve</x-admin.btn>
                                </form>
                                <form method="POST" action="{{ route('admin.purchases.update', $purchase) }}" class="inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="action" value="cancel">
                                    <x-admin.btn type="submit" variant="danger" size="sm">Cancel</x-admin.btn>
                                </form>
                            @endif
                            @if($purchase->status === 'approved')
                                <form method="POST" action="{{ route('admin.purchases.update', $purchase) }}" class="inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="action" value="receive">
                                    <x-admin.btn type="submit" variant="primary" size="sm">Mark as Received</x-admin.btn>
                                </form>
                                <form method="POST" action="{{ route('admin.purchases.update', $purchase) }}" class="inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="action" value="cancel">
                                    <x-admin.btn type="submit" variant="danger" size="sm">Cancel</x-admin.btn>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-5">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-3">Purchase Info</h3>
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="text-xs text-gray-500">PO Number</dt>
                            <dd class="font-mono font-medium text-gray-900">{{ $purchase->purchase_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Supplier</dt>
                            <dd class="font-medium text-gray-900">{{ $purchase->supplier?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Created By</dt>
                            <dd class="font-medium text-gray-900">{{ $purchase->creator?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Date</dt>
                            <dd class="font-medium text-gray-900">{{ $purchase->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
