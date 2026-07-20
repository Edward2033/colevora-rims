<?php

use App\Models\InventoryItem;
use App\Models\InventoryAlert;
use App\Models\Purchase;
use App\Models\StockTransaction;
use function Livewire\Volt\{state, mount, layout};

layout('components.layouts.employee');

state([
    'lowStockItems'      => [],
    'pendingPurchases'   => [],
    'activeAlerts'       => [],
    'recentTransactions' => [],
    'totalItems'         => 0,
    'outOfStock'         => 0,
]);

mount(function () { $this->loadData(); });

$loadData = function () {
    $this->lowStockItems      = InventoryItem::lowStock()->with('supplier')->orderBy('quantity')->get();
    $this->pendingPurchases   = Purchase::where('status', 'pending')->with(['supplier', 'items.inventoryItem'])->latest()->get();
    $this->activeAlerts       = InventoryAlert::active()->with('inventoryItem')->latest()->limit(15)->get();
    $this->recentTransactions = StockTransaction::with('inventoryItem')->latest()->limit(10)->get();
    $this->totalItems         = InventoryItem::count();
    $this->outOfStock         = InventoryItem::where('quantity', '<=', 0)->count();
};

$approvePurchase = function (int $purchaseId) {
    $purchase = Purchase::find($purchaseId);
    if ($purchase && $purchase->status === 'pending') {
        $purchase->update(['status' => 'approved']);
        $this->loadData();
        session()->flash('success', 'Purchase approved.');
    }
};

$rejectPurchase = function (int $purchaseId) {
    $purchase = Purchase::find($purchaseId);
    if ($purchase && $purchase->status === 'pending') {
        $purchase->update(['status' => 'rejected']);
        $this->loadData();
        session()->flash('success', 'Purchase rejected.');
    }
};

$resolveAlert = function (int $alertId) {
    $alert = InventoryAlert::find($alertId);
    if ($alert) { $alert->resolve(); $this->loadData(); }
};

?>

