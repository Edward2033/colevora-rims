<?php

use App\Models\Food;
use App\Models\Category;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'categoryFilter' => '', 'statusFilter' => '', 'perPage' => 15]);

$foods = computed(function () {
    return Food::query()
        ->with('category')
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
        ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
        ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
        ->latest()
        ->paginate($this->perPage);
});

$categories = computed(fn() => Category::active()->orderBy('name')->get());

$delete = function (int $id) {
    Food::findOrFail($id)->delete();
    session()->flash('success', 'Food item deleted.');
};

$toggleAvailability = function (int $id) {
    $food = Food::findOrFail($id);
    $food->update(['availability' => !$food->availability]);
};

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Food Items"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Foods']]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.foods.create') }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Food
                </x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <!-- Filters -->
        <div class="glass-card rounded-xl border border-white/[0.08] p-4 mb-5">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search food items..."
                        class="w-full px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
                <select wire:model.live="categoryFilter" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="" class="bg-gray-900">All Categories</option>
                    @foreach($this->categories as $cat)
                        <option value="{{ $cat->id }}" class="bg-gray-900">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="statusFilter" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="" class="bg-gray-900">All Status</option>
                    <option value="active" class="bg-gray-900">Active</option>
                    <option value="inactive" class="bg-gray-900">Inactive</option>
                </select>
                <select wire:model.live="perPage" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="10" class="bg-gray-900">10 / page</option>
                    <option value="15" class="bg-gray-900">15 / page</option>
                    <option value="25" class="bg-gray-900">25 / page</option>
                    <option value="50" class="bg-gray-900">50 / page</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <x-admin.table :headers="['#','Item','Category','Price','Availability','Status','Actions']">
            @forelse($this->foods as $food)
                <tr class="hover:bg-amber-500/5 transition" wire:key="food-{{ $food->id }}">
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $food->id }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($food->image)
                                <img src="{{ asset('storage/'.$food->image) }}" class="w-10 h-10 rounded-lg object-cover" alt="">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 font-bold text-sm">
                                    {{ strtoupper(substr($food->name,0,1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-white text-sm">{{ $food->name }}</p>
                                @if($food->description)
                                    <p class="text-xs text-gray-400 truncate max-w-xs">{{ Str::limit($food->description, 50) }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-300">{{ $food->category?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm">
                        @if($food->discount_price)
                            <span class="font-semibold text-green-400">${{ number_format($food->discount_price, 2) }}</span>
                            <span class="text-xs text-gray-500 line-through ml-1">${{ number_format($food->price, 2) }}</span>
                        @else
                            <span class="font-semibold text-white">${{ number_format($food->price, 2) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="toggleAvailability({{ $food->id }})" class="focus:outline-none">
                            <x-admin.badge :label="$food->availability ? 'Available' : 'Unavailable'" :color="$food->availability ? 'green' : 'red'"/>
                        </button>
                    </td>
                    <td class="px-4 py-3">
                        <x-admin.badge :label="ucfirst($food->status)" :color="$food->status === 'active' ? 'blue' : 'gray'"/>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.foods.show', $food) }}" class="text-blue-400 hover:text-blue-300 text-xs font-medium">View</a>
                            <a href="{{ route('admin.foods.edit', $food) }}" class="text-amber-400 hover:text-amber-300 text-xs font-medium">Edit</a>
                            <button wire:click="delete({{ $food->id }})" wire:confirm="Delete this food item?" class="text-red-400 hover:text-red-300 text-xs font-medium">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">No food items found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->foods->links() }}</div>
    </div>
</div>



