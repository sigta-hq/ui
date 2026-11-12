<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: root marker, no header and no tiers', function () {
    $html = Blade::render('<x-sigta::section.pricing />');

    expect($html)
        ->toContain('data-sigta="section.pricing"')
        ->not->toContain('data-sigta="section-header"')
        ->not->toContain('data-sigta="panel"');
});

it('renders the header and a tier set via :tiers, marking the figure when asked', function () {
    $html = Blade::render(
        '<x-sigta::section.pricing heading="Pricing" lede="Always know what you pay for." :tiers="$tiers" />',
        ['tiers' => [
            [
                'label' => 'Track 1',
                'title' => 'Hourly',
                'figure' => '$120',
                'figureSuffix' => '/ hour',
                'mark' => true,
                'body' => ['One rate.'],
            ],
            [
                'label' => 'Track 2',
                'title' => 'Subscription',
                'figure' => 'Fixed',
                'annot' => 'like SaaS',
                'body' => ['A fixed monthly price.'],
            ],
        ]],
    );

    expect($html)
        ->toContain('data-sigta="section-header"')
        ->toContain('Pricing')
        ->toContain('Always know what you pay for.')
        ->toContain('md:grid-cols-2')
        ->toContain('Track 1')
        ->toContain('Hourly')
        ->toContain('$120')
        ->toContain('data-sigta="mark"')
        ->toContain('One rate.')
        ->toContain('data-sigta="annot"')
        ->toContain('like SaaS');

    expect(substr_count($html, 'data-sigta="panel"'))->toBe(2);
});

it('sets the figure suffix as its own wrapping block, not inline in the figure line', function () {
    $html = Blade::render(
        '<x-sigta::section.pricing :tiers="$tiers" />',
        ['tiers' => [
            [
                'figure' => '$4,000',
                'figureSuffix' => '+ $2,000 for advisory',
            ],
        ]],
    );

    expect($html)
        ->toContain('flex flex-wrap items-baseline')
        ->toContain('<span>$4,000</span>')
        ->toContain('<small class="font-mono text-[0.85rem] font-normal leading-snug text-graphite">+ $2,000 for advisory</small>');
});

it('renders the aside slot beside the tiers in the same grid', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::section.pricing :tiers="[['title' => 'Project price', 'figure' => '$6,000']]">
            <x-slot:aside class="text-center">Unsure? Book a call.</x-slot:aside>
        </x-sigta::section.pricing>
        BLADE);

    expect($html)
        ->toContain('Project price')
        ->toContain('Unsure? Book a call.')
        ->toContain('class="flex flex-col justify-center gap-4 text-center"');

    expect(strpos($html, 'Unsure?'))->toBeGreaterThan(strpos($html, 'Project price'));
    expect(substr_count($html, 'data-sigta="panel"'))->toBe(1);
});

it('renders no aside without the slot', function () {
    $html = Blade::render('<x-sigta::section.pricing />');

    expect($html)->not->toContain('flex flex-col justify-center gap-4');
});

it('forwards arbitrary attributes onto the section root', function () {
    $html = Blade::render('<x-sigta::section.pricing id="pricing" data-test="pris" />');

    expect($html)
        ->toContain('id="pricing"')
        ->toContain('data-test="pris"');
});
