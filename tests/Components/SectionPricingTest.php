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
        ->toContain('data-scope="section.pricing" data-part="tiers"')
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

    expect(contractCss('section.pricing'))
        ->toContain('@apply grid gap-[clamp(18px,2.2vw,28px)] md:grid-cols-2;')
        // the annot overhangs the non-clipping panel, hidden on a phone
        ->toContain('@apply absolute -top-3 right-[8%] max-sm:hidden;');
});

it('renders the default tiers both runtimes share', function () {
    $html = Blade::render('<x-sigta::section.pricing />');

    foreach (\Sigta\Ui\Sections::get('pricing', 'tiers') as $tier) {
        expect($html)
            ->toContain(e($tier['title']))
            ->toContain(e($tier['figureSuffix']));
        foreach ($tier['body'] as $paragraph) {
            expect($html)->toContain(e($paragraph));
        }
    }
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
        ->toContain('data-scope="section.pricing" data-part="figure"')
        ->toContain('<span>40.000 kr.</span>')
        ->toContain('<small data-scope="section.pricing" data-part="suffix">+ 20.000 kr. til sparring</small>');

    expect(contractCss('section.pricing'))
        ->toContain('flex flex-wrap items-baseline')
        ->toContain('[data-scope="section.pricing"][data-part="suffix"] {')
        ->toContain('@apply font-mono text-[0.85rem] font-normal leading-snug text-graphite;');
});

it('spaces the body paragraphs, all but the last', function () {
    expect(Blade::render('<x-sigta::section.pricing />'))->toContain('data-scope="section.pricing" data-part="body"');

    expect(contractCss('section.pricing'))
        ->toContain('@apply text-[0.98rem] leading-[1.6] text-graphite;')
        ->toContain('&:not(:last-child) { @apply mb-4; }');
});

it('renders the aside slot beside the tiers in the same grid', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::section.pricing :tiers="[['title' => 'Projektpris', 'figure' => '60.000 kr.']]">
            <x-slot:aside class="text-center">I tvivl? Book en samtale.</x-slot:aside>
        </x-sigta::section.pricing>
        BLADE);

    expect($html)
        ->toContain('Projektpris')
        ->toContain('I tvivl? Book en samtale.')
        ->toContain('<div data-scope="section.pricing" data-part="aside" class="text-center">');

    expect(contractCss('section.pricing'))->toContain('@apply flex flex-col justify-center gap-4;');

    expect(strpos($html, 'I tvivl?'))->toBeGreaterThan(strpos($html, 'Projektpris'));
    expect(substr_count($html, 'data-sigta="panel"'))->toBe(1);
});

it('renders no aside without the slot', function () {
    $html = Blade::render('<x-sigta::section.pricing />');

    expect($html)->not->toContain('data-part="aside"');
});

it('forwards arbitrary attributes onto the section root', function () {
    $html = Blade::render('<x-sigta::section.pricing id="priser" data-test="pris" />');

    expect($html)
        ->toContain('id="priser"')
        ->toContain('data-test="pris"');
});
