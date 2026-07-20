<?php
use function Livewire\Volt\{layout};
layout('components.layouts.admin');
?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Create Order"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Orders','url'=>route('admin.orders.index')],['label'=>'Create']]"
        />
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-gray-500 text-sm">Orders are created by customers through the public website or by staff through the kitchen workflow.</p>
            <div class="mt-4">
                <x-admin.btn href="{{ route('admin.orders.index') }}" variant="secondary">Back to Orders</x-admin.btn>
            </div>
        </div>
    </div>
</div>
