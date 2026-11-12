<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: root marker, one column, no copy or portrait', function () {
    $html = Blade::render('<x-sigta::section.about />');

    expect($html)
        ->toContain('data-sigta="section.about"')
        ->not->toContain('md:grid-cols-[0.8fr_1.2fr]')
        ->not->toContain('<h2')
        ->not->toContain('<svg');
});

it('renders the eyebrow, heading and paragraphs from props', function () {
    $html = Blade::render(
        '<x-sigta::section.about eyebrow="About us" heading="A small studio" :paragraphs="$p" />',
        ['p' => ['First paragraph.', 'Second paragraph.']],
    );

    expect($html)
        ->toContain('About us')
        ->toContain('A small studio')
        ->toContain('First paragraph.')
        ->toContain('Second paragraph.');
});

it('sets the portrait slot beside the text in two columns', function () {
    $html = Blade::render('<x-sigta::section.about heading="Hi"><x-slot:portrait>OUR PORTRAIT</x-slot:portrait></x-sigta::section.about>');

    expect($html)
        ->toContain('OUR PORTRAIT')
        ->toContain('md:grid-cols-[0.8fr_1.2fr]');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section.about data-test="about" />');

    expect($html)->toContain('data-test="about"');
});
