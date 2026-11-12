{{-- Centered pull-quote: big Fraunces italic + mono cite. Generous rhythm.
     Ships no copy: pass your own quote and cite. --}}
@props([
    'quote' => null,
    'cite' => null,
])

<section data-sigta="section.quote" {{ $attributes->merge(['class' => 'py-[clamp(50px,8vw,96px)] text-center']) }}>
    <div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">
        @if ($quote)
            <blockquote class="mx-auto max-w-[24ch] text-balance font-head font-light italic text-[clamp(1.8rem,4.6vw,3.2rem)] leading-[1.14] tracking-[-0.015em]">“{{ $quote }}”</blockquote>
        @endif
        @if ($cite)
            <span class="mt-[1.8rem] block font-mono text-[0.74rem] tracking-[0.12em] uppercase text-graphite">{{ $cite }}</span>
        @endif
    </div>
</section>
