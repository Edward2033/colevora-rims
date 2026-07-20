<?php

use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Models\SiteSetting;
use function Livewire\Volt\{state, mount, layout};

layout('components.layouts.public');

state([
    'name'             => '',
    'email'            => '',
    'phone'            => '',
    'date'             => '',
    'time'             => '',
    'guests'           => 2,
    'table_id'         => null,
    'notes'            => '',
    'tables'           => collect([]),
    'submitted'        => false,
]);

mount(function () {
    if (auth()->check()) {
        $this->name  = auth()->user()->name;
        $this->email = auth()->user()->email;
    }
    $this->tables = RestaurantTable::where('status', 'available')->get();
});

$submit = function () {
    $this->validate([
        'name'    => 'required|string|max:255',
        'email'   => 'required|email',
        'phone'   => 'required|string|max:30',
        'date'    => 'required|date|after_or_equal:today',
        'time'    => 'required',
        'guests'  => 'required|integer|min:1|max:20',
        'table_id'=> 'nullable|exists:restaurant_tables,id',
        'notes'   => 'nullable|string|max:500',
    ]);

    Reservation::create([
        'user_id'  => auth()->id(),
        'name'     => $this->name,
        'email'    => $this->email,
        'phone'    => $this->phone,
        'date'     => $this->date,
        'time'     => $this->time,
        'guests'   => $this->guests,
        'table_id' => $this->table_id ?: null,
        'notes'    => $this->notes,
        'status'   => 'pending',
    ]);

    $this->submitted = true;
};

?>

<div class="min-h-screen bg-gray-50">
    {{-- Hero --}}
    <div class="bg-gradient-to-r from-orange-600 to-red-600 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-3">Reserve a Table</h1>
            <p class="text-xl text-orange-100">Book your perfect dining experience</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-16">
        <div class="max-w-2xl mx-auto">

            @if($submitted)
            <div class="bg-white rounded-2xl shadow-xl p-10 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">Reservation Submitted!</h2>
                <p class="text-gray-600 mb-6">Thank you, <strong>{{ $name }}</strong>. We'll confirm your booking for <strong>{{ $date }}</strong> at <strong>{{ $time }}</strong> via email or phone.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('menu') }}" class="bg-orange-600 hover:bg-orange-700 text-white font-medium px-6 py-3 rounded-lg transition">Browse Menu</a>
                    <button wire:click="$set('submitted', false)" class="border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-6 py-3 rounded-lg transition">Make Another</button>
                </div>
            </div>
            @else
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-10">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Make a Reservation</h2>
                    <p class="text-gray-500 text-sm">Fill in your details and we'll confirm your booking</p>
                </div>

                <form wire:submit="submit" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name *</label>
                        <input type="text" wire:model="name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                               placeholder="John Doe">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email *</label>
                            <input type="email" wire:model="email"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                   placeholder="john@example.com">
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone *</label>
                            <input type="tel" wire:model="phone"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                   placeholder="+1 234 567 8900">
                            @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date *</label>
                            <input type="date" wire:model="date" min="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            @error('date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Time *</label>
                            <input type="time" wire:model="time"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            @error('time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Guests *</label>
                            <select wire:model="guests"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                @for($i = 1; $i <= 20; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'Person' : 'People' }}</option>
                                @endfor
                            </select>
                            @error('guests') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Preferred Table</label>
                            <select wire:model="table_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="">Any Available Table</option>
                                @foreach($tables as $table)
                                <option value="{{ $table->id }}">Table {{ $table->table_number }} ({{ $table->capacity }} seats)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Special Requests</label>
                        <textarea wire:model="notes" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                  placeholder="Dietary restrictions, allergies, special occasions..."></textarea>
                    </div>

                    <button type="submit"
                            class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-lg transition text-lg">
                        Reserve Now
                    </button>
                </form>

                <p class="mt-5 text-center text-sm text-gray-400">
                    We'll confirm your reservation within 24 hours via email or phone.
                </p>
            </div>

            {{-- Info Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-8">
                @foreach([
                    ['M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'Opening Hours', SiteSetting::get('opening_hours_mon_fri','11AM–10PM')],
                    ['M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'Call Us', SiteSetting::get('phone','+1 234 567 8900')],
                    ['M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z', 'Location', SiteSetting::get('address','123 Main St')],
                ] as [$path, $title, $value])
                <div class="bg-white rounded-xl p-5 text-center shadow-sm">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">{{ $title }}</h3>
                    <p class="text-xs text-gray-500">{{ $value }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
