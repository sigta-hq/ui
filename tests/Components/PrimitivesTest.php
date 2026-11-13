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
        ->toContain('text-graphite')
        ->toContain('Intro');
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

it('renders an empty brand lockup with no built-in logo or name', function () {
    $html = Blade::render('<x-sigta::brand />');

    expect($html)
        ->toContain('data-sigta="brand"')
        ->not->toContain('<svg')
        ->not->toContain('<img');
});

it('renders the brand lockup from a logo slot and a name', function () {
    $html = Blade::render('<x-sigta::brand name="Acme" size="lg"><x-slot:logo><svg viewBox="0 0 40 40"><circle stroke="currentColor" /></svg></x-slot:logo></x-sigta::brand>');

    expect($html)
        ->toContain('Acme')
        ->toContain('size-[38px] shrink-0 [&>svg]:size-full')
        ->toContain('currentColor')
        ->not->toMatch('/#[0-9a-fA-F]{3,8}/');
});

it('renders the brand logo from an image URL and hides the name when glyph-only', function () {
    $html = Blade::render('<x-sigta::brand name="Acme" logo="/logo.svg" glyph-only />');

    expect($html)
        ->toContain('<img src="/logo.svg" alt=""')
        ->not->toContain('Acme');
});

it('renders the paper overlay', function () {
    expect(Blade::render('<x-sigta::paper />'))
        ->toContain('data-sigta="paper"')
        ->toContain('feTurbulence');
});
