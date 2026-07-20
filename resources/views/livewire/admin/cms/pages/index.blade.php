<?php

use App\Models\Page;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'perPage' => 15]);

$pages = computed(function () {
    return Page::query()
        ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%")
            ->orWhere('slug', 'like', "%{$this->search}%"))
        ->latest()
        ->paginate($this->perPage);
});

$delete = function (int $id) {
    Page::findOrFail($id)->delete();
    session()->flash('success', 'Page deleted.');
};

$toggleStatus = function (int $id) {
    $page = Page::findOrFail($id);
    $page->update(['status' => $page->status === 'active' ? 'inactive' : 'active']);
};

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="CMS Pages"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'CMS'],['label'=>'Pages']]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.cms.pages.create') }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Page
                </x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-5">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Search pages..."
                class="w-full sm:w-80 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
        </div>

        <x-admin.table :headers="['#','Title','Slug','Status','Updated','Actions']">
            @forelse($this->pages as $page)
                <tr class="hover:bg-gray-50 transition" wire:key="page-{{ $page->id }}">
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $page->id }}</td>
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $page->title }}</td>
                    <td class="px-4 py-3 text-xs font-mono text-gray-500">/{{ $page->slug }}</td>
                    <td class="px-4 py-3">
                        <button wire:click="toggleStatus({{ $page->id }})" class="focus:outline-none">
                            <x-admin.badge :label="ucfirst($page->status)" :color="$page->status === 'active' ? 'green' : 'gray'"/>
                        </button>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $page->updated_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.cms.pages.show', $page) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">View</a>
                            <a href="{{ route('admin.cms.pages.edit', $page) }}" class="text-orange-600 hover:text-orange-800 text-xs font-medium">Edit</a>
                            <button wire:click="delete({{ $page->id }})" wire:confirm="Delete this page?" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">No pages found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->pages->links() }}</div>
    </div>
</div>



