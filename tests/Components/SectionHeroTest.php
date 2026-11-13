<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: root marker and no copy, buttons, stats or visual', function () {
    $html = Blade::render('<x-sigta::section.hero />');

    expect($html)
        ->toContain('data-sigta="section.hero"')
        ->not->toContain('data-sigta="eyebrow"')
        ->not->toContain('<h1')
        ->not->toContain('data-sigta="button"')
        ->not->toContain('data-sigta="stat"')
        ->not->toContain('lg:grid-cols-[1.15fr_0.85fr]');
});

it('renders eyebrow, heading, lede, both CTAs and the stat row from props', function () {
    $html = Blade::render(
        '<x-sigta::section.hero eyebrow="Studio" heading="We build software" lede="Short lede" primary-label="Book a call" primary-href="/book" secondary-label="Our work" secondary-href="#work" :stats="$stats" />',
        ['stats' => [['value' => '99', 'label' => 'projects']]],
    );

    expect($html)
        ->toContain('Studio')
        ->toContain('<h1')
        ->toContain('We build software')
        ->toContain('Short lede')
        ->toContain('Book a call')
        ->toContain('href="/book"')
        ->toContain('Our work')
        ->toContain('href="#work"')
        ->toContain('data-sigta="stat"')
        ->toContain('projects');
});

it('takes rich heading HTML from the default slot', function () {
    $html = Blade::render('<x-sigta::section.hero>Ship <x-sigta::mark>faster</x-sigta::mark>.</x-sigta::section.hero>');

    expect($html)
        ->toContain('<h1')
        ->toContain('data-sigta="mark"')
        ->toContain('faster');
});

it('sets the visual slot beside the copy in a two-column grid', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::section.hero heading="Hello">
            <x-slot:visual><img src="/hero.png" alt="Custom"></x-slot:visual>
        </x-sigta::section.hero>
    BLADE);

    expect($html)
        ->toContain('/hero.png')
        ->toContain('lg:grid-cols-[1.15fr_0.85fr]');
});

it('forwards arbitrary attributes onto the section root', function () {
    $html = Blade::render('<x-sigta::section.hero id="top" data-test="hero" />');

    expect($html)
        ->toContain('id="top"')
        ->toContain('data-test="hero"');
});

it('opens the primary CTA in a new tab only when target asks for it', function () {
    $default = Blade::render('<x-sigta::section.hero primary-label="Book" primary-href="/book" />');
    $blank = Blade::render('<x-sigta::section.hero primary-label="Book" primary-href="/book" target="_blank" />');

    expect($default)->not->toContain('target=');

    expect($blank)
        ->toContain('target="_blank"')
        ->toContain('rel="noopener"');
});
