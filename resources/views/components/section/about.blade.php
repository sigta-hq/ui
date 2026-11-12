{{-- About: an optional `portrait` slot (a framed photo, an illustration)
     beside a text column of eyebrow, heading and paragraphs. One column
     without a portrait. Ships no copy: pass your own heading and paragraphs. --}}
@props([
    'eyebrow' => null,
    'heading' => null,
    'paragraphs' => [],
])

<section data-sigta="section.about" {{ $attributes->merge(['class' => 'py-[clamp(40px,6vw,72px)]']) }}>
    <div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">
        <div @class(['grid items-center gap-[clamp(32px,5vw,72px)]', 'md:grid-cols-[0.8fr_1.2fr]' => isset($portrait)])>
            @isset($portrait)
                <div class="relative">{{ $portrait }}</div>
            @endisset
            <div>
                @if ($eyebrow)
                    <x-sigta::eyebrow class="mb-[1.2rem]">{{ $eyebrow }}</x-sigta::eyebrow>
                @endif
                @if ($heading)
                    <x-sigta::heading level="2" size="xl" class="mb-[1.2rem]">{{ $heading }}</x-sigta::heading>
                @endif
                @foreach ($paragraphs as $paragraph)
                    <p class="mb-4 max-w-[52ch] text-graphite last:mb-0">{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>
    </div>
</section>
