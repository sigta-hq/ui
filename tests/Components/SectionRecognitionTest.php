<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: root marker + default Danish copy', function () {
    $html = Blade::render('<x-sigta::section.recognition />');

    expect($html)
        ->toContain('data-sigta="section.recognition"')
        // default heading + lede
        ->toContain('Kan du genkende det?')
        ->toContain('Fire situationer, hvor det hjælper at have en teknisk stemme')
        // composes section-header + two-col tile-grid
        ->toContain('data-sigta="section-header"')
        ->toContain('data-sigta="tile-grid"')
        ->toContain('md:grid-cols-2')
        // scene + answer copy from the default cards
        ->toContain('Tilbuddet i indbakken')
        ->toContain('Vi har fået et tilbud på 750.000 kr.')
        ->toContain('linje for linje')
        ->toContain('Vokset fra gaffatapen')
        // closing line + default CTA
        ->toContain('Hvis bare ét af dem rammer, så lad os tage en kop kaffe.')
        ->toContain('Book en intro-samtale');
});

it('lets an override prop win over the default heading', function () {
    $html = Blade::render('<x-sigta::section.recognition heading="Lyder det bekendt?" />');

    expect($html)
        ->toContain('Lyder det bekendt?')
        ->not->toContain('Kan du genkende det?');
});

it('overrides closing text and CTA', function () {
    $html = Blade::render('<x-sigta::section.recognition closing-text="Ring til os" cta-label="Skriv en mail" />');

    expect($html)
        ->toContain('Ring til os')
        ->toContain('Skriv en mail')
        ->not->toContain('Hvis bare ét af dem rammer');
});

it('renders a custom card set via :items', function () {
    $html = Blade::render(
        '<x-sigta::section.recognition :items="$items" />',
        ['items' => [
            ['num' => '01', 'scene' => 'Egen scene', 'answer' => 'Eget svar'],
        ]],
    );

    expect($html)
        ->toContain('Egen scene')
        ->toContain('Eget svar')
        ->not->toContain('Tilbuddet i indbakken');
});

it('forwards arbitrary attributes onto the section root', function () {
    $html = Blade::render('<x-sigta::section.recognition id="genkend" data-test="recog" />');

    expect($html)
        ->toContain('id="genkend"')
        ->toContain('data-test="recog"');
});
