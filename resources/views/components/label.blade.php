{{-- Mono uppercase form label. variant="inverse" re-colors it for a dark
     surface (inside <sigta:panel variant="inverse">). --}}
@props([
    'variant' => 'default',
])

@php
$tone = match ($variant) {
    'inverse' => 'text-inverse-fg-muted',
    default => 'text-graphite',
};
@endphp

<label data-sigta="label" {{ $attributes->merge(['class' => 'font-mono text-[0.68rem] tracking-[0.08em] uppercase '.$tone]) }}>{{ $slot }}</label>
