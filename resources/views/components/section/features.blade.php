{{-- Section: features — section-header + a tile-grid of service tiles
     (num, icon, title, text). Brand default = the 4 "Sådan hjælper vi"
     services, kept in packages/contract/src/sections/features.json, which the
     Svelte section reads too. Override copy/icons via :items; icon accepts an
     inline <svg…> string or an icon name. --}}
@props([
    'eyebrow' => null,
    'heading' => 'Sådan hjælper vi.',
    'lede' => 'Fire måder at bruge en tech-partner på. De fleste samarbejder starter med én og vokser derfra.',
    'cols' => 4,
    'items' => null,
])

@php
$items ??= \Sigta\Ui\Sections::get('features', 'items');
@endphp

<section data-sigta="section.features" {{ $attributes }}>
    <x-sigta::section-header data-scope="section.features" data-part="header" :eyebrow="$eyebrow" :heading="$heading" :lede="$lede" />

    <x-sigta::tile-grid :cols="$cols">
        @foreach ($items as $item)
            <x-sigta::tile-grid.tile :num="$item['num'] ?? null" data-scope="section.features" data-part="tile">
                @if (! empty($item['icon']))
                    <span data-scope="section.features" data-part="icon" aria-hidden="true">
                        @if (str_starts_with(trim($item['icon']), '<svg')){!! $item['icon'] !!}@else<x-sigta::icon :name="$item['icon']" data-scope="section.features" data-part="glyph" />@endif
                    </span>
                @endif
                @if (! empty($item['title']))
                    <x-sigta::heading level="3" data-scope="section.features" data-part="title">{{ $item['title'] }}</x-sigta::heading>
                @endif
                @if (! empty($item['text']))
                    <p data-scope="section.features" data-part="text">{{ $item['text'] }}</p>
                @endif
            </x-sigta::tile-grid.tile>
        @endforeach
    </x-sigta::tile-grid>
</section>
