@props([
    'title' => '',
    'breadcrumbs' => [],
    'actions' => null,
])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        @if(count($breadcrumbs))
            <nav class="flex items-center gap-1 text-xs text-gray-600 dark:text-gray-300 mb-1">
                @foreach($breadcrumbs as $crumb)
                    @if(!$loop->last)
                        <a href="{{ $crumb['url'] ?? '#' }}" class="hover:text-gold-500 dark:hover:text-gold-400 transition">{{ $crumb['label'] }}</a>
                        <span>/</span>
                    @else
                        <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif
        <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $title }}</h1>
    </div>
    @if($actions)
        <div class="flex items-center gap-2 flex-shrink-0">
            {{ $actions }}
        </div>
    @endif
</div>
