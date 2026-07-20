<?php

use App\Models\Payment;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'statusFilter' => '', 'methodFilter' => '', 'perPage' => 15]);

$payments = computed(function () {
    return Payment::query()
        ->with(['order.customer', 'processor'])
        ->when($this->search, fn($q) => $q->where('transaction_reference', 'like', "%{$this->search}%")
            ->orWhereHas('order', fn($q2) => $q2->where('order_number', 'like', "%{$this->search}%")))
        ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
        ->when($this->methodFilter, fn($q) => $q->where('payment_method', $this->methodFilter))
        ->latest()
        ->paginate($this->perPage);
});

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Payments"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Payments']]"
        />

        <div class="glass-card rounded-xl border border-white/[0.08] p-4 mb-5">
            <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
                <div class="flex-1 min-w-48">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search by order # or transaction ref..."
                        class="w-full px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
                <select wire:model.live="statusFilter" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="" class="bg-gray-900">All Status</option>
                    <option value="pending" class="bg-gray-900">Pending</option>
                    <option value="completed" class="bg-gray-900">Completed</option>
                    <option value="failed" class="bg-gray-900">Failed</option>
                </select>
                <select wire:model.live="methodFilter" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="" class="bg-gray-900">All Methods</option>
                    <option value="cash" class="bg-gray-900">Cash</option>
                    <option value="card" class="bg-gray-900">Card</option>
                    <option value="online" class="bg-gray-900">Online</option>
                </select>
                <select wire:model.live="perPage" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="10" class="bg-gray-900">10 / page</option>
                    <option value="15" class="bg-gray-900">15 / page</option>
                    <option value="25" class="bg-gray-900">25 / page</option>
                </select>
            </div>
        </div>

        <x-admin.table :headers="['#','Order','Customer','Method','Amount','Status','Processed By','Date','Actions']">
            @forelse($this->payments as $payment)
                @php $statusColors = ['pending'=>'yellow','completed'=>'green','failed'=>'red']; @endphp
                <tr class="hover:bg-amber-500/5 transition" wire:key="pay-{{ $payment->id }}">
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $payment->id }}</td>
                    <td class="px-4 py-3 text-xs font-mono text-gray-300">{{ $payment->order?->order_number ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-300">{{ $payment->order?->customer?->name ?? 'Guest' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-300 capitalize">{{ $payment->payment_method }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-white">${{ number_format($payment->amount, 2) }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :label="ucfirst($payment->status)" :color="$statusColors[$payment->status] ?? 'gray'"/>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-300">{{ $payment->processor?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $payment->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.payments.show', $payment) }}" class="text-blue-400 hover:text-blue-300 text-xs font-medium">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-12 text-center text-gray-400 text-sm">No payments found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->payments->links() }}</div>
    </div>
</div>



