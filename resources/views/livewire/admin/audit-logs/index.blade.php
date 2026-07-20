<?php

use App\Models\AuditLog;
use App\Models\User;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state(['search' => '', 'userFilter' => '', 'actionFilter' => '', 'dateFrom' => '', 'dateTo' => '', 'perPage' => 20]);

$logs = computed(function () {
    return AuditLog::query()
        ->with('user')
        ->when($this->search, fn($q) => $q->where('description', 'like', "%{$this->search}%")
            ->orWhere('action', 'like', "%{$this->search}%"))
        ->when($this->userFilter, fn($q) => $q->where('user_id', $this->userFilter))
        ->when($this->actionFilter, fn($q) => $q->where('action', $this->actionFilter))
        ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
        ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
        ->latest()
        ->paginate($this->perPage);
});

$users = computed(fn() => User::orderBy('name')->get(['id', 'name']));

$actions = computed(fn() => AuditLog::distinct()->pluck('action')->sort()->values());

?>

<div>
    <div class="p-6">
        <x-admin.page-header
            title="Audit Logs"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Audit Logs']]"
        />

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-5">
            <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
                <div class="flex-1 min-w-48">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search logs..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
                <select wire:model.live="userFilter" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">All Users</option>
                    @foreach($this->users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="actionFilter" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">All Actions</option>
                    @foreach($this->actions as $action)
                        <option value="{{ $action }}">{{ ucfirst($action) }}</option>
                    @endforeach
                </select>
                <input type="date" wire:model.live="dateFrom" placeholder="From"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                <input type="date" wire:model.live="dateTo" placeholder="To"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
        </div>

        <x-admin.table :headers="['#','User','Action','Description','IP Address','Date']">
            @forelse($this->logs as $log)
                @php
                    $actionColors = [
                        'create' => 'green', 'created' => 'green',
                        'update' => 'blue', 'updated' => 'blue',
                        'delete' => 'red', 'deleted' => 'red',
                        'login' => 'purple', 'logout' => 'gray',
                    ];
                    $color = $actionColors[strtolower($log->action)] ?? 'gray';
                @endphp
                <tr class="hover:bg-gray-50 transition" wire:key="log-{{ $log->id }}">
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $log->id }}</td>
                    <td class="px-4 py-3">
                        <p class="text-sm font-medium text-gray-900">{{ $log->user?->name ?? 'System' }}</p>
                        <p class="text-xs text-gray-400">{{ $log->user?->email ?? '' }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <x-admin.badge :label="ucfirst($log->action)" :color="$color"/>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $log->description }}</td>
                    <td class="px-4 py-3 text-xs font-mono text-gray-500">{{ $log->ip_address ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $log->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">No audit logs found</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">{{ $this->logs->links() }}</div>
    </div>
</div>



