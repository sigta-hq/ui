{{-- Ink CTA band. Dark surface via the contract (bg-inverse/text-inverse-fg) +
     button variants (inverse / ghost-inverse) — never !-overrides. Accent
     annotation overhangs the top-right corner. --}}
@props([
    'heading' => 'Skal vi tage en snak, før du skriver under på noget?',
    'lede' => 'En intro-samtale koster ingenting og forpligter ikke. Medbring gerne det tilbud, du er i tvivl om.',
    'primaryLabel' => 'Book en intro-samtale',
    'primaryHref' => 'https://calendly.com/sf-sigta/30min',
    'target' => null,
    'secondaryLabel' => 'Skriv en mail',
    'secondaryHref' => 'mailto:sf@sigta.dk',
    'secondaryTarget' => null,
    'annotation' => 'lad os tegne det op ↘',
])

<section data-sigta="section.cta" {{ $attributes }}>
    <div data-scope="section.cta" data-part="wrap">
        <div data-scope="section.cta" data-part="band">
            @if ($annotation)
                <x-sigta::annot :rotate="-6" tone="accent" data-scope="section.cta" data-part="note">{{ $annotation }}</x-sigta::annot>
            @endif
            <h2 data-scope="section.cta" data-part="heading">{{ $heading }}</h2>
            @if ($lede)
                <p data-scope="section.cta" data-part="lede">{{ $lede }}</p>
            @endif
            <div data-scope="section.cta" data-part="actions">
                <x-sigta::button variant="inverse" :href="$primaryHref" :target="$target">{{ $primaryLabel }} <span data-scope="section.cta" data-part="arrow" aria-hidden="true">→</span></x-sigta::button>
                <x-sigta::button variant="ghost-inverse" :href="$secondaryHref" :target="$secondaryTarget">{{ $secondaryLabel }}</x-sigta::button>
            </div>
        </div>
    </div>
</section>
