{{-- Centered pull-quote: big Fraunces italic + mono cite. Generous rhythm. --}}
@props([
    'quote' => 'Du behøver ikke forstå koden for at træffe gode beslutninger. Du skal bare kende én, der gør.',
    'cite' => 'Sådan arbejder Sigta',
])

<section data-sigta="section.quote" {{ $attributes->merge(['class' => 'py-[clamp(50px,8vw,96px)] text-center']) }}>
    <div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">
        <blockquote class="mx-auto max-w-[24ch] text-balance font-head font-light italic text-[clamp(1.8rem,4.6vw,3.2rem)] leading-[1.14] tracking-[-0.015em]">“{{ $quote }}”</blockquote>
        @if ($cite)
            <span class="mt-[1.8rem] block font-mono text-[0.74rem] tracking-[0.12em] uppercase text-graphite">{{ $cite }}</span>
        @endif
    </div>
</section>
