<?php

use App\Models\Reservation;
use App\Models\RestaurantTable;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['res' => null, 'status' => '', 'table_id' => '', 'notes' => '']);

$tables = computed(fn() => RestaurantTable::available()->orderBy('table_number')->get());

mount(function (int $id) {
    $this->res = Reservation::with(['user', 'table'])->findOrFail($id);
    $this->status = $this->res->status;
    $this->table_id = $this->res->table_id ?? '';
    $this->notes = $this->res->notes ?? '';
});

$save = function () {
    $this->validate([
        'status'   => 'required|in:pending,confirmed,cancelled,completed',
        'table_id' => 'nullable|exists:restaurant_tables,id',
        'notes'    => 'nullable|string|max:500',
    ]);

    $this->res->update([
        'status'   => $this->status,
        'table_id' => $this->table_id ?: null,
        'notes'    => $this->notes,
    ]);
    $this->res->refresh();

    session()->flash('success', 'Reservation updated.');
};

?>

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Edit Reservation"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Reservations','url'=>route('admin.reservations.index')],['label'=>'Edit']]"
        />

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="mb-5 pb-5 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Reservation Info</h3>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-xs text-gray-500">Guest</dt><dd class="font-medium text-gray-900">{{ $res->name }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Phone</dt><dd class="text-gray-700">{{ $res->phone ?? '—' }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Date</dt><dd class="font-medium text-gray-900">{{ $res->date->format('M d, Y') }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Time</dt><dd class="text-gray-700">{{ $res->time }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Guests</dt><dd class="text-gray-700">{{ $res->guests }}</dd></div>
                </dl>
            </div>

            <form wire:submit="save" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Status</label>
                    <select wire:model="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Assign Table</label>
                    <select wire:model="table_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">No table assigned</option>
                        @if($res->table)
                            <option value="{{ $res->table->id }}" selected>{{ $res->table->table_number }} (current)</option>
                        @endif
                        @foreach($this->tables as $t)
                            @if($t->id !== $res->table_id)
                                <option value="{{ $t->id }}">{{ $t->table_number }} ({{ $t->capacity }} seats)</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Notes</label>
                    <textarea wire:model="notes" rows="3"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"></textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.btn type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Save Changes</span>
                        <span wire:loading>Saving...</span>
                    </x-admin.btn>
                    <x-admin.btn href="{{ route('admin.reservations.index') }}" variant="secondary">Back</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>



