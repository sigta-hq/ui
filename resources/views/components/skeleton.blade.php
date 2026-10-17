@props([
    'variant' => 'line',
])

<div data-sigta="skeleton" aria-hidden="true" {{ $attributes->merge(['data-variant' => $variant]) }}></div>
