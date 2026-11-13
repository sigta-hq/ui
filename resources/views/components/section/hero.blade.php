{{-- Section: hero — eyebrow + big heading + lede + up to two CTAs + an inline
     stat row, beside an optional `visual` slot (illustration, screenshot). Two
     columns with a visual, one without; stacks on mobile. Ships no copy: every
     part renders only when you pass it. --}}
@props([
    'eyebrow' => null,
    'heading' => null,
    'lede' => null,
    'primaryLabel' => null,
    'primaryHref' => null,
    'target' => null,
    'secondaryLabel' => null,
    'secondaryHref' => null,
    'secondaryTarget' => null,
    'stats' => [],
])

<section data-sigta="section.hero" {{ $attributes->merge(['class' => 'max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter) pt-[clamp(48px,8vw,110px)] pb-[clamp(40px,6vw,80px)]']) }}>
    <div @class(['grid items-center gap-[clamp(32px,5vw,72px)]', 'lg:grid-cols-[1.15fr_0.85fr]' => isset($visual)])>
        <div>
            @if ($eyebrow)
                <x-sigta::eyebrow class="mb-[1.2rem]">{{ $eyebrow }}</x-sigta::eyebrow>
            @endif

            {{-- Heading: rich HTML via the default slot (author <sigta:mark> there),
                 or a plain/HTML string via :heading. --}}
            @if ($slot->isNotEmpty() || $heading !== null)
                <x-sigta::heading level="1" size="display">@if ($slot->isNotEmpty()){!! $slot !!}@else{!! $heading !!}@endif</x-sigta::heading>
            @endif

            @if ($lede)
                <x-sigta::text variant="lede" class="max-w-[36ch] mt-[1.6rem] mb-[2.2rem]">{{ $lede }}</x-sigta::text>
            @endif

            @if ($primaryLabel || $secondaryLabel)
                <div class="flex flex-wrap items-center gap-[0.9rem]">
                    @if ($primaryLabel)
                        <x-sigta::button variant="cta" :href="$primaryHref" :target="$target" class="group">
                            {{ $primaryLabel }} <span class="transition-transform duration-[180ms] ease-out group-hover:translate-x-[3px]" aria-hidden="true">→</span>
                        </x-sigta::button>
                    @endif
                    @if ($secondaryLabel)
                        <x-sigta::button :href="$secondaryHref" :target="$secondaryTarget">{{ $secondaryLabel }}</x-sigta::button>
                    @endif
                </div>
            @endif

            @if (! empty($stats))
                <div class="mt-[2.6rem] flex flex-wrap gap-[2.2rem]">
                    @foreach ($stats as $stat)
                        <x-sigta::stat variant="inline" :value="$stat['value'] ?? null" :label="$stat['label'] ?? null" />
                    @endforeach
                </div>
            @endif
        </div>

        @isset($visual)
            <div class="relative text-ink">{{ $visual }}</div>
        @endisset
    </div>
</section>
