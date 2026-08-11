@props([
    'label' => null,
    'value' => null,
    'delta' => null,
    'trend' => null,
    'variant' => 'panel',
])

@php
$inline = $variant === 'inline';

$deltaClasses = 'font-mono text-[0.72rem] mt-[0.2rem] '.match ($trend) {
    'up' => 'text-success',
    'down' => 'text-danger',
    default => 'text-graphite',
};

$prefix = match ($trend) {
    'up' => '▲',
    'down' => '▼',
    default => null,
};

// panel: boxed figure (card-like). inline: borderless hero-meta figure (brand .hero-meta b).
$rootClasses = $inline
    ? 'flex flex-col'
    : 'flex flex-col bg-surface border border-line rounded-panel p-[18px]';
@endphp

<div data-sigta="stat" {{ $attributes->merge(['class' => $rootClasses]) }}>
    @if ($inline)
        @if ($value !== null)
            <span class="font-head font-medium text-[1.5rem] leading-[1.1] text-ink tabular-nums">{{ $value }}</span>
        @endif
        @if ($label)
            <span class="font-mono text-[0.72rem] tracking-[0.08em] uppercase text-graphite mt-[0.2rem]">{{ $label }}</span>
        @endif
    @else
        @if ($label)
            <span class="font-mono text-[0.66rem] tracking-[0.08em] uppercase text-graphite">{{ $label }}</span>
        @endif
        @if ($value !== null)
            <span class="font-head font-medium text-[1.9rem] leading-[1.1] mt-1 tabular-nums">{{ $value }}</span>
        @endif
    @endif
    @if ($delta !== null)
        <span class="{{ $deltaClasses }}">@if ($prefix)<span aria-hidden="true">{{ $prefix }}</span> @endif{{ $delta }}</span>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-2 text-ink">{{ $slot }}</div>
    @endif
</div>
