{{-- Bordered surface like Card, but WITHOUT overflow-hidden so overhanging
     annotations / badges show. Static (no hover lift). Brand .price-panel.

     variant="inverse" flips it to a dark ink surface (the design's dark stat
     band) — pair it with <sigta:label variant="inverse"> and
     <sigta:stat tone="inverse"> so nested chrome inverts too. --}}
@props([
    'variant' => 'default',
])

@php
$surface = match ($variant) {
    'inverse' => 'bg-inverse text-inverse-fg border border-inverse-line',
    default => 'bg-surface-raised border border-line',
};
@endphp

<div data-sigta="panel" data-variant="{{ $variant }}" {{ $attributes->merge(['class' => 'relative flex flex-col rounded-panel p-[clamp(24px,3vw,40px)] '.$surface]) }}>{{ $slot }}</div>
