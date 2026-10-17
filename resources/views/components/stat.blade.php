@props([
    'label' => null,
    'value' => null,
    'delta' => null,
    'trend' => null,
    'variant' => 'panel',
    'tone' => 'default',
])

@php
// panel: boxed figure (card-like), label above value. inline: borderless
// hero-meta figure, value above label. The inverse tone re-inks the chrome for
// a dark surface; trend colours stay semantic in both tones.
$inline = $variant === 'inline';

$prefix = match ($trend) {
    'up' => '▲',
    'down' => '▼',
    default => null,
};
@endphp

<div data-sigta="stat" data-tone="{{ $tone }}" {{ $attributes->merge(['data-variant' => $variant]) }}>
    @if ($inline)
        @if ($value !== null)
            <span data-scope="stat" data-part="value">{{ $value }}</span>
        @endif
        @if ($label)
            <span data-scope="stat" data-part="label">{{ $label }}</span>
        @endif
    @else
        @if ($label)
            <span data-scope="stat" data-part="label">{{ $label }}</span>
        @endif
        @if ($value !== null)
            <span data-scope="stat" data-part="value">{{ $value }}</span>
        @endif
    @endif
    @if ($delta !== null)
        <span data-scope="stat" data-part="delta" @if ($trend !== null) data-trend="{{ $trend }}" @endif>@if ($prefix)<span aria-hidden="true">{{ $prefix }}</span> @endif{{ $delta }}</span>
    @endif
    @if ($slot->isNotEmpty())
        <div data-scope="stat" data-part="content">{{ $slot }}</div>
    @endif
</div>
