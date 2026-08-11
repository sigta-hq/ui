{{-- Ink CTA band. Dark surface via bg-inverse/text-inverse-fg + button
     variants (inverse / ghost-inverse) — never !-overrides. Accent annotation
     overhangs the top-right corner. --}}
@props([
    'heading' => 'Skal vi tage en snak, før du skriver under på noget?',
    'lede' => 'En intro-samtale koster ingenting og forpligter ikke. Medbring gerne det tilbud, du er i tvivl om.',
    'primaryLabel' => 'Book en intro-samtale',
    'primaryHref' => 'https://calendly.com/sf-sigta/30min',
    'secondaryLabel' => 'Skriv en mail',
    'secondaryHref' => 'mailto:sf@sigta.dk',
    'annotation' => 'lad os tegne det op ↘',
])

<section data-sigta="section.cta" {{ $attributes->merge(['class' => 'py-[clamp(40px,6vw,72px)]']) }}>
    <div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">
        <div class="relative overflow-hidden rounded-frame bg-inverse p-[clamp(34px,6vw,72px)] text-center text-inverse-fg">
            @if ($annotation)
                <x-sigta::annot :rotate="-6" tone="accent" class="absolute top-[18px] right-[7%] text-[1.5rem] max-md:hidden">{{ $annotation }}</x-sigta::annot>
            @endif
            <h2 class="mx-auto max-w-[20ch] text-balance font-head font-normal text-[clamp(2rem,5vw,3.4rem)] leading-[1.04]">{{ $heading }}</h2>
            @if ($lede)
                <p class="mx-auto mt-[1.2rem] mb-[2.2rem] max-w-[46ch] text-inverse-fg-muted">{{ $lede }}</p>
            @endif
            <div class="flex flex-wrap justify-center gap-[0.9rem]">
                <x-sigta::button variant="inverse" :href="$primaryHref" class="group">{{ $primaryLabel }} <span class="transition-transform duration-[180ms] ease-out group-hover:translate-x-[3px]" aria-hidden="true">→</span></x-sigta::button>
                <x-sigta::button variant="ghost-inverse" :href="$secondaryHref">{{ $secondaryLabel }}</x-sigta::button>
            </div>
        </div>
    </div>
</section>
