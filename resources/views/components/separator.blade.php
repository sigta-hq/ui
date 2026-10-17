@props([
    'variant' => 'solid',
])

<hr data-sigta="separator" {{ $attributes->merge(['data-variant' => $variant]) }}>
