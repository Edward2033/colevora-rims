<?php

use App\Models\Order;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'statusFilter' => '', 'perPage' => 15]);

$orders = computed(function () {
    return Order::query()
        ->with(['customer', 'payment', 'table'])
        ->when($this->search, fn($q) => $q->where('order_number', 'like', "%{$this->search}%")
            ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$this->search}%")))
        ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
        ->latest()
        ->paginate($this->perPage);
});

$updateStatus = function (int $id, string $status) {
    Order::findOrFail($id)->update(['status' => $status]);
    session()->flash('success', 'Order status updated.');
};

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Orders"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Orders']]"
        />

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <!-- Filters -->
        <div class="glass-card rounded-xl border border-white/[0.08] p-4 mb-5">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search by order # or customer..."
                        class="w-full px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
                <select wire:model.live="statusFilter" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="" class="bg-gray-900">All Status</option>
                    <option value="pending" class="bg-gray-900">Pending</option>
                    <option value="preparing" class="bg-gray-900">Preparing</option>
                    <option value="ready" class="bg-gray-900">Ready</option>
                    <option value="served" class="bg-gray-900">Served</option>
                    <option value="completed" class="bg-gray-900">Completed</option>
                    <option value="cancelled" class="bg-gray-900">Cancelled</option>
                </select>
                <select wire:model.live="perPage" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="10" class="bg-gray-900">10 / page</option>
                    <option value="15" class="bg-gray-900">15 / page</option>
                    <option value="25" class="bg-gray-900">25 / page</option>
                    <option value="50" class="bg-gray-900">50 / page</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <x-admin.table :headers="['Order #','Customer','Type','Table','Status','Payment','Total','Date','Actions']">
            @forelse($this->orders as $order)
                @php
                    $statusColors = ['pending'=>'yellow','preparing'=>'blue','ready'=>'purple','served'=>'green','completed'=>'green','cancelled'=>'red'];
                    $payColors = ['completed'=>'green','pending'=>'yellow','failed'=>'red'];
                @endphp
                <tr class="hover:bg-amber-500/5 transition" wire:key="order-{{ $order->id }}">
                    <td class="px-4 py-3 text-xs font-mono font-medium text-gray-200">{{ $order->order_number }}</td>
                    <td class="px-4 py-3 text-sm text-gray-300">{{ $order->customer?->name ?? 'Guest' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', $order->order_type) }}</td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $order->table?->table_number ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :label="ucfirst($order->status)" :color="$statusColors[$order->status] ?? 'gray'"/>
                    </td>
                    <td class="px-4 py-3">
                        @if($order->payment)
                            <x-admin.badge :label="ucfirst($order->payment->status)" :color="$payColors[$order->payment->status] ?? 'gray'"/>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-white">${{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-400 hover:text-blue-300 text-xs font-medium">View</a>
                            @if(in_array($order->status, ['pending','preparing']))
                                <button wire:click="updateStatus({{ $order->id }}, 'cancelled')" wire:confirm="Cancel this order?" class="text-red-400 hover:text-red-300 text-xs font-medium">Cancel</button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-12 text-center text-gray-400 text-sm">No orders found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->orders->links() }}</div>
    </div>
</div>



