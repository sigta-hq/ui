{{-- Bordered surface like Card, but WITHOUT overflow-hidden so overhanging
     annotations / badges show. Static (no hover lift). Brand .price-panel.

     variant="inverse" flips it to a dark ink surface (the design's dark stat
     band) — pair it with <sigta:label variant="inverse"> and
     <sigta:stat tone="inverse"> so nested chrome inverts too.

     Pass any padding class (p-4, px-6, sm:p-5, !p-2) to replace the default
     fluid padding entirely.

     Children stack as a flex column. Pass a display class (flex, grid, block)
     to lay them out yourself, or a direction class (flex-row) to keep the flex
     and turn it. --}}
@props([
    'variant' => 'default',
])

@php
// The same two tests as ownsPadding() / ownsDisplay() in @sigta-hq/core, which
// the Svelte panel runs.
$class = (string) $attributes->get('class');
$contract = ['data-variant' => $variant];

// A padding utility from the consumer replaces the default rather than competing
// with it: px-6 alone leaves no vertical default either.
if (preg_match('/(^|\s)([^\s:]+:)*!?p[xytrblse]?-/', $class)) {
    $contract['data-padding'] = 'custom';
}

// The same goes for the layout, or row classes (flex items-center) silently act
// on the column. Only an unprefixed class counts: a responsive one (sm:flex-row)
// builds on the stack, and outranks it wherever it applies. A direction class
// alone (flex-row) keeps the flex and outranks the column by itself.
if (preg_match('/(^|\s)!?(block|inline-block|flex|inline-flex|grid|inline-grid|flow-root)!?(?=\s|$)/', $class)) {
    $contract['data-layout'] = 'custom';
}
@endphp

<div data-sigta="panel" {{ $attributes->merge($contract) }}>{{ $slot }}</div>
