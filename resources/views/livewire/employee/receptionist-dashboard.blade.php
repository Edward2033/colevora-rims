<?php

use App\Models\Reservation;
use App\Models\RestaurantTable;
use function Livewire\Volt\{state, mount, layout};

layout('components.layouts.employee');

state([
    'pendingReservations'   => [],
    'confirmedReservations' => [],
    'todayReservations'     => [],
    'availableTables'       => 0,
    'totalToday'            => 0,
]);

mount(function () { $this->loadData(); });

$loadData = function () {
    $this->pendingReservations = Reservation::with(['table', 'user'])
        ->where('status', 'pending')
        ->where('date', '>=', today())
        ->orderBy('date')->orderBy('time')
        ->get();

    $this->confirmedReservations = Reservation::with(['table', 'user'])
        ->where('status', 'confirmed')
        ->where('date', '>=', today())
        ->orderBy('date')->orderBy('time')
        ->limit(20)
        ->get();

    $this->todayReservations = Reservation::with(['table', 'user'])
        ->whereDate('date', today())
        ->whereIn('status', ['pending', 'confirmed'])
        ->orderBy('time')
        ->get();

    $this->availableTables = RestaurantTable::where('status', 'available')->count();
    $this->totalToday      = Reservation::whereDate('date', today())->count();
};

$confirmReservation = function (int $id) {
    $r = Reservation::find($id);
    if ($r && $r->status === 'pending') {
        $r->update(['status' => 'confirmed']);
        $this->loadData();
        session()->flash('success', 'Reservation confirmed.');
    }
};

$cancelReservation = function (int $id) {
    $r = Reservation::find($id);
    if ($r && in_array($r->status, ['pending', 'confirmed'])) {
        $r->update(['status' => 'cancelled']);
        $this->loadData();
        session()->flash('success', 'Reservation cancelled.');
    }
};

?>

