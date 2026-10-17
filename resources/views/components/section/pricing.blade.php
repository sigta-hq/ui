{{-- Section: pricing — section-header + two non-clipping panels side by side
     (stacks on mobile). Each panel: mono label, title, a big figure (mark on
     the price), body paragraphs; a tier may carry an overhanging annot
     hand-note — which is WHY <sigta:panel> is non-clipping. Brand-default
     Danish copy, all overridable via :tiers; the default tiers live in
     packages/contract/src/sections/pricing.json, which the Svelte section
     reads too. --}}
@props([
    'heading' => 'To måder at samarbejde på.',
    'lede' => 'Én filosofi bag begge: du skal altid vide, hvad du betaler for.',
    'tiers' => null,
])

@php
$tiers ??= \Sigta\Ui\Sections::get('pricing', 'tiers');
@endphp

<section data-sigta="section.pricing" {{ $attributes }}>
    <x-sigta::section-header data-scope="section.pricing" data-part="header" :heading="$heading" :lede="$lede" />

    <div data-scope="section.pricing" data-part="tiers">
        @foreach ($tiers as $tier)
            <x-sigta::panel>
                @if (! empty($tier['annot']))
                    <x-sigta::annot :rotate="-5" data-scope="section.pricing" data-part="note">{{ $tier['annot'] }}</x-sigta::annot>
                @endif

                @if (! empty($tier['label']))
                    <span data-scope="section.pricing" data-part="label">{{ $tier['label'] }}</span>
                @endif

                @if (! empty($tier['title']))
                    <x-sigta::heading level="3" data-scope="section.pricing" data-part="title">{{ $tier['title'] }}</x-sigta::heading>
                @endif

                @if (! empty($tier['figure']))
                    <div data-scope="section.pricing" data-part="figure">
                        <span>@if ($tier['mark'] ?? false)<x-sigta::mark>{{ $tier['figure'] }}</x-sigta::mark>@else{{ $tier['figure'] }}@endif</span>
                        @if (! empty($tier['figureSuffix']))
                            <small data-scope="section.pricing" data-part="suffix">{{ $tier['figureSuffix'] }}</small>
                        @endif
                    </div>
                @endif

                @foreach (($tier['body'] ?? []) as $paragraph)
                    <p data-scope="section.pricing" data-part="body">{{ $paragraph }}</p>
                @endforeach
            </x-sigta::panel>
        @endforeach

        {{-- One tier leaves the second column empty; the aside slot fills it, e.g.
             with the call to book for a reader still deciding. --}}
        @isset($aside)
            <div data-scope="section.pricing" data-part="aside" {{ $aside->attributes }}>{{ $aside }}</div>
        @endisset
    </div>
</section>
