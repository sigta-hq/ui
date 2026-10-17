{{-- Centered pull-quote: big Fraunces italic + mono cite. Generous rhythm. --}}
@props([
    'quote' => 'Du behøver ikke forstå koden for at træffe gode beslutninger. Du skal bare kende én, der gør.',
    'cite' => 'Sådan arbejder Sigta',
])

<section data-sigta="section.quote" {{ $attributes }}>
    <div data-scope="section.quote" data-part="wrap">
        <blockquote data-scope="section.quote" data-part="quote">“{{ $quote }}”</blockquote>
        @if ($cite)
            <span data-scope="section.quote" data-part="cite">{{ $cite }}</span>
        @endif
    </div>
</section>
