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
        ->toContain('data-variant="1"')
        ->toContain('aria-hidden="true"')
        ->toContain('data-scope="rule" data-part="line"')
        ->toContain('viewBox="0 0 1180 14"')
        ->toContain('d="M0 8 C140 4 260 11 420 7');

    expect(contractCss('rule'))->toContain('@apply block w-full h-3.5 text-line;');
});

it('wraps the rule variant round its three paths', function () {
    expect(Blade::render('<x-sigta::rule variant="2" />'))->toContain('d="M0 7 C160 11');
    expect(Blade::render('<x-sigta::rule :variant="4" />'))->toContain('d="M0 8 C140 4');
    expect(Blade::render('<x-sigta::rule :variant="0" />'))->toContain('d="M0 8 C140 4');
});

it('renders the separator as a plain hairline', function () {
    expect(Blade::render('<x-sigta::separator />'))
        ->toContain('<hr data-sigta="separator" data-variant="solid"');
    expect(Blade::render('<x-sigta::separator variant="dashed" />'))
        ->toContain('data-variant="dashed"');

    expect(contractCss('separator'))
        ->toContain('@apply border-0 border-t border-line;')
        ->toContain('&[data-variant="dashed"] { @apply border-dashed; }');
});

it('renders the highlighter mark', function () {
    expect(Blade::render('<x-sigta::mark>shipped</x-sigta::mark>'))
        ->toContain('data-sigta="mark"')
        ->toContain('shipped');
});

it('renders the highlighter swipe from the contract stylesheet', function () {
    expect(contractCss('mark'))->toContain('after:bg-accent');
});

it('renders annotations with rotation', function () {
    expect(Blade::render('<x-sigta::annot rotate="4">drawn, not generated</x-sigta::annot>'))
        ->toContain('rotate(4deg)')
        ->toContain('data-tone="ink"')
        ->toContain('aria-hidden="true"');

    expect(contractCss('annot'))->toContain('@apply font-hand font-semibold leading-none text-[1.35rem] inline-block;');
});

it('resolves the annotation tone, keeping muted for back-compat', function () {
    expect(Blade::render('<x-sigta::annot muted>note</x-sigta::annot>'))->toContain('data-tone="muted"');
    expect(Blade::render('<x-sigta::annot tone="accent" muted>note</x-sigta::annot>'))->toContain('data-tone="accent"');
    expect(Blade::render('<x-sigta::annot :decorative="false">note</x-sigta::annot>'))->not->toContain('aria-hidden');

    expect(contractCss('annot'))
        ->toContain('&[data-tone="muted"] { @apply text-graphite; }')
        ->toContain('&[data-tone="accent"] { @apply text-accent; }');
});

it('renders the eyebrow kicker', function () {
    expect(Blade::render('<x-sigta::eyebrow>Design studio</x-sigta::eyebrow>'))
        ->toContain('data-sigta="eyebrow"')
        ->toContain('Design studio');

    expect(contractCss('eyebrow'))->toContain('uppercase');
});

it('renders the brand lockup with currentColor only', function () {
    $html = Blade::render('<x-sigta::brand />');

    expect($html)
        ->toContain('data-sigta="brand"')
        ->toContain('data-size="base"')
        ->toContain('data-scope="brand" data-part="glyph"')
        ->toContain('data-scope="brand" data-part="name">SIGTA')
        ->toContain('currentColor')
        ->not->toMatch('/#[0-9a-fA-F]{3,8}/');
});

it('renders the brand glyph alone', function () {
    expect(Blade::render('<x-sigta::brand glyph-only size="sm" />'))
        ->toContain('data-size="sm"')
        ->not->toContain('data-part="name"');

    expect(contractCss('brand'))->toContain('[data-sigta="brand"][data-size="sm"] > & { @apply size-[22px]; }');
});

it('renders the paper overlay', function () {
    expect(Blade::render('<x-sigta::paper />'))
        ->toContain('data-sigta="paper"')
        ->toContain('data-scope="paper" data-part="dots"')
        ->toContain('data-scope="paper" data-part="grain"')
        ->toContain('feTurbulence');

    expect(Blade::render('<x-sigta::paper :dots="false" :grain="false" />'))
        ->not->toContain('data-part="dots"')
        ->not->toContain('feTurbulence');

    expect(contractCss('paper'))->toContain('@apply pointer-events-none fixed inset-0 z-0;');
});
