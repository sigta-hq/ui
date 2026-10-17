<?php

use Illuminate\Support\Facades\Blade;

it('renders the navbar with all three slots', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::navbar>
            <x-slot:brand><x-sigta::brand /></x-slot:brand>
            <x-sigta::navlink href="/work">Work</x-sigta::navlink>
            <x-slot:actions><x-sigta::button variant="cta">Book</x-sigta::button></x-slot:actions>
        </x-sigta::navbar>
        BLADE);

    expect($html)
        ->toContain('data-sigta="navbar"')
        ->toContain('<header')
        ->toContain('data-sigta="brand"')
        ->toContain('Work')
        ->toContain('Book')
        ->toContain('data-scope="navbar" data-part="bar"')
        ->toContain('data-scope="navbar" data-part="brand"')
        ->toContain('data-scope="navbar" data-part="actions"')
        ->not->toMatch('/<header[^>]*class=/');

    expect(contractCss('navbar'))->toContain('sticky top-0 z-50');
});

it('wires the mobile toggle with aria-expanded and a controlled panel', function () {
    $html = Blade::render('<x-sigta::navbar><x-sigta::navlink href="/">Home</x-sigta::navlink></x-sigta::navbar>');

    expect($html)
        ->toContain('aria-expanded="false"')
        ->toContain('x-data="{ open: false }')
        ->toContain('aria-controls="');

    // aria-controls points at the panel id.
    preg_match('/aria-controls="([^"]+)"/', $html, $m);
    expect($html)->toContain('id="'.$m[1].'"');
});

it('folds the links behind the burger below md by default', function () {
    $html = Blade::render('<x-sigta::navbar><x-sigta::navlink href="/">Home</x-sigta::navlink></x-sigta::navbar>');

    expect($html)
        ->toContain('data-collapse="md"')
        ->toContain('data-scope="navbar" data-part="links"')
        ->toMatch('/data-scope="navbar"\s+data-part="toggle"/')
        ->toMatch('/data-scope="navbar"\s+data-part="panel"/');

    // Links show from md, the burger and its panel only below it.
    $css = contractCss('navbar');
    expect($css)
        ->toMatch('/\[data-part="links"\] \{\s*@apply hidden md:flex items-center gap-8;/')
        ->toMatch('/\[data-part="toggle"\] \{\s*@apply md:hidden inline-flex/')
        ->toMatch('/\[data-part="panel"\] \{\s*@apply md:hidden border-t/');
});

it('folds the links behind the burger below lg when collapse is lg', function () {
    $html = Blade::render('<x-sigta::navbar collapse="lg"><x-sigta::navlink href="/">Home</x-sigta::navlink></x-sigta::navbar>');

    expect($html)->toContain('data-collapse="lg"')->not->toContain('data-collapse="md"');

    // With collapse lg the bar's links wait for lg, and the burger and panel stay up to it.
    $css = contractCss('navbar');
    expect($css)
        ->toContain('[data-sigta="navbar"][data-collapse="lg"] > [data-part="bar"] > & { @apply hidden lg:flex; }')
        ->toContain('[data-sigta="navbar"][data-collapse="lg"] > [data-part="bar"] > [data-part="end"] > & { @apply inline-flex lg:hidden; }')
        ->toContain('[data-sigta="navbar"][data-collapse="lg"] > & { @apply flex lg:hidden; }');
});

it('renders the CTA as a plain button when it has no href', function () {
    $html = Blade::render('<x-sigta::navbar cta-label="Book" />');

    expect($html)->toMatch('/<button[^>]*data-sigta="button"/')->not->toContain('<a data-sigta="button"');
});

it('forwards attributes onto the header root', function () {
    $html = Blade::render('<x-sigta::navbar data-test="nav" wire:key="topnav">Links</x-sigta::navbar>');

    expect($html)
        ->toContain('data-test="nav"')
        ->toContain('wire:key="topnav"');
});

it('renders a CTA button from props and forwards target like a normal button', function () {
    $html = Blade::render('<x-sigta::navbar cta-label="Book" cta-href="https://calendly.com/sf-sigta/30min" target="_blank" />');

    expect($html)
        ->toContain('Book')
        ->toContain('href="https://calendly.com/sf-sigta/30min"')
        ->toContain('target="_blank"')
        ->toContain('rel="noopener"');

    expect($html)->not->toMatch('/<header[^>]*target=/');
});

it('renders navlinks with the animated underline and current state', function () {
    $plain = Blade::render('<x-sigta::navlink href="/work">Work</x-sigta::navlink>');
    $current = Blade::render('<x-sigta::navlink href="/work" current>Work</x-sigta::navlink>');

    expect($plain)
        ->toContain('data-sigta="navlink"')
        ->toContain('href="/work"')
        ->not->toContain('class=')
        ->not->toContain('aria-current');

    expect($current)->toContain('aria-current="page"');

    // The underline grows on hover; the current page keeps it drawn.
    expect(contractCss('navlink'))
        ->toContain('font-mono')
        ->toMatch('/&\[aria-current="page"\] \{\s*@apply text-ink after:right-0;/')
        ->toMatch('/&:not\(\[aria-current="page"\]\) \{\s*@apply text-graphite hover:text-ink after:right-full hover:after:right-0;/');
});

it('forwards attributes on navlinks', function () {
    $html = Blade::render('<x-sigta::navlink href="/work" wire:navigate data-test="link">Work</x-sigta::navlink>');

    expect($html)
        ->toContain('wire:navigate')
        ->toContain('data-test="link"');
});
