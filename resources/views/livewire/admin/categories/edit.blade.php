<?php

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use function Livewire\Volt\{state, mount, layout, uses};

uses([WithFileUploads::class]);
layout('components.layouts.admin');

state(['categoryId' => null, 'name' => '', 'slug' => '', 'description' => '', 'status' => 'active', 'existingImage' => null, 'image' => null]);

mount(function (int $id) {
    $cat = Category::findOrFail($id);
    $this->categoryId    = $cat->id;
    $this->name          = $cat->name;
    $this->slug          = $cat->slug;
    $this->description   = $cat->description ?? '';
    $this->status        = $cat->status;
    $this->existingImage = $cat->image;
});

$updatedName = function () {
    $this->slug = Str::slug($this->name);
};

$save = function () {
    $cat = Category::findOrFail($this->categoryId);

    $this->validate([
        'name'        => 'required|string|max:100|unique:categories,name,'.$this->categoryId,
        'slug'        => 'required|string|max:120|unique:categories,slug,'.$this->categoryId,
        'description' => 'nullable|string|max:500',
        'status'      => 'required|in:active,inactive',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
    ]);

    $imagePath = $cat->image;
    if ($this->image) {
        if ($imagePath) Storage::disk('public')->delete($imagePath);
        $imagePath = $this->image->store('categories', 'public');
    }

    $cat->update([
        'name'        => $this->name,
        'slug'        => $this->slug,
        'description' => $this->description,
        'status'      => $this->status,
        'image'       => $imagePath,
    ]);

    session()->flash('success', 'Category updated successfully.');
    $this->redirect(route('admin.categories.index'), navigate: false);
};

$removeImage = function () {
    $cat = Category::findOrFail($this->categoryId);
    if ($cat->image) {
        Storage::disk('public')->delete($cat->image);
        $cat->update(['image' => null]);
        $this->existingImage = null;
    }
};

?>

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Edit Category"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Categories','url'=>route('admin.categories.index')],['label'=>'Edit']]"
        />

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form wire:submit="save" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.live="name" wire:keyup="updatedName"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('name') border-red-400 @enderror">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Slug <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="slug"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 font-mono @error('slug') border-red-400 @enderror">
                    @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Description</label>
                    <textarea wire:model="description" rows="3"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Status</label>
                    <select wire:model="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Category Image</label>
                    @if($existingImage)
                        <div class="mb-3 flex items-center gap-3">
                            <img src="{{ Storage::url($existingImage) }}" class="h-20 w-20 object-cover rounded-lg border border-gray-200" alt="Current">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Current image</p>
                                <button type="button" wire:click="removeImage" wire:confirm="Remove this image?"
                                    class="text-xs text-red-600 hover:text-red-800 font-medium">Remove</button>
                            </div>
                        </div>
                    @endif
                    <input type="file" wire:model="image" accept="image/jpeg,image/png,image/webp,image/gif"
                        class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    <p class="mt-1 text-xs text-gray-400">JPG, PNG, WebP or GIF — max 5MB. Leave blank to keep current.</p>
                    @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    @if($image)
                        <img src="{{ $image->temporaryUrl() }}" class="mt-2 h-20 w-20 object-cover rounded-lg border border-gray-200" alt="Preview">
                    @endif
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.btn type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Update Category</span>
                        <span wire:loading>Updating...</span>
                    </x-admin.btn>
                    <x-admin.btn href="{{ route('admin.categories.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>



