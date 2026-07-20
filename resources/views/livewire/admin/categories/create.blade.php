<?php

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use function Livewire\Volt\{state, mount, layout, uses};

uses([WithFileUploads::class]);
layout('components.layouts.admin');

state(['name' => '', 'slug' => '', 'description' => '', 'status' => 'active', 'image' => null]);

$updatedName = function () {
    $this->slug = Str::slug($this->name);
};

$save = function () {
    $this->validate([
        'name'        => 'required|string|max:100|unique:categories,name',
        'slug'        => 'required|string|max:120|unique:categories,slug',
        'description' => 'nullable|string|max:500',
        'status'      => 'required|in:active,inactive',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
    ]);

    $imagePath = $this->image ? $this->image->store('categories', 'public') : null;

    Category::create([
        'name'        => $this->name,
        'slug'        => $this->slug,
        'description' => $this->description,
        'status'      => $this->status,
        'image'       => $imagePath,
        'created_by'  => auth()->id(),
    ]);

    session()->flash('success', 'Category created successfully.');
    $this->redirect(route('admin.categories.index'), navigate: false);
};

?>

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Create Category"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Categories','url'=>route('admin.categories.index')],['label'=>'Create']]"
        />

        <div class="glass-card p-6 rounded-xl">
            <form wire:submit="save" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.live="name" wire:keyup="updatedName"
                        class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-900/90 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-transparent @error('name') border-red-400 @enderror"
                        placeholder="e.g. Starters">
                    @error('name')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Slug <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="slug"
                        class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-900/90 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-transparent font-mono @error('slug') border-red-400 @enderror">
                    @error('slug')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Description</label>
                    <textarea wire:model="description" rows="3"
                        class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-900/90 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-transparent"
                        placeholder="Optional description..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Status</label>
                    <select wire:model="status" class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-900/90 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Category Image</label>
                    <input type="file" wire:model="image" accept="image/jpeg,image/png,image/webp,image/gif"
                        class="w-full text-sm text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gold-500/10 file:text-gold-600 dark:file:text-gold-400 hover:file:bg-gold-500/20">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">JPG, PNG, WebP or GIF — max 5MB</p>
                    @error('image')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    @if($image)
                        <img src="{{ $image->temporaryUrl() }}" class="mt-2 h-20 w-20 object-cover rounded-lg border border-gray-200 dark:border-gray-700" alt="Preview">
                    @endif
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.btn type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Save Category</span>
                        <span wire:loading>Saving...</span>
                    </x-admin.btn>
                    <x-admin.btn href="{{ route('admin.categories.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>



