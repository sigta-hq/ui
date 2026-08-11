{{-- Section: features — section-header + a tile-grid of service tiles
     (num, icon, title, text). Brand default = the 4 "Sådan hjælper vi"
     services. Override copy/icons via :items; icon accepts an inline <svg…>
     string or a lucide name. --}}
@props([
    'eyebrow' => null,
    'heading' => 'Sådan hjælper vi.',
    'lede' => 'Fire måder at bruge en tech-partner på. De fleste samarbejder starter med én og vokser derfra.',
    'cols' => 4,
    'items' => null,
])

@php
$items ??= [
    [
        'num' => '01',
        'icon' => '<svg width="34" height="34" viewBox="0 0 34 34" fill="none"><circle cx="17" cy="17" r="13" stroke="currentColor" stroke-width="2"/><path d="M17 17 L23 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="17" cy="17" r="2.4" fill="currentColor"/><path d="M17 4 V7 M17 27 V30 M4 17 H7 M27 17 H30" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
        'title' => 'Teknisk ledelse',
        'text' => 'Strategi, roadmap og arkitektur på deltid. En ekstern CTO, uden en CTO på lønningslisten.',
    ],
    [
        'num' => '02',
        'icon' => '<svg width="34" height="34" viewBox="0 0 34 34" fill="none"><path d="M5 8 H21 V19 H12 L8 23 V19 H5 Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M24 13 H29 V24 H26 V28 L22 24 H15" stroke="currentColor" stroke-width="2" stroke-linejoin="round" style="fill: var(--sigta-accent)"/></svg>',
        'title' => 'Din mand i rummet',
        'text' => 'Med til leverandørmøder og gennemgang af tilbud og kontrakter. Rådgivningen er uvildig: Sigta tjener ikke på, hvilken løsning du vælger.',
    ],
    [
        'num' => '03',
        'icon' => '<svg width="34" height="34" viewBox="0 0 34 34" fill="none"><circle cx="14" cy="14" r="9" stroke="currentColor" stroke-width="2"/><path d="M21 21 L29 29" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/><path d="M10 14 L13 17 L18.5 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'title' => 'Teknisk vurdering',
        'text' => 'Sundhedstjek af kodebase, projekt eller leverandør. Et ærligt svar på spørgsmålet: er det her fornuftigt?',
    ],
    [
        'num' => '04',
        'icon' => '<svg width="34" height="34" viewBox="0 0 34 34" fill="none"><path d="M12 9 6 17 12 25" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 9 28 17 22 25" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 7 15 27" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
        'title' => 'Hands-on udvikling',
        'text' => "MVP'er, prototyper og kritiske systemer. Vi kan også selv skrive koden. Omhyggeligt.",
    ],
];
@endphp

<section data-sigta="section.features" {{ $attributes->merge(['class' => 'max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter) py-[clamp(40px,6vw,72px)]']) }}>
    <x-sigta::section-header class="mb-[clamp(28px,4vw,52px)]" :eyebrow="$eyebrow" :heading="$heading" :lede="$lede" />

    <x-sigta::tile-grid :cols="$cols">
        @foreach ($items as $item)
            <x-sigta::tile-grid.tile :num="$item['num'] ?? null" class="min-h-[240px]">
                @if (! empty($item['icon']))
                    <span class="mt-[18px] mb-auto text-ink" aria-hidden="true">
                        @if (str_starts_with(trim($item['icon']), '<svg')){!! $item['icon'] !!}@else<x-sigta::icon :name="$item['icon']" class="size-[34px]" />@endif
                    </span>
                @endif
                @if (! empty($item['title']))
                    <x-sigta::heading level="3" class="mt-[18px] mb-2">{{ $item['title'] }}</x-sigta::heading>
                @endif
                @if (! empty($item['text']))
                    <p class="text-[0.95rem] leading-[1.5] text-graphite">{{ $item['text'] }}</p>
                @endif
            </x-sigta::tile-grid.tile>
        @endforeach
    </x-sigta::tile-grid>
</section>
