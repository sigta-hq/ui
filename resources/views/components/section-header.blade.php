{{-- Brand .sec-head: heading left, graphite lede right, baseline-aligned, wraps. --}}
@props([
    'heading' => null,
    'eyebrow' => null,
    'lede' => null,
])

<div data-sigta="section-header" {{ $attributes }}>
    <div>
        @if ($eyebrow)
            <x-sigta::eyebrow data-scope="section-header" data-part="eyebrow">{{ $eyebrow }}</x-sigta::eyebrow>
        @endif
        <x-sigta::heading level="2" size="xl" data-scope="section-header" data-part="heading">{{ $heading }}</x-sigta::heading>
    </div>
    @if ($lede !== null || $slot->isNotEmpty())
        <x-sigta::text variant="muted" data-scope="section-header" data-part="lede">{{ $lede ?? $slot }}</x-sigta::text>
    @endif
</div>
