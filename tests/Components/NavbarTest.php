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
        ->toContain('sticky top-0 z-50');
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
        ->toContain('font-mono')
        ->toContain('after:right-full')
        ->not->toContain('aria-current');

    expect($current)
        ->toContain('aria-current="page"')
        ->toContain('text-ink')
        ->toContain('after:right-0');
});

it('forwards attributes on navlinks', function () {
    $html = Blade::render('<x-sigta::navlink href="/work" wire:navigate data-test="link">Work</x-sigta::navlink>');

    expect($html)
        ->toContain('wire:navigate')
        ->toContain('data-test="link"');
});
