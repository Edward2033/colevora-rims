<?php

use App\Models\User;
use function Livewire\Volt\{state, computed, layout};

layout('components.layouts.admin');

state(['search' => '', 'userType' => '', 'perPage' => 15]);

$users = computed(function () {
    return User::query()
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%"))
        ->when($this->userType, fn($q) => $q->where('user_type', $this->userType))
        ->with('roles')
        ->latest()
        ->paginate($this->perPage);
});

$deleteUser = function ($userId) {
    $user = User::find($userId);
    if ($user && $user->id !== auth()->id()) {
        $user->delete();
        session()->flash('success', 'User deleted.');
    }
};

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Users Management"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Users']]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.users.create') }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add User
                </x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="glass-card rounded-xl border border-white/[0.08] p-4 mb-5">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search by name or email..."
                        class="w-full px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
                <select wire:model.live="userType" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="" class="bg-gray-900">All Types</option>
                    <option value="admin" class="bg-gray-900">Admin</option>
                    <option value="employee" class="bg-gray-900">Employee</option>
                    <option value="customer" class="bg-gray-900">Customer</option>
                </select>
                <select wire:model.live="perPage" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="10" class="bg-gray-900">10 / page</option>
                    <option value="15" class="bg-gray-900">15 / page</option>
                    <option value="25" class="bg-gray-900">25 / page</option>
                    <option value="50" class="bg-gray-900">50 / page</option>
                </select>
            </div>
        </div>

        <x-admin.table :headers="['#','User','Email','Type','Role','Phone','Joined','Actions']">
            @forelse($this->users as $user)
                @php
                    $typeColors = ['admin'=>'purple','employee'=>'blue','customer'=>'green'];
                @endphp
                <tr class="hover:bg-amber-500/5 transition" wire:key="user-{{ $user->id }}">
                    <td class="px-4 py-3 text-xs text-gray-400 font-mono">#{{ $user->id }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-white">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-300">{{ $user->email }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :label="ucfirst($user->user_type)" :color="$typeColors[$user->user_type] ?? 'gray'"/>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $user->roles->pluck('name')->join(', ') ?: '—' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-400">{{ $user->phone ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-400 hover:text-blue-300 text-xs font-medium">View</a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-amber-400 hover:text-amber-300 text-xs font-medium">Edit</a>
                            @if($user->id !== auth()->id())
                                <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Delete this user?" class="text-red-400 hover:text-red-300 text-xs font-medium">Delete</button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400 text-sm">No users found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->users->links() }}</div>
    </div>
</div>
