@props([
    'label' => null,
    'value' => null,
    'delta' => null,
    'trend' => null,
    'variant' => 'panel',
    'tone' => 'default',
])

@php
$inline = $variant === 'inline';
$inverse = $tone === 'inverse';

// Tone-aware text roles. inverse re-colors the graphite/ink chrome for a dark
// surface; trend colors (success/danger) stay semantic in both tones.
$mutedText = $inverse ? 'text-inverse-fg-muted' : 'text-graphite';
$valueText = $inverse ? 'text-inverse-fg' : 'text-ink';

$deltaClasses = 'font-mono text-[0.72rem] mt-[0.2rem] '.match ($trend) {
    'up' => 'text-success',
    'down' => 'text-danger',
    default => $mutedText,
};

$prefix = match ($trend) {
    'up' => '▲',
    'down' => '▼',
    default => null,
};

// panel: boxed figure (card-like). inline: borderless hero-meta figure (brand
// .hero-meta b). inverse tone darkens the boxed surface; inline stays bare so
// it composes on top of an already-dark <sigta:panel variant="inverse">.
$surface = $inverse ? 'bg-inverse border border-inverse-line' : 'bg-surface border border-line';
$rootClasses = $inline
    ? 'flex flex-col'
    : 'flex flex-col rounded-panel p-[18px] '.$surface;
@endphp

<div data-sigta="stat" data-tone="{{ $tone }}" {{ $attributes->merge(['class' => $rootClasses]) }}>
    @if ($inline)
        @if ($value !== null)
            <span class="font-head font-medium text-[1.5rem] leading-[1.1] {{ $valueText }} tabular-nums">{{ $value }}</span>
        @endif
        @if ($label)
            <span class="font-mono text-[0.72rem] tracking-[0.08em] uppercase {{ $mutedText }} mt-[0.2rem]">{{ $label }}</span>
        @endif
    @else
        @if ($label)
            <span class="font-mono text-[0.66rem] tracking-[0.08em] uppercase {{ $mutedText }}">{{ $label }}</span>
        @endif
        @if ($value !== null)
            <span class="font-head font-medium text-[1.9rem] leading-[1.1] mt-1 tabular-nums {{ $valueText }}">{{ $value }}</span>
        @endif
    @endif
    @if ($delta !== null)
        <span class="{{ $deltaClasses }}">@if ($prefix)<span aria-hidden="true">{{ $prefix }}</span> @endif{{ $delta }}</span>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-2 {{ $valueText }}">{{ $slot }}</div>
    @endif
</div>
