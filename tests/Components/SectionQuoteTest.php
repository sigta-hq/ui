<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: root marker and no quote or cite', function () {
    $html = Blade::render('<x-sigta::section.quote />');

    expect($html)
        ->toContain('data-sigta="section.quote"')
        ->not->toContain('<blockquote')
        ->not->toContain('uppercase');
});

it('renders the quote and cite from props', function () {
    $html = Blade::render('<x-sigta::section.quote quote="Good craft speaks for itself." cite="Our philosophy" />');

    expect($html)
        ->toContain('<blockquote')
        ->toContain('font-head')
        ->toContain('italic')
        ->toContain('Good craft speaks for itself.')
        ->toContain('Our philosophy');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section.quote data-test="quote" />');

    expect($html)->toContain('data-test="quote"');
});
