<?php

use App\Models\Reservation;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'statusFilter' => '', 'dateFilter' => '', 'perPage' => 15]);

$reservations = computed(function () {
    return Reservation::query()
        ->with(['user', 'table'])
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->orWhere('phone', 'like', "%{$this->search}%"))
        ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
        ->when($this->dateFilter, fn($q) => $q->whereDate('date', $this->dateFilter))
        ->latest()
        ->paginate($this->perPage);
});

$updateStatus = function (int $id, string $status) {
    Reservation::findOrFail($id)->update(['status' => $status]);
    session()->flash('success', 'Reservation status updated.');
};

$delete = function (int $id) {
    Reservation::findOrFail($id)->delete();
    session()->flash('success', 'Reservation deleted.');
};

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Reservations"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Reservations']]"
        />

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 text-green-400 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="glass-card rounded-xl border border-white/[0.08] p-4 mb-5">
            <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
                <div class="flex-1 min-w-48">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search by name, email or phone..."
                        class="w-full px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
                <select wire:model.live="statusFilter" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="" class="bg-gray-900">All Status</option>
                    <option value="pending" class="bg-gray-900">Pending</option>
                    <option value="confirmed" class="bg-gray-900">Confirmed</option>
                    <option value="cancelled" class="bg-gray-900">Cancelled</option>
                    <option value="completed" class="bg-gray-900">Completed</option>
                </select>
                <input type="date" wire:model.live="dateFilter"
                    class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                <select wire:model.live="perPage" class="px-3 py-2 text-sm border border-white/10 bg-white/5 text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-500">
                    <option value="10" class="bg-gray-900">10 / page</option>
                    <option value="15" class="bg-gray-900">15 / page</option>
                    <option value="25" class="bg-gray-900">25 / page</option>
                </select>
            </div>
        </div>

        <x-admin.table :headers="['#','Guest','Contact','Date & Time','Guests','Table','Status','Actions']">
            @forelse($this->reservations as $res)
                @php $statusColors = ['pending'=>'yellow','confirmed'=>'green','cancelled'=>'red','completed'=>'blue']; @endphp
                <tr class="hover:bg-amber-500/5 transition" wire:key="res-{{ $res->id }}">
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $res->id }}</td>
                    <td class="px-4 py-3">
                        <p class="text-sm font-medium text-white">{{ $res->name }}</p>
                        @if($res->user)
                            <p class="text-xs text-gray-400">Registered user</p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-xs text-gray-300">{{ $res->phone ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $res->email ?? '—' }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-sm font-medium text-white">{{ $res->date->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-400">{{ $res->time }}</p>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-300">{{ $res->guests }}</td>
                    <td class="px-4 py-3 text-sm text-gray-300">{{ $res->table?->table_number ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :label="ucfirst($res->status)" :color="$statusColors[$res->status] ?? 'gray'"/>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.reservations.show', $res) }}" class="text-blue-400 hover:text-blue-300 text-xs font-medium">View</a>
                            @if($res->status === 'pending')
                                <button wire:click="updateStatus({{ $res->id }}, 'confirmed')" class="text-green-400 hover:text-green-300 text-xs font-medium">Confirm</button>
                                <button wire:click="updateStatus({{ $res->id }}, 'cancelled')" wire:confirm="Cancel this reservation?" class="text-red-400 hover:text-red-300 text-xs font-medium">Cancel</button>
                            @endif
                            <button wire:click="delete({{ $res->id }})" wire:confirm="Delete this reservation?" class="text-red-400 hover:text-red-300 text-xs font-medium">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400 text-sm">No reservations found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->reservations->links() }}</div>
    </div>
</div>



