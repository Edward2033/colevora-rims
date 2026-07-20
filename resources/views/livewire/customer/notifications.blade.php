<?php

use function Livewire\Volt\{state, mount, computed, layout};

layout('components.layouts.customer');

state(['notifications' => collect([])]);

mount(function () {
    $this->notifications = auth()->user()->notifications()->paginate(20);
});

$markAsRead = function ($id) {
    auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
    $this->notifications = auth()->user()->notifications()->paginate(20);
};

$markAllAsRead = function () {
    auth()->user()->unreadNotifications->markAsRead();
    $this->notifications = auth()->user()->notifications()->paginate(20);
};

$unreadCount = computed(function () {
    return auth()->user()->unreadNotifications()->count();
});

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Notifications</h1>
            <p class="text-gray-400 mt-1">Stay updated with your orders and activities</p>
        </div>
        @if($this->unreadCount > 0)
        <button wire:click="markAllAsRead"
                class="bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-bold px-5 py-2.5 rounded-xl transition">
            Mark All as Read
        </button>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="glass-card rounded-2xl border border-gold-500/20 overflow-hidden">
        @if($notifications->count() > 0)
        <div class="divide-y divide-white/5">
            @foreach($notifications as $notification)
            <div class="p-5 hover:bg-white/5 transition {{ $notification->read_at ? '' : 'bg-gold-500/5' }}"
                 wire:key="notification-{{ $notification->id }}">
                <div class="flex items-start gap-4">
                    <!-- Icon -->
                    @php $type = $notification->data['type'] ?? 'default'; @endphp
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                        {{ $type === 'order' ? 'bg-blue-500/20' : ($type === 'payment' ? 'bg-green-500/20' : 'bg-gold-500/20') }}">
                        @if($type === 'order')
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @elseif($type === 'payment')
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white">
                            {{ $notification->data['title'] ?? 'Notification' }}
                        </p>
                        <p class="text-sm text-gray-400 mt-0.5">
                            {{ $notification->data['message'] ?? $notification->data['body'] ?? 'No message available' }}
                        </p>
                        <div class="flex items-center gap-4 mt-2">
                            <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                            @if(isset($notification->data['action_url']))
                            <a href="{{ $notification->data['action_url'] }}" class="text-xs text-gold-400 hover:text-gold-300 font-medium">
                                View Details →
                            </a>
                            @endif
                        </div>
                    </div>

                    @if(!$notification->read_at)
                    <button wire:click="markAsRead('{{ $notification->id }}')"
                            class="flex-shrink-0 text-xs text-gold-400 hover:text-gold-300 font-semibold transition">
                        Mark Read
                    </button>
                    @else
                    <span class="flex-shrink-0 w-2 h-2 rounded-full bg-white/10 mt-2"></span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="p-4 border-t border-white/5">
            {{ $notifications->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <h3 class="text-lg font-semibold text-white mb-2">No Notifications</h3>
            <p class="text-gray-400">You're all caught up! We'll notify you when there's something new.</p>
        </div>
        @endif
    </div>
</div>
