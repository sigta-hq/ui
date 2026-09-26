{{-- Bordered surface like Card, but WITHOUT overflow-hidden so overhanging
     annotations / badges show. Static (no hover lift). Brand .price-panel.

     variant="inverse" flips it to a dark ink surface (the design's dark stat
     band) — pair it with <sigta:label variant="inverse"> and
     <sigta:stat tone="inverse"> so nested chrome inverts too.

     Pass any padding class (p-4, px-6, sm:p-5, !p-2) to replace the default
     fluid padding entirely. --}}
@props([
    'variant' => 'default',
])

@php
// A padding utility from the consumer replaces the default rather than competing
// with it: both would be single-class utilities, and the compiled CSS order, not
// the markup, would pick the winner.
$padding = preg_match('/(^|\s)([^\s:]+:)*!?p[xytrblse]?-/', (string) $attributes->get('class'))
    ? ''
    : 'p-[clamp(24px,3vw,40px)] ';

$surface = match ($variant) {
    'inverse' => 'bg-inverse text-inverse-fg border border-inverse-line',
    default => 'bg-surface-raised border border-line',
};
@endphp

<div data-sigta="panel" data-variant="{{ $variant }}" {{ $attributes->merge(['class' => 'relative flex flex-col rounded-panel '.$padding.$surface]) }}>{{ $slot }}</div>
