<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: root marker + default Danish copy', function () {
    $html = Blade::render('<x-sigta::section.hero />');

    expect($html)
        ->toContain('data-sigta="section.hero"')
        // default eyebrow + heading (with mark) + lede
        ->toContain('Tech-partner · Hjælp, rådgivning og udvikling')
        ->toContain('forretning')
        ->toContain('data-sigta="mark"')
        ->toContain('Vi taler begge sprog, forretning og teknik.')
        // default CTAs
        ->toContain('Book en intro-samtale')
        ->toContain('Se hvordan vi arbejder')
        // default inline stats
        ->toContain('data-sigta="stat"')
        ->toContain('14 år')
        ->toContain('bag tastaturet')
        // default visual (søkort)
        ->toContain('sikker havn');
});

it('lets an override prop win over the default heading', function () {
    $html = Blade::render('<x-sigta::section.hero heading="Vi bygger fremtiden" />');

    expect($html)
        ->toContain('Vi bygger fremtiden')
        ->not->toContain('Teknologi,');
});

it('overrides lede and CTA labels', function () {
    $html = Blade::render('<x-sigta::section.hero lede="Kort lede" primary-label="Kontakt os" secondary-label="Læs mere" />');

    expect($html)
        ->toContain('Kort lede')
        ->toContain('Kontakt os')
        ->toContain('Læs mere')
        ->not->toContain('Se hvordan vi arbejder') // default secondary label swapped out
        ->toContain('data-sigta="stat"'); // stats still default
});

it('renders a custom stat row via :stats', function () {
    $html = Blade::render(
        '<x-sigta::section.hero :stats="$stats" />',
        ['stats' => [['value' => '99', 'label' => 'projekter']]],
    );

    expect($html)
        ->toContain('99')
        ->toContain('projekter')
        ->not->toContain('bag tastaturet');
});

it('renders a custom visual slot instead of the default søkort', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::section.hero>
            <x-slot:visual><img src="/hero.png" alt="Custom"></x-slot:visual>
        </x-sigta::section.hero>
    BLADE);

    expect($html)
        ->toContain('/hero.png')
        ->not->toContain('sikker havn');
});

it('forwards arbitrary attributes onto the section root', function () {
    $html = Blade::render('<x-sigta::section.hero id="top" data-test="hero" />');

    expect($html)
        ->toContain('id="top"')
        ->toContain('data-test="hero"');
});

it('opens the primary CTA in a new tab by default and lets target be overridden', function () {
    $default = Blade::render('<x-sigta::section.hero />');
    $overridden = Blade::render('<x-sigta::section.hero target="_self" />');

    expect($default)
        ->toContain('target="_blank"')
        ->toContain('rel="noopener"');

    expect($overridden)
        ->toContain('target="_self"')
        ->not->toContain('target="_blank"');
});
