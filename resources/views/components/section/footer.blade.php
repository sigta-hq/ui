{{-- Site footer: brand + tagline, nav columns, dashed signoff line. Uses a
     <footer> landmark as its root but carries the section contract's
     data-sigta hook. --}}
@props([
    'tagline' => 'Sigta er tech-partneren for virksomheder uden egen teknisk ledelse: hjælp, rådgivning og udvikling.',
    'signoff' => '© 2026 Sigta · CVR 45530760',
    'columns' => [
        [
            'heading' => 'Navigation',
            'links' => [
                ['label' => 'Kan du genkende det?', 'href' => '#genkend'],
                ['label' => 'Sådan hjælper vi', 'href' => '#hjaelp'],
                ['label' => 'Priser', 'href' => '#priser'],
                ['label' => 'Om Sigta', 'href' => '#om'],
            ],
        ],
        [
            'heading' => 'Kontakt',
            'links' => [
                ['label' => 'Book en intro-samtale', 'href' => 'https://calendly.com/sf-sigta/30min'],
                ['label' => 'sf@sigta.dk', 'href' => 'mailto:sf@sigta.dk'],
                ['label' => 'LinkedIn', 'href' => 'https://www.linkedin.com/in/soeren-frisk/'],
            ],
        ],
    ],
])

<footer data-sigta="section.footer" {{ $attributes->merge(['class' => 'mt-[clamp(50px,7vw,90px)] border-t border-line pt-[clamp(40px,5vw,64px)] pb-8']) }}>
    <div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">
        <div class="grid gap-8 sm:grid-cols-2 md:grid-cols-[1.5fr_1fr_1fr]">
            <div>
                <x-sigta::brand />
                @if ($tagline)
                    <p class="mt-4 max-w-[32ch] text-[0.92rem] text-graphite">{{ $tagline }}</p>
                @endif
            </div>
            @foreach ($columns as $column)
                <div>
                    <h4 class="mb-4 font-mono text-[0.68rem] font-normal tracking-[0.12em] uppercase text-graphite">{{ $column['heading'] }}</h4>
                    <ul class="m-0 flex list-none flex-col gap-[0.6rem] p-0">
                        @foreach ($column['links'] as $link)
                            <li><a href="{{ $link['href'] }}" class="text-[0.92rem] text-ink no-underline hover:underline hover:underline-offset-[3px]">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
        @if ($signoff)
            <div class="mt-[clamp(36px,5vw,56px)] flex flex-wrap items-center justify-between gap-4 border-t border-dashed border-line pt-[1.6rem] font-mono text-[0.72rem] text-graphite">
                <span>{{ $signoff }}</span>
            </div>
        @endif
    </div>
</footer>
