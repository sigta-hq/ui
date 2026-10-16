<?php

use Illuminate\Support\Facades\Blade;

it('renders an icon with brand stroke width', function () {
    $html = Blade::render('<x-sigta::icon name="arrow-right" />');

    expect($html)
        ->toContain('<svg')
        ->toContain('stroke-width="1.75"')
        ->toContain('aria-hidden="true"');
});

it('renders headings at the requested level', function () {
    expect(Blade::render('<x-sigta::heading level="1">Title</x-sigta::heading>'))
        ->toContain('<h1')
        ->toContain('Title');

    expect(Blade::render('<x-sigta::heading level="3" size="display">Big</x-sigta::heading>'))
        ->toContain('<h3');
});

it('renders text variants', function () {
    expect(Blade::render('<x-sigta::text variant="lede">Intro</x-sigta::text>'))
        ->toContain('data-variant="lede"')
        ->toContain('Intro');

    expect(contractCss('text'))->toContain('&[data-variant="lede"] { @apply text-[clamp(1.05rem,1.6vw,1.3rem)] text-graphite');
});

it('renders the hand-drawn rule', function () {
    expect(Blade::render('<x-sigta::rule />'))
        ->toContain('data-sigta="rule"')
        ->toContain('aria-hidden="true"')
        ->toContain('viewBox="0 0 1180 14"');
});

it('renders the highlighter mark', function () {
    expect(Blade::render('<x-sigta::mark>shipped</x-sigta::mark>'))
        ->toContain('data-sigta="mark"')
        ->toContain('shipped');
});

it('renders annotations with rotation', function () {
    expect(Blade::render('<x-sigta::annot rotate="4">drawn, not generated</x-sigta::annot>'))
        ->toContain('rotate(4deg)')
        ->toContain('aria-hidden="true"');
});

it('renders the eyebrow kicker', function () {
    expect(Blade::render('<x-sigta::eyebrow>Design studio</x-sigta::eyebrow>'))
        ->toContain('data-sigta="eyebrow"')
        ->toContain('uppercase');
});

it('renders the brand lockup with currentColor only', function () {
    $html = Blade::render('<x-sigta::brand />');

    expect($html)
        ->toContain('data-sigta="brand"')
        ->toContain('SIGTA')
        ->toContain('currentColor')
        ->not->toMatch('/#[0-9a-fA-F]{3,8}/');
});

it('renders the paper overlay', function () {
    expect(Blade::render('<x-sigta::paper />'))
        ->toContain('data-sigta="paper"')
        ->toContain('feTurbulence');
});
