@props([
    'variant' => 'body',
])

<p data-sigta="text" {{ $attributes->merge(['data-variant' => $variant]) }}>{{ $slot }}</p>
