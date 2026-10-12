<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: root marker + default Danish copy', function () {
    $html = Blade::render('<x-sigta::section.pricing />');

    expect($html)
        ->toContain('data-sigta="section.pricing"')
        // default heading + lede
        ->toContain('To måder at samarbejde på.')
        ->toContain('du skal altid vide, hvad du betaler for.')
        // composes section-header + two non-clipping panels
        ->toContain('data-sigta="section-header"')
        ->toContain('data-sigta="panel"')
        ->toContain('md:grid-cols-2')
        // both tiers
        ->toContain('Spor 1 · Timepris')
        ->toContain('Klassisk: én lav, fast timepris')
        ->toContain('Spor 2 · Fast månedspris')
        ->toContain('Skræddersyet software som abonnement')
        // marked price figure
        ->toContain('data-sigta="mark"')
        ->toContain('800 kr.')
        ->toContain('/ time, ekskl. moms')
        // overhanging annot on the second panel
        ->toContain('data-sigta="annot"')
        ->toContain('som SaaS, bare jeres eget');
});

it('lets an override prop win over the default heading', function () {
    $html = Blade::render('<x-sigta::section.pricing heading="Priser og pakker" />');

    expect($html)
        ->toContain('Priser og pakker')
        ->not->toContain('To måder at samarbejde på.');
});

it('renders a custom tier set via :tiers, marking the figure when asked', function () {
    $html = Blade::render(
        '<x-sigta::section.pricing :tiers="$tiers" />',
        ['tiers' => [
            [
                'label' => 'Fast pris',
                'title' => 'Projektpris',
                'figure' => '50.000 kr.',
                'figureSuffix' => '/ projekt',
                'mark' => true,
                'body' => ['Én samlet pris.'],
            ],
        ]],
    );

    expect($html)
        ->toContain('Projektpris')
        ->toContain('50.000 kr.')
        ->toContain('data-sigta="mark"')
        ->toContain('Én samlet pris.')
        ->not->toContain('Spor 1 · Timepris');
});

it('sets the figure suffix as its own wrapping block, not inline in the figure line', function () {
    $html = Blade::render(
        '<x-sigta::section.pricing :tiers="$tiers" />',
        ['tiers' => [
            [
                'figure' => '40.000 kr.',
                'figureSuffix' => '+ 20.000 kr. til sparring',
            ],
        ]],
    );

    expect($html)
        ->toContain('flex flex-wrap items-baseline')
        ->toContain('<span>40.000 kr.</span>')
        ->toContain('<small class="font-mono text-[0.85rem] font-normal leading-snug text-graphite">+ 20.000 kr. til sparring</small>');
});

it('forwards arbitrary attributes onto the section root', function () {
    $html = Blade::render('<x-sigta::section.pricing id="priser" data-test="pris" />');

    expect($html)
        ->toContain('id="priser"')
        ->toContain('data-test="pris"');
});
