<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: root marker, an empty grid and no header', function () {
    $html = Blade::render('<x-sigta::section.features />');

    expect($html)
        ->toContain('data-sigta="section.features"')
        ->toContain('data-sigta="tile-grid"')
        ->not->toContain('data-sigta="section-header"')
        ->not->toContain('data-sigta="tile"');
});

it('defaults to a four-column grid', function () {
    expect(Blade::render('<x-sigta::section.features />'))->toContain('lg:grid-cols-4');
});

it('renders the header and an item set via :items with an inline svg icon', function () {
    $html = Blade::render(
        '<x-sigta::section.features heading="What we do" lede="Four ways to work with us." :items="$items" />',
        ['items' => [
            ['num' => '01', 'icon' => '<svg data-custom-icon></svg>', 'title' => 'Strategy', 'text' => 'Description.'],
            ['num' => '02', 'icon' => 'code', 'title' => 'Development', 'text' => 'Hands-on.'],
        ]],
    );

    expect($html)
        ->toContain('data-sigta="section-header"')
        ->toContain('What we do')
        ->toContain('Four ways to work with us.')
        ->toContain('data-sigta="tile"')
        ->toContain('Strategy')
        ->toContain('Description.')
        ->toContain('data-custom-icon')
        ->toContain('Development')
        ->toContain('01')
        ->toContain('02');
});

it('forwards arbitrary attributes onto the section root', function () {
    $html = Blade::render('<x-sigta::section.features id="services" data-test="feat" />');

    expect($html)
        ->toContain('id="services"')
        ->toContain('data-test="feat"');
});
