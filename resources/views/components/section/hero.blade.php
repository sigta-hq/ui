{{-- Section: hero — eyebrow + big heading (mark highlight) + lede + two CTAs +
     inline stat row + a visual slot (default: hand-drawn søkort). Two-column
     grid that stacks on mobile. Brand-default Danish copy, all overridable. --}}
@props([
    'eyebrow' => 'Tech-partner · Hjælp, rådgivning og udvikling',
    'heading' => null,
    'lede' => 'Sigta er den tech-partner, du kan få: hjælp, rådgivning og udvikling til virksomheder, der ikke har deres egen tekniske ledelse. Vi taler begge sprog, forretning og teknik.',
    'primaryLabel' => 'Book en intro-samtale',
    'primaryHref' => 'https://calendly.com/sf-sigta/30min',
    'target' => '_blank',
    'secondaryLabel' => 'Se hvordan vi arbejder',
    'secondaryHref' => '#hjaelp',
    'secondaryTarget' => null,
    'stats' => null,
])

@php
// Inline stat row (brand .hero-meta). Ship a default so the section is finished
// out of the box; override with :stats=[['value'=>…,'label'=>…], …].
$stats ??= [
    ['value' => '14 år', 'label' => 'bag tastaturet'],
    ['value' => '1', 'label' => 'fast lav timepris'],
    ['value' => '2', 'label' => 'måder at samarbejde på'],
];
@endphp

<section data-sigta="section.hero" {{ $attributes->merge(['class' => 'max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter) pt-[clamp(48px,8vw,110px)] pb-[clamp(40px,6vw,80px)]']) }}>
    <div class="grid items-center gap-[clamp(32px,5vw,72px)] lg:grid-cols-[1.15fr_0.85fr]">
        <div>
            @if ($eyebrow)
                <x-sigta::eyebrow class="mb-[1.2rem]">{{ $eyebrow }}</x-sigta::eyebrow>
            @endif

            {{-- Heading: rich HTML via the default slot (author <sigta:mark> there),
                 or a plain/HTML string via :heading, else the branded default. --}}
            <x-sigta::heading level="1" size="display">@if ($slot->isNotEmpty()){!! $slot !!}@elseif ($heading !== null){!! $heading !!}@else Teknologi, <em>oversat</em> til <x-sigta::mark>forretning</x-sigta::mark>. Og&nbsp;tilbage&nbsp;igen.@endif</x-sigta::heading>

            @if ($lede)
                <x-sigta::text variant="lede" class="max-w-[36ch] mt-[1.6rem] mb-[2.2rem]">{{ $lede }}</x-sigta::text>
            @endif

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

            @if (! empty($stats))
                <div class="mt-[2.6rem] flex flex-wrap gap-[2.2rem]">
                    @foreach ($stats as $stat)
                        <x-sigta::stat variant="inline" :value="$stat['value'] ?? null" :label="$stat['label'] ?? null" />
                    @endforeach
                </div>
            @endif
        </div>

        <div class="relative text-ink">
            @isset($visual)
                {{ $visual }}
            @else
                {{-- håndtegnet søkort — inline SVG, tokens/currentColor only --}}
                <svg viewBox="0 0 360 300" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Et håndtegnet søkort: en stiplet rute fra 'I er her' uden om forhindringerne 'dyrt' og 'teknisk gæld' til et kryds markeret 'sikker havn'.">
                    <defs>
                        <pattern id="soekort-dots" width="20" height="20" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1" style="fill: var(--sigta-line)"/>
                        </pattern>
                    </defs>
                    {{-- kompasrose --}}
                    <circle cx="60" cy="64" r="22" fill="none" stroke="currentColor" stroke-width="2"/>
                    <path d="M60 46 L65 64 L60 82 L55 64 Z" fill="currentColor"/>
                    <text x="60" y="38" text-anchor="middle" font-family="JetBrains Mono, monospace" font-size="11" fill="currentColor">N</text>
                    {{-- forhindring: dyrt --}}
                    <path d="M120 96 c10 -14 34 -12 40 2 s-4 26 -22 24 -28 -12 -18 -26z" style="fill: var(--sigta-surface-raised)" stroke="currentColor" stroke-width="2"/>
                    <text x="140" y="112" text-anchor="middle" font-family="Caveat, cursive" font-size="16" style="fill: var(--sigta-fg-muted)">dyrt</text>
                    {{-- forhindring: teknisk gæld --}}
                    <path d="M166 212 c2 -18 28 -28 64 -28 s66 10 64 28 -28 30 -64 30 -66 -12 -64 -30z" style="fill: var(--sigta-surface-raised)" stroke="currentColor" stroke-width="2"/>
                    <text x="230" y="219" text-anchor="middle" font-family="Caveat, cursive" font-size="17" style="fill: var(--sigta-fg-muted)">teknisk gæld</text>
                    {{-- ruten --}}
                    <circle cx="46" cy="252" r="7" fill="currentColor"/>
                    <text x="60" y="274" font-family="Caveat, cursive" font-size="19" fill="currentColor">I er her</text>
                    <path d="M46 252 C70 220 88 150 122 140 S170 168 196 158 232 108 262 92 300 74 306 62"
                          fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-dasharray="3 7"/>
                    {{-- målet --}}
                    <rect x="286" y="38" width="46" height="34" rx="3" style="fill: var(--sigta-accent)" opacity=".9" transform="rotate(-5 309 55)"/>
                    <path d="M300 48 L318 66 M318 48 L300 66" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    <ellipse cx="309" cy="57" rx="26" ry="20" fill="none" style="stroke: var(--sigta-danger)" stroke-width="1.8" transform="rotate(-8 309 57)"/>
                    <text x="298" y="102" text-anchor="middle" font-family="Caveat, cursive" font-size="20" fill="currentColor" transform="rotate(-4 298 102)">sikker havn</text>
                </svg>
            @endisset
        </div>
    </div>
</section>
