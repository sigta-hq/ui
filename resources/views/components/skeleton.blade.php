@props([
    'variant' => 'line',
])

@php
$classes = 'bg-line/60 animate-pulse '.match ($variant) {
    'heading' => 'h-8 w-2/3 rounded-base',
    'block' => 'aspect-video rounded-base',
    'circle' => 'size-10 rounded-full',
    default => 'h-4 rounded-base',
};
@endphp

<div data-sigta="skeleton" aria-hidden="true" {{ $attributes->merge(['class' => $classes]) }}></div>
