<?php

use Illuminate\Support\Facades\Blade;

it('renders with default Danish heading and the seven FAQ entries', function () {
    $html = Blade::render('<x-sigta::section.faq />');

    expect($html)
        ->toContain('data-sigta="section.faq"')
        ->toContain('Spørgsmål, vi ofte får.')
        ->toContain('Og de ærlige svar.')
        ->toContain('Hvad koster det?')
        ->toContain('800 kr./time ekskl. moms')
        ->toContain('Er vi bundet af noget?')
        ->toContain('data-sigta="accordion"')
        ->toContain('data-sigta="accordion.item"');

    // seven <details> items
    expect(substr_count($html, 'data-sigta="accordion.item"'))->toBe(7);
});

it('names the accordion group so it is single-open', function () {
    $html = Blade::render('<x-sigta::section.faq />');

    expect($html)->toContain('name="faq"');
});

it('lets the heading and lede props win', function () {
    $html = Blade::render('<x-sigta::section.faq heading="Egne spørgsmål" lede="Egen lede." />');

    expect($html)
        ->toContain('Egne spørgsmål')
        ->toContain('Egen lede.')
        ->not->toContain('Spørgsmål, vi ofte får.');
});

it('renders custom items from the :items prop', function () {
    $html = Blade::render('<x-sigta::section.faq :items="$items" />', [
        'items' => [['q' => 'Eget spørgsmål?', 'a' => 'Eget svar.']],
    ]);

    expect($html)
        ->toContain('Eget spørgsmål?')
        ->toContain('Eget svar.')
        ->not->toContain('Hvad koster det?');

    expect(substr_count($html, 'data-sigta="accordion.item"'))->toBe(1);
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section.faq data-test="faq" />');

    expect($html)->toContain('data-test="faq"');
});
