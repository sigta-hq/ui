@props([
    'heading' => null,
    'meta' => null,
    'variant' => 'default',
])

@php
$dot = 'absolute -left-[5px] top-[3px] size-[9px] rounded-full '.match ($variant) {
    'accent' => 'border border-ink bg-accent',
    default => 'bg-ink',
};
@endphp

<li data-sigta="timeline.item" {{ $attributes->merge(['class' => 'relative border-l border-line pl-6 pb-8 last:pb-1']) }}>
    <span aria-hidden="true" class="{{ $dot }}"></span>

    @if ($meta)
        <p class="m-0 font-mono text-[0.68rem] tracking-[0.08em] uppercase text-graphite">{{ $meta }}</p>
    @endif

    @if ($heading)
        <h3 class="m-0 {{ $meta ? 'mt-1' : '' }} font-head text-[1.1rem] font-medium leading-snug text-ink">{{ $heading }}</h3>
    @endif

    @if ($slot->isNotEmpty())
        <div class="{{ ($heading || $meta) ? 'mt-1.5' : '' }} font-body text-[0.95rem] leading-relaxed text-graphite">{{ $slot }}</div>
    @endif
</li>
