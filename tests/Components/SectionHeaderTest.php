<?php

use Illuminate\Support\Facades\Blade;

it('renders a heading and a lede in a baseline-aligned flex row', function () {
    $html = Blade::render('<x-sigta::section-header heading="Kan du genkende det?" lede="Fire situationer, hvor det hjælper." />');

    expect($html)
        ->toContain('data-sigta="section-header"')
        ->toContain('Kan du genkende det?')
        ->toContain('Fire situationer, hvor det hjælper.')
        ->toContain('<h2')
        ->toMatch('/<h2 data-sigta="heading"[^>]*data-part="heading"/')
        ->toMatch('/<p data-sigta="text"[^>]*data-variant="muted"[^>]*data-part="lede"|<p data-sigta="text"[^>]*data-part="lede"[^>]*data-variant="muted"/');

    expect(contractCss('section-header'))
        ->toMatch('/\[data-sigta="section-header"\] \{\s*@apply flex flex-wrap items-end justify-between gap-8;/')
        ->toContain('[data-scope="section-header"][data-part="heading"] {')
        ->toContain('[data-scope="section-header"][data-part="lede"] {');
});

it('renders the lede from the default slot when no lede prop is given', function () {
    $html = Blade::render('<x-sigta::section-header heading="Priser">Lede fra slot</x-sigta::section-header>');

    expect($html)->toContain('Lede fra slot')->toContain('data-part="lede"');
});

it('omits the lede when there is neither a prop nor a slot', function () {
    expect(Blade::render('<x-sigta::section-header heading="Priser" />'))->not->toContain('data-part="lede"');
});

it('renders an optional eyebrow above the heading', function () {
    $html = Blade::render('<x-sigta::section-header eyebrow="Om Sigta" heading="Manden bag" lede="Kort intro." />');

    expect($html)
        ->toContain('data-sigta="eyebrow"')
        ->toMatch('/data-scope="section-header" data-part="eyebrow"/')
        ->toContain('Om Sigta');

    expect(strpos($html, 'Om Sigta'))->toBeLessThan(strpos($html, 'Manden bag'));
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section-header heading="H" lede="L" data-test="sh" />');

    expect($html)->toContain('data-test="sh"');
});
