{{-- Mono uppercase form label. variant="inverse" re-colors it for a dark
     surface (inside <sigta:panel variant="inverse">). --}}
@props([
    'variant' => 'default',
])

<label data-sigta="label" {{ $attributes->merge(['data-variant' => $variant]) }}>{{ $slot }}</label>
