{{-- Section: pricing — section-header + two non-clipping panels side by side
     (stacks on mobile). Each panel: mono label, title, a big figure (mark on
     the price), body paragraphs; a tier may carry an overhanging annot
     hand-note — which is WHY <sigta:panel> is non-clipping. Ships no copy:
     pass your own heading and :tiers. --}}
@props([
    'heading' => null,
    'lede' => null,
    'tiers' => [],
])

<section data-sigta="section.pricing" {{ $attributes->merge(['class' => 'max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter) py-[clamp(40px,6vw,72px)]']) }}>
    @if ($heading || $lede)
        <x-sigta::section-header class="mb-[clamp(28px,4vw,52px)]" :heading="$heading" :lede="$lede" />
    @endif

    <div class="grid gap-[clamp(18px,2.2vw,28px)] md:grid-cols-2">
        @foreach ($tiers as $tier)
            <x-sigta::panel>
                @if (! empty($tier['annot']))
                    <x-sigta::annot :rotate="-5" class="absolute -top-3 right-[8%] text-[1.3rem] max-sm:hidden">{{ $tier['annot'] }}</x-sigta::annot>
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
