{{-- Brand .sec-head: heading left, graphite lede right, baseline-aligned, wraps. --}}
@props([
    'heading' => null,
    'eyebrow' => null,
    'lede' => null,
])

<div data-sigta="section-header" {{ $attributes->merge(['class' => 'flex flex-wrap items-end justify-between gap-8']) }}>
    <div>
        @if ($eyebrow)
            <x-sigta::eyebrow class="mb-[1.2rem]">{{ $eyebrow }}</x-sigta::eyebrow>
        @endif
        <x-sigta::heading level="2" size="xl" class="max-w-[16ch]">{{ $heading }}</x-sigta::heading>
    </div>
    @if ($lede !== null || $slot->isNotEmpty())
        <x-sigta::text variant="muted" class="max-w-[34ch]">{{ $lede ?? $slot }}</x-sigta::text>
    @endif
</div>
