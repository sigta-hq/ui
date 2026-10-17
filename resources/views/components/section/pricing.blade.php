{{-- Section: pricing — section-header + two non-clipping panels side by side
     (stacks on mobile). Each panel: mono label, title, a big figure (mark on
     the price), body paragraphs; a tier may carry an overhanging annot
     hand-note — which is WHY <sigta:panel> is non-clipping. Brand-default
     Danish copy, all overridable via :tiers. --}}
@props([
    'heading' => 'To måder at samarbejde på.',
    'lede' => 'Én filosofi bag begge: du skal altid vide, hvad du betaler for.',
    'tiers' => null,
])

@php
$tiers ??= [
    [
        'label' => 'Spor 1 · Timepris',
        'title' => 'Klassisk: én lav, fast timepris',
        'figure' => '800 kr.',
        'figureSuffix' => '/ time, ekskl. moms',
        'mark' => true,
        'body' => [
            'Én timepris. Ingen tillæg, ingen seniorrater, ingen overraskelser. Prisen er bevidst lavere end hos de store konsulenthuse, fordi et partnerskab fungerer bedst, når fokus er på at bygge og ikke på at diskutere, hvem der betaler for hvad.',
            'Nogle gange går tingene godt, andre gange skal noget laves om. Den lave timepris gør, at vi kan begge dele uden dårlig stemning.',
        ],
    ],
    [
        'label' => 'Spor 2 · Fast månedspris',
        'title' => 'Skræddersyet software som abonnement',
        'figure' => 'Fast pris',
        'figureSuffix' => '/ måned, aftalt på forhånd',
        'mark' => false,
        'annot' => 'som SaaS, bare jeres eget',
        'body' => [
            'Den nye måde at få software på: Vi bygger et værktøj eller en platform tilpasset jer, og I betaler en fast månedlig pris, som var det et SaaS-abonnement.',
            'Startinvesteringen er en brøkdel af et klassisk udviklingsprojekt, og drift og vedligehold er regnet med. Softwaren bliver ved med at virke, og I ved præcis, hvad den koster.',
        ],
    ],
];
@endphp

<section data-sigta="section.pricing" {{ $attributes->merge(['class' => 'max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter) py-[clamp(40px,6vw,72px)]']) }}>
    <x-sigta::section-header class="mb-[clamp(28px,4vw,52px)]" :heading="$heading" :lede="$lede" />

    <div class="grid gap-[clamp(18px,2.2vw,28px)] md:grid-cols-2">
        @foreach ($tiers as $tier)
            <x-sigta::panel>
                @if (! empty($tier['annot']))
                    <x-sigta::annot :rotate="-5" class="absolute -top-3 right-[8%] max-sm:hidden">{{ $tier['annot'] }}</x-sigta::annot>
                @endif

                @if (! empty($tier['label']))
                    <span class="mb-[1.1rem] block font-mono text-[0.66rem] tracking-[0.14em] uppercase text-graphite">{{ $tier['label'] }}</span>
                @endif

                @if (! empty($tier['title']))
                    <x-sigta::heading level="3" class="mb-4 text-[clamp(1.4rem,2.2vw,1.8rem)]">{{ $tier['title'] }}</x-sigta::heading>
                @endif

                @if (! empty($tier['figure']))
                    {{-- A wrapping flex row, not inline text: a suffix too long for the line
                         drops below the figure as one block with its own leading, instead of
                         breaking inside the figure's tall line box and stranding its last words. --}}
                    <div class="mt-[0.4rem] mb-[1.2rem] flex flex-wrap items-baseline gap-x-[0.3em] gap-y-1 font-head font-medium text-[clamp(2rem,3.4vw,2.8rem)] tabular-nums">
                        <span>@if ($tier['mark'] ?? false)<x-sigta::mark>{{ $tier['figure'] }}</x-sigta::mark>@else{{ $tier['figure'] }}@endif</span>
                        @if (! empty($tier['figureSuffix']))
                            <small class="font-mono text-[0.85rem] font-normal leading-snug text-graphite">{{ $tier['figureSuffix'] }}</small>
                        @endif
                    </div>
                @endif

                @foreach (($tier['body'] ?? []) as $paragraph)
                    <p @class(['text-[0.98rem] leading-[1.6] text-graphite', 'mb-4' => ! $loop->last])>{{ $paragraph }}</p>
                @endforeach
            </x-sigta::panel>
        @endforeach

        {{-- One tier leaves the second column empty; the aside slot fills it, e.g.
             with the call to book for a reader still deciding. --}}
        @isset($aside)
            <div {{ $aside->attributes->merge(['class' => 'flex flex-col justify-center gap-4']) }}>{{ $aside }}</div>
        @endisset
    </div>
</section>
