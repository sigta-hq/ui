{{-- One cell of a tile-grid. Optional mono number on top (brand .svc/.rec .num). --}}
@props([
    'num' => null,
])

<article data-sigta="tile" {{ $attributes->merge(['class' => 'flex flex-col bg-surface-raised p-[clamp(22px,2.4vw,32px)]']) }}>
    @if ($num !== null)
        <span class="font-mono text-[0.72rem] tracking-[0.1em] text-graphite">{{ $num }}</span>
    @endif
    {{ $slot }}
</article>
