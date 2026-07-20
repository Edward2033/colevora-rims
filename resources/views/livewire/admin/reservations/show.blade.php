<?php

use App\Models\Reservation;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['res' => null]);

mount(function (int $id) {
    $this->res = Reservation::with(['user', 'table'])->findOrFail($id);
});

?>

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Reservation Details"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Reservations','url'=>route('admin.reservations.index')],['label'=>'#'.$res->id]]"
        >
            <x-slot:actions>
                <x-admin.btn href="{{ route('admin.reservations.edit', $res) }}" variant="secondary">Edit</x-admin.btn>
            </x-slot:actions>
        </x-admin.page-header>

        @php $statusColors = ['pending'=>'yellow','confirmed'=>'green','cancelled'=>'red','completed'=>'blue']; @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Reservation #{{ $res->id }}</h3>
                <x-admin.badge :label="ucfirst($res->status)" :color="$statusColors[$res->status] ?? 'gray'"/>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs text-gray-500">Guest Name</dt>
                    <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $res->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Email</dt>
                    <dd class="text-sm text-gray-700 mt-0.5">{{ $res->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Phone</dt>
                    <dd class="text-sm text-gray-700 mt-0.5">{{ $res->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Registered User</dt>
                    <dd class="text-sm text-gray-700 mt-0.5">{{ $res->user?->name ?? 'Guest' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Date</dt>
                    <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $res->date->format('M d, Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Time</dt>
                    <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $res->time }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Number of Guests</dt>
                    <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $res->guests }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Assigned Table</dt>
                    <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $res->table?->table_number ?? 'Not assigned' }}</dd>
                </div>
                @if($res->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-gray-500">Notes</dt>
                        <dd class="text-sm text-gray-700 mt-0.5">{{ $res->notes }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-xs text-gray-500">Created</dt>
                    <dd class="text-sm text-gray-700 mt-0.5">{{ $res->created_at->format('M d, Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>



