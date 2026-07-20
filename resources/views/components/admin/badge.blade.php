@props([
    'label' => '',
    'color' => 'gray',
])

@php
    $colors = [
        'gray'   => 'bg-gray-500/20 text-gray-300 border border-gray-500/30',
        'green'  => 'bg-green-500/20 text-green-300 border border-green-500/30',
        'red'    => 'bg-red-500/20 text-red-300 border border-red-500/30',
        'yellow' => 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30',
        'blue'   => 'bg-blue-500/20 text-blue-300 border border-blue-500/30',
        'purple' => 'bg-purple-500/20 text-purple-300 border border-purple-500/30',
        'orange' => 'bg-orange-500/20 text-orange-300 border border-orange-500/30',
        'indigo' => 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30',
    ];
    $cls = $colors[$color] ?? $colors['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold $cls"]) }}>
    {{ $label ?: $slot }}
</span>
