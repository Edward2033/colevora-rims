@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center font-medium rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed';
    $sizes = ['sm' => 'px-3 py-1.5 text-xs', 'md' => 'px-4 py-2 text-sm', 'lg' => 'px-5 py-2.5 text-base'];
    $variants = [
        'primary'   => 'bg-orange-600 hover:bg-orange-700 dark:bg-gold-600 dark:hover:bg-gold-700 text-white focus:ring-orange-500 dark:focus:ring-gold-500',
        'secondary' => 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 focus:ring-gray-400',
        'danger'    => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
        'success'   => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
        'warning'   => 'bg-yellow-500 hover:bg-yellow-600 text-white focus:ring-yellow-400',
        'ghost'     => 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 focus:ring-gray-400',
    ];
    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
