<?php

use App\Models\Category;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'perPage' => 15, 'confirmDelete' => null]);

$categories = computed(function () {
    return Category::query()
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
            ->orWhere('description', 'like', "%{$this->search}%"))
        ->withCount('foods')
        ->latest()
        ->paginate($this->perPage);
});

$delete = function (int $id) {
    $cat = Category::find($id);
    if ($cat) {
        $cat->delete();
        $this->confirmDelete = null;
        session()->flash('success', 'Category deleted successfully.');
    }
};

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Categories"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Categories']]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.categories.create') }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Category
                </x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <!-- Filters -->
        <div class="glass-card p-4 mb-5 rounded-xl">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search categories..."
                        class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-900/90 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                </div>
                <select wire:model.live="perPage" class="px-3 py-2 text-sm bg-white dark:bg-gray-900/90 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500">
                    <option value="10">10 / page</option>
                    <option value="15">15 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <x-admin.table :headers="['#','Name','Slug','Foods','Status','Created','Actions']">
            @forelse($this->categories as $cat)
                <tr class="hover:bg-amber-500/5 transition" wire:key="cat-{{ $cat->id }}">
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $cat->id }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($cat->image)
                                <img src="{{ asset('storage/'.$cat->image) }}" class="w-8 h-8 rounded-lg object-cover" alt="">
                            @else
                                <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 font-bold text-xs">
                                    {{ strtoupper(substr($cat->name,0,1)) }}
                                </div>
                            @endif
                            <span class="font-medium text-white text-sm">{{ $cat->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs font-mono">{{ $cat->slug }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :label="$cat->foods_count" color="blue"/>
                    </td>
                    <td class="px-4 py-3">
                        <x-admin.badge
                            :label="ucfirst($cat->status ?? 'active')"
                            :color="($cat->status ?? 'active') === 'active' ? 'green' : 'gray'"
                        />
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $cat->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.categories.show', $cat) }}" class="text-blue-400 hover:text-blue-300 text-xs font-medium">View</a>
                            <a href="{{ route('admin.categories.edit', $cat) }}" class="text-amber-400 hover:text-amber-300 text-xs font-medium">Edit</a>
                            <button wire:click="delete({{ $cat->id }})" wire:confirm="Delete this category?" class="text-red-400 hover:text-red-300 text-xs font-medium">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        No categories found
                    </td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->categories->links() }}</div>
    </div>
</div>



