<?php

use App\Models\Food;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['food' => null]);

mount(function (int $id) {
    $this->food = Food::with(['category', 'creator', 'ingredients.inventoryItem'])->findOrFail($id);
});

?>

<div>
    <div class="p-6 max-w-3xl">
        <x-admin.page-header
            title="Food Details"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Foods','url'=>route('admin.foods.index')],['label'=>$food->name]]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.foods.edit', $food) }}" variant="secondary">Edit</x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">Name:</span> <span class="font-semibold text-gray-900 ml-2">{{ $food->name }}</span></div>
                <div><span class="text-gray-500">Category:</span> <span class="ml-2">{{ $food->category?->name ?? '—' }}</span></div>
                <div><span class="text-gray-500">Price:</span> <span class="font-semibold ml-2">${{ number_format($food->price, 2) }}</span></div>
                <div><span class="text-gray-500">Discount:</span> <span class="ml-2">{{ $food->discount_price ? '$'.number_format($food->discount_price,2) : '—' }}</span></div>
                <div><span class="text-gray-500">Status:</span>
                    <x-admin.badge :label="ucfirst($food->status)" :color="$food->status==='active'?'blue':'gray'" class="ml-2"/>
                </div>
                <div><span class="text-gray-500">Available:</span>
                    <x-admin.badge :label="$food->availability?'Yes':'No'" :color="$food->availability?'green':'red'" class="ml-2"/>
                </div>
                <div><span class="text-gray-500">Created by:</span> <span class="ml-2">{{ $food->creator?->name ?? 'System' }}</span></div>
                <div><span class="text-gray-500">Created:</span> <span class="ml-2">{{ $food->created_at->format('M d, Y') }}</span></div>
            </div>

            @if($food->description)
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mb-1 uppercase font-medium">Description</p>
                    <p class="text-sm text-gray-700">{{ $food->description }}</p>
                </div>
            @endif

            @if($food->ingredients->count())
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mb-2 uppercase font-medium">Ingredients</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($food->ingredients as $ing)
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                                {{ $ing->inventoryItem?->name ?? 'Unknown' }} — {{ $ing->quantity }} {{ $ing->unit }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>



