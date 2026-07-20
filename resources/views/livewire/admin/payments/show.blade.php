<?php

use App\Models\Payment;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['payment' => null]);

mount(function (int $id) {
    $this->payment = Payment::with(['order.customer', 'order.items.food', 'processor'])->findOrFail($id);
});

?>

<div>
    <div class="p-6 max-w-3xl">
        <x-admin.page-header
            title="Payment Details"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Payments','url'=>route('admin.payments.index')],['label'=>'#'.$payment->id]]"
        />

        @php $statusColors = ['pending'=>'yellow','completed'=>'green','failed'=>'red']; @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-4">Order Items</h3>
                    @if($payment->order)
                        <div class="space-y-2">
                            @foreach($payment->order->items as $item)
                                <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $item->food?->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}</p>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">${{ number_format($item->quantity * $item->unit_price, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-200 space-y-1 text-sm">
                            <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>${{ number_format($payment->order->subtotal, 2) }}</span></div>
                            <div class="flex justify-between text-gray-600"><span>Tax</span><span>${{ number_format($payment->order->tax, 2) }}</span></div>
                            <div class="flex justify-between font-bold text-gray-900 text-base pt-1 border-t border-gray-200"><span>Total</span><span>${{ number_format($payment->amount, 2) }}</span></div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Order not found.</p>
                    @endif
                </div>
            </div>

            <div class="space-y-5">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-3">Payment Info</h3>
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="text-xs text-gray-500">Status</dt>
                            <dd class="mt-0.5"><x-admin.badge :label="ucfirst($payment->status)" :color="$statusColors[$payment->status] ?? 'gray'"/></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Method</dt>
                            <dd class="font-medium text-gray-900 capitalize">{{ $payment->payment_method }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Amount</dt>
                            <dd class="font-bold text-gray-900 text-lg">${{ number_format($payment->amount, 2) }}</dd>
                        </div>
                        @if($payment->transaction_reference)
                            <div>
                                <dt class="text-xs text-gray-500">Transaction Ref</dt>
                                <dd class="font-mono text-xs text-gray-700">{{ $payment->transaction_reference }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs text-gray-500">Processed By</dt>
                            <dd class="font-medium text-gray-900">{{ $payment->processor?->name ?? '—' }}</dd>
                        </div>
                        @if($payment->paid_at)
                            <div>
                                <dt class="text-xs text-gray-500">Paid At</dt>
                                <dd class="text-gray-700">{{ $payment->paid_at->format('M d, Y H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                @if($payment->order)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h3 class="font-semibold text-gray-900 mb-3">Customer</h3>
                        <p class="text-sm font-medium text-gray-900">{{ $payment->order->customer?->name ?? 'Guest' }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->order->customer?->email ?? '' }}</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.orders.show', $payment->order) }}" class="text-orange-600 hover:text-orange-700 text-xs font-medium">View Order →</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>



