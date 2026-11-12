<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: the ink band and no copy or buttons', function () {
    $html = Blade::render('<x-sigta::section.cta />');

    expect($html)
        ->toContain('data-sigta="section.cta"')
        ->toContain('bg-inverse')
        ->not->toContain('<h2')
        ->not->toContain('data-sigta="annot"')
        ->not->toContain('data-sigta="button"');
});

it('renders heading, lede, annotation and both buttons from props', function () {
    $html = Blade::render('<x-sigta::section.cta heading="Ready to start?" lede="We reply within a day." primary-label="Call now" primary-href="tel:12345678" secondary-label="Read more" secondary-href="/more" annotation="right here" />');

    expect($html)
        ->toContain('Ready to start?')
        ->toContain('We reply within a day.')
        ->toContain('Call now')
        ->toContain('tel:12345678')
        ->toContain('Read more')
        ->toContain('/more')
        ->toContain('right here')
        ->toContain('text-inverse-fg');
});

it('styles the dark band via button variants, not !-overrides', function () {
    $html = Blade::render('<x-sigta::section.cta heading="Go" primary-label="Book" primary-href="/book" secondary-label="Mail" secondary-href="mailto:hi@example.com" />');

    // The primary button uses variant="inverse" (paper fill = bg-surface),
    // the secondary uses variant="ghost-inverse" (border-inverse-line).
    expect($html)
        ->toContain('bg-surface')
        ->toContain('border-inverse-line')
        ->not->toContain('!');
});

it('forwards target onto the primary button like a normal button', function () {
    $html = Blade::render('<x-sigta::section.cta primary-label="Book" primary-href="/book" target="_blank" secondary-label="Mail" secondary-href="/mail" secondary-target="_blank" />');

    expect($html)
        ->toContain('target="_blank"')
        ->toContain('rel="noopener"');

    expect(substr_count($html, 'target="_blank"'))->toBe(2);
});

it('does not force a target when the prop is omitted', function () {
    $html = Blade::render('<x-sigta::section.cta primary-label="Book" primary-href="/book" />');

    expect($html)->not->toContain('target=');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section.cta data-test="cta" />');

    expect($html)->toContain('data-test="cta"');
});
