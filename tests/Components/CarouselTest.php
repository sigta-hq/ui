<?php

use Illuminate\Support\Facades\Blade;

it('renders a carousel region with snap slides', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::carousel label="Selected work">
            <x-sigta::carousel.slide>One</x-sigta::carousel.slide>
            <x-sigta::carousel.slide>Two</x-sigta::carousel.slide>
            <x-sigta::carousel.slide>Three</x-sigta::carousel.slide>
        </x-sigta::carousel>
    BLADE);

    expect($html)
        ->toContain('data-sigta="carousel"')
        ->toContain('role="region"')
        ->toContain('aria-roledescription="carousel"')
        ->toContain('aria-label="Selected work"')
        ->toContain('data-sigta="carousel.slide"')
        ->toContain('aria-roledescription="slide"')
        ->toContain('snap-start')
        ->toContain('One')
        ->toContain('Three');

    // one group role per slide, plus the dots group
    expect(substr_count($html, 'aria-roledescription="slide"'))->toBe(3);
});

it('renders prev/next controls and clickable dots', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::carousel>
            <x-sigta::carousel.slide>A</x-sigta::carousel.slide>
            <x-sigta::carousel.slide>B</x-sigta::carousel.slide>
        </x-sigta::carousel>
    BLADE);

    expect($html)
        ->toContain('aria-label="Previous slide"')
        ->toContain('aria-label="Next slide"')
        ->toContain('x-on:click="prev()"')
        ->toContain('x-on:click="next()"')
        ->toContain('x-bind:disabled="atStart()"')   // disabled at ends
        ->toContain('x-bind:disabled="atEnd()"')
        ->toContain('x-for="i in count"')            // dots generated from count
        ->toContain('aria-current');
});

it('accepts a basis width for multi-per-view slides', function () {
    $html = Blade::render('<x-sigta::carousel.slide basis="basis-1/2" data-testid="s">Half</x-sigta::carousel.slide>');

    expect($html)
        ->toContain('basis-1/2')
        ->toContain('data-testid="s"')   // attribute forwarding
        ->toContain('Half');
});
