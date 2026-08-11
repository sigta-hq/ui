<?php

use Illuminate\Support\Facades\Blade;

it('renders the default pull-quote and cite', function () {
    $html = Blade::render('<x-sigta::section.quote />');

    expect($html)
        ->toContain('data-sigta="section.quote"')
        ->toContain('Du behøver ikke forstå koden for at træffe gode beslutninger. Du skal bare kende én, der gør.')
        ->toContain('Sådan arbejder Sigta')
        ->toContain('<blockquote')
        ->toContain('font-head')
        ->toContain('italic');
});

it('lets the quote and cite props win', function () {
    $html = Blade::render('<x-sigta::section.quote quote="Et eget citat." cite="Egen kilde" />');

    expect($html)
        ->toContain('Et eget citat.')
        ->toContain('Egen kilde')
        ->not->toContain('Du behøver ikke forstå koden');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section.quote data-test="quote" />');

    expect($html)->toContain('data-test="quote"');
});
