<?php

use Illuminate\Support\Facades\Blade;

it('renders the default pull-quote and cite', function () {
    $html = Blade::render('<x-sigta::section.quote />');

    expect($html)
        ->toContain('data-sigta="section.quote"')
        ->toContain('Du behøver ikke forstå koden for at træffe gode beslutninger. Du skal bare kende én, der gør.')
        ->toContain('Sådan arbejder Sigta')
        ->toContain('<blockquote data-scope="section.quote" data-part="quote">');

    expect(contractCss('section.quote'))
        ->toContain('[data-scope="section.quote"][data-part="quote"] {')
        ->toMatch('/@apply [^;]*font-head font-light italic/');
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
