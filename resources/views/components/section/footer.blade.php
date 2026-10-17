{{-- Site footer: brand + tagline, nav columns, dashed signoff line. Uses a
     <footer> landmark as its root but carries the section contract's
     data-sigta hook. The default columns live in
     packages/contract/src/sections/footer.json, which the Svelte section reads
     too. --}}
@props([
    'tagline' => 'Sigta er tech-partneren for virksomheder uden egen teknisk ledelse: hjælp, rådgivning og udvikling.',
    'signoff' => '© 2026 Sigta · CVR 45530760',
    'columns' => \Sigta\Ui\Sections::get('footer', 'columns'),
])

<footer data-sigta="section.footer" {{ $attributes }}>
    <div data-scope="section.footer" data-part="wrap">
        <div data-scope="section.footer" data-part="grid">
            <div>
                <x-sigta::brand />
                @if ($tagline)
                    <p data-scope="section.footer" data-part="tagline">{{ $tagline }}</p>
                @endif
            </div>
            @foreach ($columns as $column)
                <div>
                    <h4 data-scope="section.footer" data-part="heading">{{ $column['heading'] }}</h4>
                    <ul data-scope="section.footer" data-part="links">
                        @foreach ($column['links'] as $link)
                            <li><a href="{{ $link['href'] }}" data-scope="section.footer" data-part="link">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
        @if ($signoff)
            <div data-scope="section.footer" data-part="signoff">
                <span>{{ $signoff }}</span>
            </div>
        @endif
    </div>
</footer>
