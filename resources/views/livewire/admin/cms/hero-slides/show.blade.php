<?php
use function Livewire\Volt\{layout};
layout('components.layouts.admin');
?>

@php
    $id = $id ?? request()->route('id');
    $slide = \App\Models\HeroSlide::findOrFail($id);
@endphp

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Hero Slide Details"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Hero Slides','url'=>route('admin.cms.hero-slides.index')],['label'=>$slide->title]]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.cms.hero-slides.edit', $slide) }}" variant="secondary">Edit</x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">{{ $slide->title }}</h2>
                <x-admin.badge :label="ucfirst($slide->status)" :color="$slide->status === 'active' ? 'green' : 'gray'"/>
            </div>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-xs text-gray-500">Subtitle</dt>
                    <dd class="text-gray-700 mt-0.5">{{ $slide->subtitle ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Order</dt>
                    <dd class="font-medium text-gray-900 mt-0.5">{{ $slide->ordering }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Button Text</dt>
                    <dd class="text-gray-700 mt-0.5">{{ $slide->button_text ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Button Link</dt>
                    <dd class="text-gray-700 mt-0.5 font-mono text-xs">{{ $slide->button_link ?? '—' }}</dd>
                </div>
                @if($slide->image)
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-gray-500 mb-2">Slide Image</dt>
                        <dd class="mt-2">
                            <img src="{{ asset('storage/' . $slide->image) }}" 
                                 alt="{{ $slide->title }}" 
                                 class="w-full max-w-2xl h-64 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-2 font-mono">{{ $slide->image }}</p>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>
</div>
