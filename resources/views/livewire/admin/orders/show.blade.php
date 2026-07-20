<?php

use App\Models\Order;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['order' => null]);

mount(function (int $id) {
    $this->order = Order::with(['customer', 'items.food', 'payment', 'table', 'assignments.employee.user'])->findOrFail($id);
});

$updateStatus = function (string $status) {
    $this->order->update(['status' => $status]);
    $this->order->refresh();
    $this->dispatch('order-status-updated', orderId: $this->order->id, status: $status);
    session()->flash('success', 'Order status updated to '.ucfirst($status).'.');
};

?>

<div>
    <div class="p-6 max-w-4xl">
        <x-admin.page-header
            title="Order #{{ $order->order_number }}"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Orders','url'=>route('admin.orders.index')],['label'=>$order->order_number]]"
        >
            <x-slot:actions>
                @php $statusColors = ['pending'=>'yellow','preparing'=>'blue','ready'=>'purple','served'=>'green','completed'=>'green','cancelled'=>'red']; @endphp
                <x-admin.badge :label="ucfirst($order->status)" :color="$statusColors[$order->status] ?? 'gray'" class="text-sm px-3 py-1"/>
            </x-slot:actions>
        </x-admin.page-header>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Order Info -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Items -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-4">Order Items</h3>
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0">
                                @if($item->food?->image)
                                    <img src="{{ asset('storage/' . $item->food->image) }}" alt="{{ $item->food->name }}" class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                                @else
                                    <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $item->food?->name ?? 'Unknown Item' }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->food?->category?->name ?? '' }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">${{ number_format($item->subtotal ?? ($item->quantity * $item->price), 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-200 space-y-1 text-sm">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
                        <div class="flex justify-between text-gray-600"><span>Tax</span><span>${{ number_format($order->tax, 2) }}</span></div>
                        @if($order->discount > 0)
                            <div class="flex justify-between text-green-600"><span>Discount</span><span>-${{ number_format($order->discount, 2) }}</span></div>
                        @endif
                        <div class="flex justify-between font-bold text-gray-900 text-base pt-1 border-t border-gray-200"><span>Total</span><span>${{ number_format($order->total_amount, 2) }}</span></div>
                    </div>
                </div>

                <!-- Status Actions -->
                @if($order->status !== 'completed' && $order->status !== 'cancelled')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h3 class="font-semibold text-gray-900 mb-3">Update Status</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['pending','preparing','ready','served','completed'] as $s)
                                @if($s !== $order->status)
                                    <button wire:click="updateStatus('{{ $s }}')"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition capitalize">
                                        {{ ucfirst($s) }}
                                    </button>
                                @endif
                            @endforeach
                            @if(in_array($order->status, ['pending','preparing']))
                                <button wire:click="updateStatus('cancelled')" wire:confirm="Cancel this order?"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition">
                                    Cancel Order
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-5">
                <!-- Customer -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-3">Customer</h3>
                    @if($order->customer)
                        <p class="text-sm font-medium text-gray-900">{{ $order->customer->name }}</p>
                        <p class="text-xs text-gray-500">{{ $order->customer->email }}</p>
                        <p class="text-xs text-gray-500">{{ $order->customer->phone ?? '—' }}</p>
                    @else
                        <p class="text-sm text-gray-500">Guest order</p>
                    @endif
                </div>

                <!-- Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-3">Details</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Type</span><span class="capitalize font-medium">{{ str_replace('_', ' ', $order->order_type) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Table</span><span class="font-medium">{{ $order->table?->table_number ?? '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Date</span><span class="font-medium">{{ $order->created_at->format('M d, Y H:i') }}</span></div>
                    </div>
                </div>

                <!-- Payment -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-3">Payment</h3>
                    @if($order->payment)
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">Method</span><span class="capitalize font-medium">{{ $order->payment->payment_method }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Amount</span><span class="font-semibold">${{ number_format($order->payment->amount, 2) }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Status</span>
                                <x-admin.badge :label="ucfirst($order->payment->status)" :color="$order->payment->status==='completed'?'green':($order->payment->status==='failed'?'red':'yellow')"/>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No payment recorded</p>
                    @endif
                </div>

                @if($order->notes)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h3 class="font-semibold text-gray-900 mb-2">Notes</h3>
                        <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>



