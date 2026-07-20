<?php

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\InventoryItem;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state([
    'supplier_id' => '',
    'items' => [['inventory_item_id' => '', 'quantity' => 1, 'unit_price' => 0]],
]);

$suppliers = computed(fn() => Supplier::active()->orderBy('name')->get());
$inventoryItems = computed(fn() => InventoryItem::active()->orderBy('name')->get());

$addItem = function () {
    $this->items[] = ['inventory_item_id' => '', 'quantity' => 1, 'unit_price' => 0];
};

$removeItem = function (int $index) {
    if (count($this->items) > 1) {
        array_splice($this->items, $index, 1);
        $this->items = array_values($this->items);
    }
};

$totalAmount = computed(function () {
    return collect($this->items)->sum(fn($i) => ($i['quantity'] ?? 0) * ($i['unit_price'] ?? 0));
});

$save = function () {
    $this->validate([
        'supplier_id'                    => 'required|exists:suppliers,id',
        'items'                          => 'required|array|min:1',
        'items.*.inventory_item_id'      => 'required|exists:inventory_items,id',
        'items.*.quantity'               => 'required|numeric|min:0.01',
        'items.*.unit_price'             => 'required|numeric|min:0',
    ]);

    $total = collect($this->items)->sum(fn($i) => $i['quantity'] * $i['unit_price']);

    $purchase = Purchase::create([
        'supplier_id'  => $this->supplier_id,
        'total_amount' => $total,
        'status'       => 'pending',
        'created_by'   => auth()->id(),
    ]);

    foreach ($this->items as $item) {
        PurchaseItem::create([
            'purchase_id'       => $purchase->id,
            'inventory_item_id' => $item['inventory_item_id'],
            'quantity'          => $item['quantity'],
            'unit_price'        => $item['unit_price'],
            'subtotal'          => $item['quantity'] * $item['unit_price'],
        ]);
    }

    session()->flash('success', 'Purchase order created successfully.');
    $this->redirect(route('admin.purchases.show', $purchase), navigate: false);
};

?>

<div>
    <div class="p-6 max-w-3xl">
        <x-admin.page-header
            title="New Purchase Order"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Purchases','url'=>route('admin.purchases.index')],['label'=>'Create']]"
        />

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form wire:submit="save" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Supplier <span class="text-red-500">*</span></label>
                    <select wire:model="supplier_id" class="w-full sm:w-80 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('supplier_id') border-red-400 @enderror">
                        <option value="">Select supplier...</option>
                        @foreach($this->suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-medium text-gray-700">Items <span class="text-red-500">*</span></label>
                        <button type="button" wire:click="addItem"
                            class="text-xs text-orange-600 hover:text-orange-700 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Item
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach($this->items as $index => $item)
                            <div class="grid grid-cols-12 gap-2 items-start" wire:key="item-{{ $index }}">
                                <div class="col-span-5">
                                    <select wire:model="items.{{ $index }}.inventory_item_id"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('items.'.$index.'.inventory_item_id') border-red-400 @enderror">
                                        <option value="">Select item...</option>
                                        @foreach($this->inventoryItems as $inv)
                                            <option value="{{ $inv->id }}">{{ $inv->name }} ({{ $inv->unit }})</option>
                                        @endforeach
                                    </select>
                                    @error('items.'.$index.'.inventory_item_id')<p class="mt-0.5 text-xs text-red-600">Required</p>@enderror
                                </div>
                                <div class="col-span-3">
                                    <input type="number" wire:model="items.{{ $index }}.quantity" step="0.01" min="0.01"
                                        placeholder="Qty"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('items.'.$index.'.quantity') border-red-400 @enderror">
                                </div>
                                <div class="col-span-3">
                                    <input type="number" wire:model="items.{{ $index }}.unit_price" step="0.01" min="0"
                                        placeholder="Unit $"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('items.'.$index.'.unit_price') border-red-400 @enderror">
                                </div>
                                <div class="col-span-1 flex justify-center pt-2">
                                    @if(count($this->items) > 1)
                                        <button type="button" wire:click="removeItem({{ $index }})" class="text-red-400 hover:text-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-200 flex justify-end">
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Total Amount</p>
                            <p class="text-xl font-bold text-gray-900">${{ number_format($this->totalAmount, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.btn type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Create Purchase Order</span>
                        <span wire:loading>Creating...</span>
                    </x-admin.btn>
                    <x-admin.btn href="{{ route('admin.purchases.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>



