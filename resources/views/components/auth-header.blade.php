@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-3 text-center mb-6">
    <h1 class="text-3xl font-bold text-white gold-glow">{{ $title }}</h1>
    <p class="text-sm text-gray-400">{{ $description }}</p>
</div>
