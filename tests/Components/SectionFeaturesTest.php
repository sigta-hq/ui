<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: root marker + default Danish copy', function () {
    $html = Blade::render('<x-sigta::section.features />');

    expect($html)
        ->toContain('data-sigta="section.features"')
        // default heading + lede
        ->toContain('Sådan hjælper vi.')
        ->toContain('Fire måder at bruge en tech-partner på.')
        // composes section-header + tile-grid
        ->toContain('data-sigta="section-header"')
        ->toContain('data-sigta="tile-grid"')
        ->toContain('data-sigta="tile"')
        // all four default services
        ->toContain('Teknisk ledelse')
        ->toContain('Din mand i rummet')
        ->toContain('Teknisk vurdering')
        ->toContain('Hands-on udvikling')
        ->toContain('01')
        ->toContain('04');
});

it('defaults to a four-column grid', function () {
    expect(Blade::render('<x-sigta::section.features />'))->toContain('data-sigta="tile-grid" data-cols="4"');
});

it('lets an override prop win over the default heading', function () {
    $html = Blade::render('<x-sigta::section.features heading="Vores ydelser" />');

    expect($html)
        ->toContain('Vores ydelser')
        ->not->toContain('Sådan hjælper vi.');
});

it('renders a custom item set via :items with an inline svg icon', function () {
    $html = Blade::render(
        '<x-sigta::section.features :items="$items" />',
        ['items' => [
            ['num' => 'A', 'icon' => '<svg data-custom-icon></svg>', 'title' => 'Egen ydelse', 'text' => 'Beskrivelse.'],
        ]],
    );

    expect($html)
        ->toContain('Egen ydelse')
        ->toContain('Beskrivelse.')
        ->toContain('data-custom-icon')
        ->not->toContain('Teknisk ledelse');
});

it('forwards arbitrary attributes onto the section root', function () {
    $html = Blade::render('<x-sigta::section.features id="hjaelp" data-test="feat" />');

    expect($html)
        ->toContain('id="hjaelp"')
        ->toContain('data-test="feat"');
});
