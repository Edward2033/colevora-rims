<?php
use function Livewire\Volt\{layout};
layout('components.layouts.admin');
?>

@php
    $id = $id ?? request()->route('id');
    $table = \App\Models\RestaurantTable::with(['orders' => fn($q) => $q->latest()->limit(5)])->findOrFail($id);
    $statusColors = ['available'=>'green','occupied'=>'red','reserved'=>'yellow','maintenance'=>'gray'];
@endphp

<div>
    <div class="p-6 max-w-3xl">
        <x-admin.page-header
            title="Table Details"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Tables','url'=>route('admin.tables.index')],['label'=>'Table '.$table->table_number]]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.tables.edit', $table) }}" variant="secondary">Edit</x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="w-16 h-16 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-xl mx-auto mb-4">
                    {{ $table->table_number }}
                </div>
                <p class="text-sm text-gray-500">{{ $table->capacity }} seats</p>
                <p class="text-xs text-gray-400 mt-1">{{ $table->location ?? 'No location' }}</p>
                <div class="mt-3">
                    <x-admin.badge :label="ucfirst($table->status)" :color="$statusColors[$table->status] ?? 'gray'"/>
                </div>
                <p class="text-xs font-mono text-gray-300 mt-3 break-all">QR: {{ $table->qr_code }}</p>
            </div>

            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Recent Orders</h3>
                @if($table->orders->count())
                    <div class="space-y-2">
                        @foreach($table->orders as $order)
                            @php $orderColors = ['pending'=>'yellow','preparing'=>'blue','ready'=>'purple','served'=>'green','completed'=>'green','cancelled'=>'red']; @endphp
                            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                <div>
                                    <p class="text-sm font-mono font-medium text-gray-900">{{ $order->order_number }}</p>
                                    <p class="text-xs text-gray-400">{{ $order->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-semibold text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                                    <x-admin.badge :label="ucfirst($order->status)" :color="$orderColors[$order->status] ?? 'gray'"/>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">No orders for this table yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
