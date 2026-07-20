@props([
    'title' => '',
    'value' => '',
    'sub'   => '',
    'icon'  => '',
    'color' => 'orange',
    'trend' => null,
])

@php
    $colors = [
        'orange' => ['bg' => 'bg-orange-50', 'icon' => 'bg-orange-100 text-orange-600', 'text' => 'text-orange-600'],
        'blue'   => ['bg' => 'bg-blue-50',   'icon' => 'bg-blue-100 text-blue-600',     'text' => 'text-blue-600'],
        'green'  => ['bg' => 'bg-green-50',  'icon' => 'bg-green-100 text-green-600',   'text' => 'text-green-600'],
        'red'    => ['bg' => 'bg-red-50',     'icon' => 'bg-red-100 text-red-600',       'text' => 'text-red-600'],
        'purple' => ['bg' => 'bg-purple-50', 'icon' => 'bg-purple-100 text-purple-600', 'text' => 'text-purple-600'],
        'yellow' => ['bg' => 'bg-yellow-50', 'icon' => 'bg-yellow-100 text-yellow-600', 'text' => 'text-yellow-600'],
        'indigo' => ['bg' => 'bg-indigo-50', 'icon' => 'bg-indigo-100 text-indigo-600', 'text' => 'text-indigo-600'],
    ];
    $c = $colors[$color] ?? $colors['orange'];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
    @if($icon)
        <div class="flex-shrink-0 w-12 h-12 rounded-xl {{ $c['icon'] }} flex items-center justify-center">
            {!! $icon !!}
        </div>
    @endif
    <div class="flex-1 min-w-0">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide truncate">{{ $title }}</p>
        <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $value }}</p>
        @if($sub)
            <p class="text-xs text-gray-500 mt-0.5">{{ $sub }}</p>
        @endif
    </div>
    @if($trend !== null)
        <div class="flex-shrink-0 text-xs font-semibold {{ $trend >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ $trend >= 0 ? '↑' : '↓' }} {{ abs($trend) }}%
        </div>
    @endif
</div>
