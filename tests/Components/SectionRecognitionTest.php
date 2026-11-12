<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: root marker, an empty grid, no header or CTA', function () {
    $html = Blade::render('<x-sigta::section.recognition />');

    expect($html)
        ->toContain('data-sigta="section.recognition"')
        ->toContain('data-sigta="tile-grid"')
        ->not->toContain('data-sigta="section-header"')
        ->not->toContain('data-sigta="tile"')
        ->not->toContain('data-sigta="button"');
});

it('renders the header, cards, closing line and CTA from props', function () {
    $html = Blade::render(
        '<x-sigta::section.recognition heading="Sound familiar?" lede="Four situations." :items="$items" closing-text="If one fits, let us talk." cta-label="Book a call" cta-href="/book" />',
        ['items' => [
            ['num' => '01 · The quote', 'scene' => 'We got a quote. Is it fair?', 'answer' => 'We read it with you.'],
        ]],
    );

    expect($html)
        ->toContain('data-sigta="section-header"')
        ->toContain('Sound familiar?')
        ->toContain('Four situations.')
        ->toContain('md:grid-cols-2')
        ->toContain('01 · The quote')
        ->toContain('We got a quote. Is it fair?')
        ->toContain('We read it with you.')
        ->toContain('If one fits, let us talk.')
        ->toContain('Book a call')
        ->toContain('href="/book"');
});

it('forwards arbitrary attributes onto the section root', function () {
    $html = Blade::render('<x-sigta::section.recognition id="familiar" data-test="recog" />');

    expect($html)
        ->toContain('id="familiar"')
        ->toContain('data-test="recog"');
});

it('opens the CTA in a new tab only when target asks for it', function () {
    $default = Blade::render('<x-sigta::section.recognition cta-label="Book" cta-href="/book" />');
    $blank = Blade::render('<x-sigta::section.recognition cta-label="Book" cta-href="/book" target="_blank" />');

    expect($default)->not->toContain('target=');

    expect($blank)
        ->toContain('target="_blank"')
        ->toContain('rel="noopener"');
});
