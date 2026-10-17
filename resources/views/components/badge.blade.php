@props([
    'variant' => 'outline',
])

<span data-sigta="badge" {{ $attributes->merge(['data-variant' => $variant]) }}>{{ $slot }}</span>
