<?php

use App\Models\HeroSlide;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use function Livewire\Volt\{state, mount, layout, uses};

uses([WithFileUploads::class]);
layout('components.layouts.admin');

state([
    'slideId' => null, 'title' => '', 'subtitle' => '',
    'button_text' => '', 'button_link' => '',
    'status' => 'active', 'ordering' => 1,
    'existingImage' => null, 'image' => null,
]);

mount(function (int $id) {
    $slide = HeroSlide::findOrFail($id);
    $this->slideId       = $slide->id;
    $this->title         = $slide->title;
    $this->subtitle      = $slide->subtitle ?? '';
    $this->existingImage = $slide->image;
    $this->button_text   = $slide->button_text ?? '';
    $this->button_link   = $slide->button_link ?? '';
    $this->status        = $slide->status;
    $this->ordering      = $slide->ordering;
});

$save = function () {
    $this->validate([
        'title'       => 'required|string|max:200',
        'subtitle'    => 'nullable|string|max:500',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        'button_text' => 'nullable|string|max:100',
        'button_link' => 'nullable|string|max:500',
        'status'      => 'required|in:active,inactive',
        'ordering'    => 'required|integer|min:1',
    ]);

    $slide = HeroSlide::findOrFail($this->slideId);
    $imagePath = $slide->image;
    if ($this->image) {
        if ($imagePath) Storage::disk('public')->delete($imagePath);
        $imagePath = $this->image->store('hero-slides', 'public');
    }

    $slide->update([
        'title'       => $this->title,
        'subtitle'    => $this->subtitle,
        'image'       => $imagePath,
        'button_text' => $this->button_text,
        'button_link' => $this->button_link,
        'status'      => $this->status,
        'ordering'    => $this->ordering,
    ]);

    session()->flash('success', 'Hero slide updated.');
    $this->redirect(route('admin.cms.hero-slides.index'), navigate: false);
};

$removeImage = function () {
    $slide = HeroSlide::findOrFail($this->slideId);
    if ($slide->image) {
        Storage::disk('public')->delete($slide->image);
        $slide->update(['image' => null]);
        $this->existingImage = null;
    }
};

?>

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Edit Hero Slide"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Hero Slides','url'=>route('admin.cms.hero-slides.index')],['label'=>'Edit']]"
        />

        <div class="glass-card p-6 rounded-xl">
            <form wire:submit="save" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="title"
                            class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-900/90 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500 @error('title') border-red-400 @enderror">
                        @error('title')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Subtitle</label>
                        <textarea wire:model="subtitle" rows="2"
                            class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-900/90 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500"></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Slide Image</label>
                        @if($existingImage)
                            <div class="mb-3 flex items-center gap-3">
                                <img src="{{ Storage::url($existingImage) }}" class="h-20 w-40 object-cover rounded-lg border border-gray-200 dark:border-gray-700" alt="Current">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Current image</p>
                                    <button type="button" wire:click="removeImage" wire:confirm="Remove this image?"
                                        class="text-xs text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium">Remove</button>
                                </div>
                            </div>
                        @endif
                        <input type="file" wire:model="image" accept="image/jpeg,image/png,image/webp,image/gif"
                            class="w-full text-sm text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gold-500/10 file:text-gold-600 dark:file:text-gold-400 hover:file:bg-gold-500/20">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">JPG, PNG, WebP or GIF — max 5MB. Leave blank to keep current.</p>
                        @error('image')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        @if($image)
                            <img src="{{ $image->temporaryUrl() }}" class="mt-2 h-24 w-full object-cover rounded-lg border border-gray-200 dark:border-gray-700" alt="Preview">
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Button Text</label>
                        <input type="text" wire:model="button_text"
                            class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-900/90 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Button Link</label>
                        <input type="text" wire:model="button_link"
                            class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-900/90 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Order</label>
                        <input type="number" wire:model="ordering" min="1"
                            class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-900/90 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Status</label>
                        <select wire:model="status" class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-900/90 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.btn type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Update Slide</span>
                        <span wire:loading>Updating...</span>
                    </x-admin.btn>
                    <x-admin.btn href="{{ route('admin.cms.hero-slides.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>



