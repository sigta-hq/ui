{{-- Brand "Om Sigta": device-framed portrait + text column. Portrait defaults
     to an inline SVG placeholder (assets/soren.jpg lives in the real site, not
     here); pass the `portrait` slot to drop in a real framed image. The default
     paragraphs live in packages/contract/src/sections/about.json, which the
     Svelte section reads too. --}}
@props([
    'eyebrow' => 'Om Sigta · Manden bag',
    'heading' => 'Jeg hedder Søren, og jeg taler begge sprog.',
    'paragraphs' => \Sigta\Ui\Sections::get('about', 'paragraphs'),
])

<section data-sigta="section.about" {{ $attributes }}>
    <div data-scope="section.about" data-part="wrap">
        <div data-scope="section.about" data-part="grid">
            <div data-scope="section.about" data-part="portrait">
                @isset($portrait)
                    {{ $portrait }}
                @else
                    {{-- device frame + inline SVG portrait placeholder --}}
                    <div data-scope="section.about" data-part="frame">
                        <svg viewBox="0 0 686 800" xmlns="http://www.w3.org/2000/svg" data-scope="section.about" data-part="image" role="img" aria-label="Pladsholder for sort-hvidt portræt af Søren Risbøll Frisk">
                            <rect width="686" height="800" style="fill: var(--sigta-surface-raised)"/>
                            <rect x="24" y="24" width="638" height="752" rx="8" fill="none" style="stroke: var(--sigta-line)" stroke-width="2" stroke-dasharray="10 12"/>
                            <path d="M343 176 c68 -4 122 52 120 126 -2 76 -52 134 -120 134 s-118 -58 -120 -134 c-2 -74 52 -122 120 -126z" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                            <path d="M150 800 c6 -156 88 -234 193 -236 s189 78 193 236" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                            <path d="M262 258 c8 26 32 42 60 40 M366 296 c22 4 42 -6 52 -24" fill="none" style="stroke: var(--sigta-line)" stroke-width="4" stroke-linecap="round"/>
                            <text x="343" y="716" text-anchor="middle" font-family="JetBrains Mono, monospace" font-size="22" letter-spacing="4" style="fill: var(--sigta-fg-muted)">PORTRÆT PÅ VEJ</text>
                        </svg>
                    </div>
                    <x-sigta::annot :rotate="-5" data-scope="section.about" data-part="note">det er mig ✎</x-sigta::annot>
                @endisset
            </div>
            <div>
                @if ($eyebrow)
                    <x-sigta::eyebrow data-scope="section.about" data-part="eyebrow">{{ $eyebrow }}</x-sigta::eyebrow>
                @endif
                <x-sigta::heading level="2" size="xl" data-scope="section.about" data-part="heading">{{ $heading }}</x-sigta::heading>
                @foreach ($paragraphs as $paragraph)
                    <p data-scope="section.about" data-part="paragraph">{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>
    </div>
</section>
