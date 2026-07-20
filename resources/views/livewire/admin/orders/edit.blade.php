<?php
use function Livewire\Volt\{layout};
layout('components.layouts.admin');
?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Edit Order"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Orders','url'=>route('admin.orders.index')],['label'=>'Edit']]"
        />
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-gray-500 text-sm">Order status can be updated from the order details page or through the kitchen workflow dashboards.</p>
            <div class="mt-4">
                <x-admin.btn href="{{ route('admin.orders.index') }}" variant="secondary">Back to Orders</x-admin.btn>
            </div>
        </div>
    </div>
</div>
