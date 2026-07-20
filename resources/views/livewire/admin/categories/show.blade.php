<?php

use App\Models\Category;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['category' => null]);

mount(function (int $id) {
    $this->category = Category::withCount('foods')->with('creator')->findOrFail($id);
});

?>

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Category Details"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Categories','url'=>route('admin.categories.index')],['label'=>$category->name]]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.categories.edit', $category) }}" variant="secondary">Edit</x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">Name:</span> <span class="font-medium text-gray-900 ml-2">{{ $category->name }}</span></div>
                <div><span class="text-gray-500">Slug:</span> <span class="font-mono text-gray-700 ml-2">{{ $category->slug }}</span></div>
                <div><span class="text-gray-500">Status:</span>
                    <x-admin.badge :label="ucfirst($category->status)" :color="$category->status === 'active' ? 'green' : 'gray'" class="ml-2"/>
                </div>
                <div><span class="text-gray-500">Foods:</span> <span class="font-medium ml-2">{{ $category->foods_count }}</span></div>
                <div><span class="text-gray-500">Created by:</span> <span class="ml-2">{{ $category->creator?->name ?? 'System' }}</span></div>
                <div><span class="text-gray-500">Created:</span> <span class="ml-2">{{ $category->created_at->format('M d, Y H:i') }}</span></div>
            </div>
            @if($category->description)
                <div class="pt-2 border-t border-gray-100">
                    <p class="text-sm text-gray-500 mb-1">Description</p>
                    <p class="text-sm text-gray-700">{{ $category->description }}</p>
                </div>
            @endif
        </div>
    </div>
</div>



