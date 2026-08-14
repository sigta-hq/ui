{{-- Section: recognition — the "Kan du genkende det?" block. section-header +
     a two-column tile-grid of scene/answer cards (italic Fraunces scene line,
     an answer prefixed with →), then a closing line + CTA below the grid.
     Brand-default Danish copy, all overridable. --}}
@props([
    'heading' => 'Kan du genkende det?',
    'lede' => 'Fire situationer, hvor det hjælper at have en teknisk stemme på sin egen side af bordet.',
    'items' => null,
    'closingText' => 'Hvis bare ét af dem rammer, så lad os tage en kop kaffe.',
    'ctaLabel' => 'Book en intro-samtale',
    'ctaHref' => 'https://calendly.com/sf-sigta/30min',
    'target' => '_blank',
])

@php
$items ??= [
    [
        'num' => '01 · Tilbuddet i indbakken',
        'scene' => '"Vi har fået et tilbud på 750.000 kr. fra et bureau. Er det rimeligt?"',
        'answer' => 'Svært at vide, når man ikke taler sproget. Vi læser det med dig, linje for linje.',
    ],
    [
        'num' => '02 · Projektet skrider',
        'scene' => '"Udviklingen er bagud, budgettet er brugt, og ingen kan rigtig forklare hvorfor."',
        'answer' => 'Vi finder ud af, hvor det knirker, og hvad der skal til for at komme i mål.',
    ],
    [
        'num' => '03 · Klar til at bygge',
        'scene' => '"Idéen er på plads. Men arkitektur, teknologivalg, og hvem skal bygge det?"',
        'answer' => 'Vi lægger planen sammen med dig, før du bruger den første krone.',
    ],
    [
        'num' => '04 · Vokset fra gaffatapen',
        'scene' => '"Virksomheden er vokset. Systemerne er fulgt med, men kun på gaffatape."',
        'answer' => 'Vi rydder op og sætter en teknisk retning, der holder til næste vækstspring.',
    ],
];
@endphp

<section data-sigta="section.recognition" {{ $attributes->merge(['class' => 'max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter) py-[clamp(40px,6vw,72px)]']) }}>
    <x-sigta::section-header class="mb-[clamp(28px,4vw,52px)]" :heading="$heading" :lede="$lede" />

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
