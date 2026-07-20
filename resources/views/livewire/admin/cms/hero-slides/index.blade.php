<?php

use App\Models\HeroSlide;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'perPage' => 15]);

$slides = computed(function () {
    return HeroSlide::query()
        ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
        ->orderBy('ordering')
        ->paginate($this->perPage);
});

$delete = function (int $id) {
    HeroSlide::findOrFail($id)->delete();
    session()->flash('success', 'Hero slide deleted.');
};

$toggleStatus = function (int $id) {
    $slide = HeroSlide::findOrFail($id);
    $slide->update(['status' => $slide->status === 'active' ? 'inactive' : 'active']);
};

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Hero Slides"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'CMS'],['label'=>'Hero Slides']]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.cms.hero-slides.create') }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Slide
                </x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-5">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Search slides..."
                class="w-full sm:w-80 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
        </div>

        <x-admin.table :headers="['Order','Title','Subtitle','Button','Status','Actions']">
            @forelse($this->slides as $slide)
                <tr class="hover:bg-gray-50 transition" wire:key="slide-{{ $slide->id }}">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $slide->ordering }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($slide->image)
                                <img src="{{ asset('storage/' . $slide->image) }}" 
                                     alt="{{ $slide->title }}" 
                                     class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                            @else
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $slide->title }}</p>
                                @if($slide->image)
                                    <p class="text-xs text-gray-400 truncate max-w-xs">{{ basename($slide->image) }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ Str::limit($slide->subtitle ?? '—', 50) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $slide->button_text ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <button wire:click="toggleStatus({{ $slide->id }})" class="focus:outline-none">
                            <x-admin.badge :label="ucfirst($slide->status)" :color="$slide->status === 'active' ? 'green' : 'gray'"/>
                        </button>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.cms.hero-slides.edit', $slide) }}" class="text-orange-600 hover:text-orange-800 text-xs font-medium">Edit</a>
                            <button wire:click="delete({{ $slide->id }})" wire:confirm="Delete this slide?" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">No hero slides found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->slides->links() }}</div>
    </div>
</div>



