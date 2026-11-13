{{-- Ink CTA band. Dark surface via bg-inverse/text-inverse-fg + button
     variants (inverse / ghost-inverse) — never !-overrides. Accent annotation
     overhangs the top-right corner. Ships no copy: every part renders only
     when you pass it. --}}
@props([
    'heading' => null,
    'lede' => null,
    'primaryLabel' => null,
    'primaryHref' => null,
    'target' => null,
    'secondaryLabel' => null,
    'secondaryHref' => null,
    'secondaryTarget' => null,
    'annotation' => null,
])

<section data-sigta="section.cta" {{ $attributes->merge(['class' => 'py-[clamp(40px,6vw,72px)]']) }}>
    <div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">
        <div class="relative overflow-hidden rounded-frame bg-inverse p-[clamp(34px,6vw,72px)] text-center text-inverse-fg">
            @if ($annotation)
                <x-sigta::annot :rotate="-6" tone="accent" class="absolute top-[18px] right-[7%] text-[1.5rem] max-md:hidden">{{ $annotation }}</x-sigta::annot>
            @endif
            @if ($heading)
                <h2 class="mx-auto max-w-[20ch] text-balance font-head font-normal text-[clamp(2rem,5vw,3.4rem)] leading-[1.04]">{{ $heading }}</h2>
            @endif
            @if ($lede)
                <p class="mx-auto mt-[1.2rem] mb-[2.2rem] max-w-[46ch] text-inverse-fg-muted">{{ $lede }}</p>
            @endif
            @if ($primaryLabel || $secondaryLabel)
                <div class="flex flex-wrap justify-center gap-[0.9rem]">
                    @if ($primaryLabel)
                        <x-sigta::button variant="inverse" :href="$primaryHref" :target="$target" class="group">{{ $primaryLabel }} <span class="transition-transform duration-[180ms] ease-out group-hover:translate-x-[3px]" aria-hidden="true">→</span></x-sigta::button>
                    @endif
                    @if ($secondaryLabel)
                        <x-sigta::button variant="ghost-inverse" :href="$secondaryHref" :target="$secondaryTarget">{{ $secondaryLabel }}</x-sigta::button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
