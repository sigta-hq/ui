{{-- Section: recognition — "does this sound familiar?". section-header + a
     two-column tile-grid of scene/answer cards (italic Fraunces scene line, an
     answer prefixed with →), then a closing line + CTA below the grid. Ships
     no copy: pass your own heading and items. --}}
@props([
    'heading' => null,
    'lede' => null,
    'items' => [],
    'closingText' => null,
    'ctaLabel' => null,
    'ctaHref' => null,
    'target' => null,
])

<section data-sigta="section.recognition" {{ $attributes->merge(['class' => 'max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter) py-[clamp(40px,6vw,72px)]']) }}>
    @if ($heading || $lede)
        <x-sigta::section-header class="mb-[clamp(28px,4vw,52px)]" :heading="$heading" :lede="$lede" />
    @endif

    <x-sigta::tile-grid cols="2">
        @foreach ($items as $item)
            <x-sigta::tile-grid.tile :num="$item['num'] ?? null" class="gap-[0.9rem]">
                @if (! empty($item['scene']))
                    <p class="font-head italic font-normal text-[clamp(1.15rem,1.7vw,1.4rem)] leading-[1.3]">{{ $item['scene'] }}</p>
                @endif
                @if (! empty($item['answer']))
                    <p class="flex items-baseline gap-[0.55em] text-[0.95rem] text-graphite before:content-['→'] before:shrink-0 before:font-mono before:text-ink">{{ $item['answer'] }}</p>
                @endif
            </x-sigta::tile-grid.tile>
        @endforeach
    </x-sigta::tile-grid>

    @if ($closingText || $ctaLabel)
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4 text-center">
            @if ($closingText)
                <p class="text-[1.1rem]">{{ $closingText }}</p>
            @endif
            @if ($ctaLabel)
                <x-sigta::button :href="$ctaHref" :target="$target" class="group">
                    {{ $ctaLabel }} <span class="transition-transform duration-[180ms] ease-out group-hover:translate-x-[3px]" aria-hidden="true">→</span>
                </x-sigta::button>
            @endif
        </div>
    @endif
</section>
