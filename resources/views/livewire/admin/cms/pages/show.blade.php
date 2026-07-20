<?php
use function Livewire\Volt\{layout};
layout('components.layouts.admin');
?>

@php
    $id = $id ?? request()->route('id');
    $page = \App\Models\Page::findOrFail($id);
@endphp

<div>
    <div class="p-6 max-w-3xl">
        <x-admin.page-header
            title="Page Details"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Pages','url'=>route('admin.cms.pages.index')],['label'=>$page->title]]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.cms.pages.edit', $page) }}" variant="secondary">Edit</x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $page->title }}</h2>
                        <p class="text-xs font-mono text-gray-400 mt-0.5">/{{ $page->slug }}</p>
                    </div>
                    <x-admin.badge :label="ucfirst($page->status)" :color="$page->status === 'active' ? 'green' : 'gray'"/>
                </div>
                <div class="text-xs text-gray-400">
                    Last updated: {{ $page->updated_at->format('M d, Y H:i') }}
                </div>
            </div>

            @if($page->content)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Content Preview</h3>
                    <div class="prose prose-sm max-w-none text-gray-700 border border-gray-100 rounded-lg p-4 bg-gray-50">
                        {!! $page->content !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