<div class="space-y-6" wire:poll.15s="loadData">

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/20 text-green-400 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white">Inventory Dashboard</h1>
            <p class="text-sm text-gray-400 mt-0.5">Monitor stock levels, alerts and purchase orders</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-green-400 bg-green-400/10 border border-green-400/20 rounded-full px-4 py-2 w-fit">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            Live · refreshes every 15s
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 bg-blue-500/10 border border-blue-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Total Items</p>
                <div class="h-8 w-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $totalItems }}</p>
            <p class="text-xs text-blue-400/70 mt-1">In inventory</p>
        </div>

        <div class="rounded-2xl p-5 bg-red-500/10 border border-red-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-red-400 uppercase tracking-wider">Low Stock</p>
                <div class="h-8 w-8 rounded-lg bg-red-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ count($lowStockItems) }}</p>
            <p class="text-xs text-red-400/70 mt-1">Need restocking</p>
        </div>

        <div class="rounded-2xl p-5 bg-gray-500/10 border border-gray-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Out of Stock</p>
                <div class="h-8 w-8 rounded-lg bg-gray-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $outOfStock }}</p>
            <p class="text-xs text-gray-400/70 mt-1">Completely empty</p>
        </div>

        <div class="rounded-2xl p-5 bg-yellow-500/10 border border-yellow-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-yellow-400 uppercase tracking-wider">Pending Orders</p>
                <div class="h-8 w-8 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ count($pendingPurchases) }}</p>
            <p class="text-xs text-yellow-400/70 mt-1">Awaiting approval</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Low Stock --}}
        <div>
            <div class="flex items-center gap-3 mb-4">
                <span class="w-3 h-3 rounded-full bg-red-400 flex-shrink-0"></span>
                <h2 class="text-base font-bold text-white">Low Stock Items</h2>
                @if(count($lowStockItems) > 0)
                    <span class="bg-red-400/20 text-red-400 text-xs font-bold px-2.5 py-0.5 rounded-full border border-red-400/30">{{ count($lowStockItems) }}</span>
                @endif
            </div>
            @if(count($lowStockItems) > 0)
                <div class="rounded-2xl overflow-hidden divide-y divide-white/5 bg-white/[0.04] border border-white/[0.08]">
                    @foreach($lowStockItems as $item)
                        <div class="p-4 flex justify-between items-center" wire:key="ls-{{ $item->id }}">
                            <div>
                                <p class="font-semibold text-white text-sm">{{ $item->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Stock: <span class="font-bold text-red-400">{{ $item->quantity }} {{ $item->unit }}</span>
                                    <span class="text-gray-600 mx-1">/</span>
                                    Min: <span class="text-gray-300">{{ $item->minimum_quantity }} {{ $item->unit }}</span>
                                </p>
                                @if($item->supplier)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $item->supplier->name }}</p>
                                @endif
                            </div>
                            <span class="bg-red-400/20 text-red-400 text-xs font-bold px-2.5 py-1 rounded-full border border-red-400/30 flex-shrink-0">LOW</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl p-8 text-center bg-white/[0.03] border border-white/[0.07]">
                    <svg class="w-10 h-10 mx-auto text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-gray-500 text-sm">All stock levels are healthy</p>
                </div>
            @endif
        </div>

        {{-- Pending Purchases --}}
        <div>
            <div class="flex items-center gap-3 mb-4">
                <span class="w-3 h-3 rounded-full bg-yellow-400 flex-shrink-0"></span>
                <h2 class="text-base font-bold text-white">Pending Purchase Orders</h2>
            </div>
            @if(count($pendingPurchases) > 0)
                <div class="space-y-3">
                    @foreach($pendingPurchases as $purchase)
                        <div class="rounded-2xl p-4 bg-white/[0.04] border border-white/[0.07] border-l-4 border-l-yellow-400" wire:key="po-{{ $purchase->id }}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-bold text-white text-sm">{{ $purchase->purchase_number }}</p>
                                    <p class="text-xs text-gray-400">{{ $purchase->supplier->name ?? 'Unknown Supplier' }}</p>
                                </div>
                                <p class="font-bold text-amber-400">${{ number_format($purchase->total_amount, 2) }}</p>
                            </div>
                            @if($purchase->items->count() > 0)
                                <p class="text-xs text-gray-500 mb-3">{{ $purchase->items->count() }} item(s) · {{ $purchase->created_at->diffForHumans() }}</p>
                            @endif
                            <div class="flex gap-2">
                                <button wire:click="approvePurchase({{ $purchase->id }})"
                                    class="flex-1 bg-green-600 hover:bg-green-500 text-white text-xs font-semibold py-2 rounded-xl transition">Approve</button>
                                <button wire:click="rejectPurchase({{ $purchase->id }})"
                                    class="flex-1 bg-red-600 hover:bg-red-500 text-white text-xs font-semibold py-2 rounded-xl transition">Reject</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl p-8 text-center bg-white/[0.03] border border-white/[0.07]">
                    <p class="text-gray-500 text-sm">No pending purchase orders</p>
                </div>
            @endif
        </div>

    </div>

    {{-- Active Alerts --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="w-3 h-3 rounded-full bg-orange-400 animate-pulse flex-shrink-0"></span>
            <h2 class="text-base font-bold text-white">Active Inventory Alerts</h2>
            @if(count($activeAlerts) > 0)
                <span class="bg-orange-400/20 text-orange-400 text-xs font-bold px-2.5 py-0.5 rounded-full border border-orange-400/30">{{ count($activeAlerts) }}</span>
            @endif
        </div>
        @if(count($activeAlerts) > 0)
            <div class="rounded-2xl overflow-hidden bg-white/[0.04] border border-white/[0.08]">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-white/[0.04] border-b border-white/[0.07]">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Message</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($activeAlerts as $alert)
                                <tr wire:key="alert-{{ $alert->id }}" class="hover:bg-white/[0.03] transition">
                                    <td class="px-4 py-3 font-medium text-white">{{ $alert->inventoryItem->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-400 text-xs max-w-xs truncate">{{ $alert->message }}</td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $alert->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">
                                        <button wire:click="resolveAlert({{ $alert->id }})"
                                            class="text-green-400 hover:text-green-300 text-xs font-semibold transition">Resolve</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="rounded-2xl p-8 text-center bg-white/[0.03] border border-white/[0.07]">
                <svg class="w-10 h-10 mx-auto text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-gray-500 text-sm">No active alerts</p>
            </div>
        @endif
    </div>

    {{-- Recent Transactions --}}
    @if(count($recentTransactions) > 0)
    <div>
        <h2 class="text-base font-bold text-white mb-4">Recent Stock Transactions</h2>
        <div class="rounded-2xl overflow-hidden bg-white/[0.04] border border-white/[0.08]">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white/[0.04] border-b border-white/[0.07]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($recentTransactions as $tx)
                            <tr wire:key="tx-{{ $tx->id }}" class="hover:bg-white/[0.03] transition">
                                <td class="px-4 py-3 font-medium text-white">{{ $tx->inventoryItem->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $tx->quantity > 0 ? 'bg-green-400/20 text-green-400 border border-green-400/30' : 'bg-red-400/20 text-red-400 border border-red-400/30' }}">
                                        {{ ucfirst($tx->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-semibold {{ $tx->quantity > 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $tx->quantity > 0 ? '+' : '' }}{{ $tx->quantity }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $tx->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