<div class="space-y-6" wire:poll.15s="loadData">

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/20 text-green-400 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white">Receptionist Dashboard</h1>
            <p class="text-sm text-gray-400 mt-0.5">Manage reservations and guest arrivals</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-green-400 bg-green-400/10 border border-green-400/20 rounded-full px-4 py-2 w-fit">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            Live · refreshes every 15s
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 bg-yellow-500/10 border border-yellow-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-yellow-400 uppercase tracking-wider">Pending</p>
                <div class="h-8 w-8 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ count($pendingReservations) }}</p>
            <p class="text-xs text-yellow-400/70 mt-1">Awaiting confirmation</p>
        </div>

        <div class="rounded-2xl p-5 bg-green-500/10 border border-green-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-green-400 uppercase tracking-wider">Confirmed</p>
                <div class="h-8 w-8 rounded-lg bg-green-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ count($confirmedReservations) }}</p>
            <p class="text-xs text-green-400/70 mt-1">Upcoming confirmed</p>
        </div>

        <div class="rounded-2xl p-5 bg-blue-500/10 border border-blue-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Today</p>
                <div class="h-8 w-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $totalToday }}</p>
            <p class="text-xs text-blue-400/70 mt-1">Total today</p>
        </div>

        <div class="rounded-2xl p-5 bg-purple-500/10 border border-purple-500/20">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider">Available Tables</p>
                <div class="h-8 w-8 rounded-lg bg-purple-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $availableTables }}</p>
            <p class="text-xs text-purple-400/70 mt-1">Free right now</p>
        </div>
    </div>

    {{-- Today's Schedule --}}
    @if(count($todayReservations) > 0)
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="w-3 h-3 rounded-full bg-blue-400 animate-pulse flex-shrink-0"></span>
            <h2 class="text-base font-bold text-white">Today's Reservations</h2>
            <span class="bg-blue-400/20 text-blue-400 text-xs font-bold px-2.5 py-0.5 rounded-full border border-blue-400/30">{{ count($todayReservations) }}</span>
        </div>
        <div class="rounded-2xl overflow-hidden bg-white/[0.04] border border-white/[0.08]">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white/[0.04] border-b border-white/[0.07]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Guest</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Time</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Guests</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Table</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($todayReservations as $r)
                            <tr wire:key="today-{{ $r->id }}" class="hover:bg-white/[0.03] transition">
                                <td class="px-4 py-3 font-medium text-white">{{ $r->user->name ?? $r->name ?? 'Guest' }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ \Carbon\Carbon::parse($r->time)->format('g:i A') }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ $r->guests ?? '—' }} pax</td>
                                <td class="px-4 py-3 text-gray-300">{{ $r->table?->table_number ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $r->status === 'confirmed' ? 'bg-green-400/20 text-green-400 border border-green-400/30' : 'bg-yellow-400/20 text-yellow-400 border border-yellow-400/30' }}">
                                        {{ ucfirst($r->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Pending Reservations --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="w-3 h-3 rounded-full bg-yellow-400 flex-shrink-0"></span>
            <h2 class="text-base font-bold text-white">Pending Reservations</h2>
            @if(count($pendingReservations) > 0)
                <span class="bg-yellow-400/20 text-yellow-400 text-xs font-bold px-2.5 py-0.5 rounded-full border border-yellow-400/30">{{ count($pendingReservations) }}</span>
            @endif
        </div>

        @if(count($pendingReservations) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($pendingReservations as $r)
                    <div class="rounded-2xl p-5 bg-white/[0.04] border border-white/[0.07] border-l-4 border-l-yellow-400" wire:key="pend-{{ $r->id }}">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-bold text-white text-sm">{{ $r->user->name ?? $r->name ?? 'Guest' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($r->date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($r->time)->format('g:i A') }}</p>
                            </div>
                            <span class="bg-yellow-400/20 text-yellow-400 text-xs font-semibold px-2.5 py-1 rounded-full border border-yellow-400/30">PENDING</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-white/5 text-gray-300 text-xs px-2 py-1 rounded-lg">{{ $r->guests ?? '?' }} guests</span>
                            @if($r->table)
                                <span class="bg-amber-500/20 text-amber-400 text-xs px-2 py-1 rounded-lg">Table {{ $r->table->table_number }}</span>
                            @endif
                            @if($r->notes)
                                <span class="bg-blue-500/20 text-blue-400 text-xs px-2 py-1 rounded-lg">Has notes</span>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="confirmReservation({{ $r->id }})"
                                class="flex-1 bg-green-600 hover:bg-green-500 text-white text-xs font-semibold py-2 rounded-xl transition">
                                Confirm
                            </button>
                            <button wire:click="cancelReservation({{ $r->id }})"
                                class="flex-1 bg-red-600 hover:bg-red-500 text-white text-xs font-semibold py-2 rounded-xl transition">
                                Cancel
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl p-10 text-center bg-white/[0.03] border border-white/[0.07]">
                <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-gray-500 text-sm">No pending reservations</p>
            </div>
        @endif
    </div>

    {{-- Upcoming Confirmed --}}
    @if(count($confirmedReservations) > 0)
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="w-3 h-3 rounded-full bg-green-400 flex-shrink-0"></span>
            <h2 class="text-base font-bold text-white">Upcoming Confirmed Reservations</h2>
            <span class="bg-green-400/20 text-green-400 text-xs font-bold px-2.5 py-0.5 rounded-full border border-green-400/30">{{ count($confirmedReservations) }}</span>
        </div>
        <div class="rounded-2xl overflow-hidden bg-white/[0.04] border border-white/[0.08]">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white/[0.04] border-b border-white/[0.07]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Guest</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Time</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Guests</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Table</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($confirmedReservations as $r)
                            <tr wire:key="conf-{{ $r->id }}" class="hover:bg-white/[0.03] transition">
                                <td class="px-4 py-3 font-medium text-white">{{ $r->user->name ?? $r->name ?? 'Guest' }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ \Carbon\Carbon::parse($r->date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ \Carbon\Carbon::parse($r->time)->format('g:i A') }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ $r->guests ?? '—' }} pax</td>
                                <td class="px-4 py-3 text-gray-300">{{ $r->table?->table_number ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <button wire:click="cancelReservation({{ $r->id }})"
                                        class="text-red-400 hover:text-red-300 text-xs font-semibold transition">Cancel</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
