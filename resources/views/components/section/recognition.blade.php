{{-- Section: recognition — the "Kan du genkende det?" block. section-header +
     a two-column tile-grid of scene/answer cards (italic Fraunces scene line,
     an answer prefixed with →), then a closing line + CTA below the grid.
     Brand-default Danish copy, all overridable; the default cards live in
     packages/contract/src/sections/recognition.json, which the Svelte section
     reads too. --}}
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
$items ??= \Sigta\Ui\Sections::get('recognition', 'items');
@endphp

<section data-sigta="section.recognition" {{ $attributes }}>
    <x-sigta::section-header data-scope="section.recognition" data-part="header" :heading="$heading" :lede="$lede" />

    <x-sigta::tile-grid cols="2">
        @foreach ($items as $item)
            <x-sigta::tile-grid.tile :num="$item['num'] ?? null" data-scope="section.recognition" data-part="tile">
                @if (! empty($item['scene']))
                    <p data-scope="section.recognition" data-part="scene">{{ $item['scene'] }}</p>
                @endif
                @if (! empty($item['answer']))
                    <p data-scope="section.recognition" data-part="answer">{{ $item['answer'] }}</p>
                @endif
            </x-sigta::tile-grid.tile>
        @endforeach
    </x-sigta::tile-grid>

    @if ($closingText || $ctaLabel)
        <div data-scope="section.recognition" data-part="outro">
            @if ($closingText)
                <p data-scope="section.recognition" data-part="closing">{{ $closingText }}</p>
            @endif
            @if ($ctaLabel)
                <x-sigta::button :href="$ctaHref" :target="$target">
                    {{ $ctaLabel }} <span data-scope="section.recognition" data-part="arrow" aria-hidden="true">→</span>
                </x-sigta::button>
            @endif
        </div>
    @endif
</section>
