{{-- Section: features — section-header + a tile-grid of tiles (num, icon,
     title, text) from :items. icon accepts an inline <svg…> string or a lucide
     name. Ships no copy: pass your own heading and items. --}}
@props([
    'eyebrow' => null,
    'heading' => null,
    'lede' => null,
    'cols' => 4,
    'items' => [],
])

<section data-sigta="section.features" {{ $attributes->merge(['class' => 'max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter) py-[clamp(40px,6vw,72px)]']) }}>
    @if ($eyebrow || $heading || $lede)
        <x-sigta::section-header class="mb-[clamp(28px,4vw,52px)]" :eyebrow="$eyebrow" :heading="$heading" :lede="$lede" />
    @endif

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
