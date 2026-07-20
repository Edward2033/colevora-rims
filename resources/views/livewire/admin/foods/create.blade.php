<?php

use App\Models\Food;
use App\Models\Category;
use Livewire\WithFileUploads;
use function Livewire\Volt\{state, computed, mount, layout, uses};

uses([WithFileUploads::class]);
layout('components.layouts.admin');

state([
    'name'           => '',
    'category_id'    => '',
    'description'    => '',
    'price'          => '',
    'discount_price' => '',
    'availability'   => true,
    'status'         => 'active',
    'image'          => null,
]);

$categories = computed(fn() => Category::active()->orderBy('name')->get());

$save = function () {
    $this->validate([
        'name'           => 'required|string|max:150',
        'category_id'    => 'required|exists:categories,id',
        'description'    => 'nullable|string|max:1000',
        'price'          => 'required|numeric|min:0',
        'discount_price' => 'nullable|numeric|min:0',
        'availability'   => 'boolean',
        'status'         => 'required|in:active,inactive',
        'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
    ]);

    $imagePath = null;
    if ($this->image) {
        $imagePath = $this->image->store('foods', 'public');
    }

    Food::create([
        'name'           => $this->name,
        'category_id'    => $this->category_id,
        'description'    => $this->description,
        'price'          => $this->price,
        'discount_price' => $this->discount_price ?: null,
        'availability'   => $this->availability,
        'status'         => $this->status,
        'image'          => $imagePath,
        'created_by'     => auth()->id(),
    ]);

    session()->flash('success', 'Food item created successfully.');
    $this->redirect(route('admin.foods.index'), navigate: false);
};

?>

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Add Food Item"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Foods','url'=>route('admin.foods.index')],['label'=>'Create']]"
        />

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="glass-card rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <form wire:submit="save" class="space-y-5" enctype="multipart/form-data">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('name') border-red-400 @enderror"
                            placeholder="e.g. Grilled Chicken">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Category <span class="text-red-500">*</span></label>
                        <select wire:model="category_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('category_id') border-red-400 @enderror">
                            <option value="">Select category...</option>
                            @foreach($this->categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Status</label>
                        <select wire:model="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Price ($) <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="price" step="0.01" min="0"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('price') border-red-400 @enderror"
                            placeholder="0.00">
                        @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Discount Price ($)</label>
                        <input type="number" wire:model="discount_price" step="0.01" min="0"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                            placeholder="Leave blank for no discount">
                        @error('discount_price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Description</label>
                        <textarea wire:model="description" rows="3"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                            placeholder="Optional description..."></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Food Image</label>
                        <input type="file" wire:model="image" accept="image/jpeg,image/png,image/webp,image/gif"
                            class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                        <p class="mt-1 text-xs text-gray-400">JPG, PNG, WebP or GIF — max 5MB</p>
                        @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        @if($image)
                            <img src="{{ $image->temporaryUrl() }}" class="mt-2 h-24 w-24 object-cover rounded-lg border border-gray-200" alt="Preview">
                        @endif
                    </div>

                    <div class="sm:col-span-2 flex items-center gap-3">
                        <input type="checkbox" wire:model="availability" id="availability" class="rounded border-gray-600 text-amber-500 focus:ring-amber-500">
                        <label for="availability" class="text-sm font-medium text-gray-200">Available for ordering</label>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.btn type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Save Food Item</span>
                        <span wire:loading>Saving...</span>
                    </x-admin.btn>
                    <x-admin.btn href="{{ route('admin.foods.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>
