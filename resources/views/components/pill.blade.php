@props([
    'variant' => 'neutral',
])

<span data-sigta="pill" {{ $attributes->merge(['data-variant' => $variant]) }}>
    <span data-scope="pill" data-part="dot" aria-hidden="true"></span>{{ $slot }}
</span>
