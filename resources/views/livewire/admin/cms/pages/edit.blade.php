<?php

use App\Models\Page;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['pageId' => null, 'title' => '', 'slug' => '', 'content' => '', 'status' => 'active']);

mount(function (int $id) {
    $page = Page::findOrFail($id);
    $this->pageId = $page->id;
    $this->title = $page->title;
    $this->slug = $page->slug;
    $this->content = $page->content ?? '';
    $this->status = $page->status;
});

$save = function () {
    $this->validate([
        'title'   => 'required|string|max:200',
        'slug'    => "required|string|max:200|unique:pages,slug,{$this->pageId}",
        'content' => 'nullable|string',
        'status'  => 'required|in:active,inactive',
    ]);

    Page::findOrFail($this->pageId)->update([
        'title'   => $this->title,
        'slug'    => $this->slug,
        'content' => $this->content,
        'status'  => $this->status,
    ]);

    session()->flash('success', 'Page updated.');
    $this->redirect(route('admin.cms.pages.index'), navigate: false);
};

?>

<div>
    <div class="p-6 max-w-3xl">
        <x-admin.page-header
            title="Edit Page"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Pages','url'=>route('admin.cms.pages.index')],['label'=>'Edit']]"
        />

        <div class="glass-card rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <form wire:submit="save" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="title"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('title') border-red-400 @enderror">
                    @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Slug <span class="text-red-500">*</span></label>
                    <div class="flex items-center">
                        <span class="px-3 py-2 text-sm bg-gray-50 border border-r-0 border-gray-300 rounded-l-lg text-gray-500">/</span>
                        <input type="text" wire:model="slug"
                            class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-orange-500 @error('slug') border-red-400 @enderror">
                    </div>
                    @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Content</label>
                    <textarea wire:model="content" rows="10"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 font-mono"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Status</label>
                    <select wire:model="status" class="w-full sm:w-48 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.btn type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Update Page</span>
                        <span wire:loading>Updating...</span>
                    </x-admin.btn>
                    <x-admin.btn href="{{ route('admin.cms.pages.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>



