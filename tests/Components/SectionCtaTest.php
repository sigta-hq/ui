<?php

use Illuminate\Support\Facades\Blade;

it('renders the default ink band copy and both buttons', function () {
    $html = Blade::render('<x-sigta::section.cta />');

    expect($html)
        ->toContain('data-sigta="section.cta"')
        ->toContain('Skal vi tage en snak, før du skriver under på noget?')
        ->toContain('En intro-samtale koster ingenting og forpligter ikke.')
        ->toContain('Book en intro-samtale')
        ->toContain('Skriv en mail')
        ->toContain('lad os tegne det op ↘')
        ->toContain('bg-inverse')
        ->toContain('text-inverse-fg');
});

it('styles the dark band via button variants, not !-overrides', function () {
    $html = Blade::render('<x-sigta::section.cta />');

    // The primary button uses variant="inverse" (paper fill), the secondary
    // uses variant="ghost-inverse" (hairline outline).
    expect($html)
        ->toContain('data-variant="inverse"')
        ->toContain('data-variant="ghost-inverse"')
        ->not->toContain('!');
});

it('lets the heading and label/href props win', function () {
    $html = Blade::render('<x-sigta::section.cta heading="Egen overskrift" primary-label="Ring nu" primary-href="tel:12345678" secondary-label="Læs mere" secondary-href="/mere" />');

    expect($html)
        ->toContain('Egen overskrift')
        ->toContain('Ring nu')
        ->toContain('tel:12345678')
        ->toContain('Læs mere')
        ->toContain('/mere')
        ->not->toContain('Skal vi tage en snak');
});

it('forwards target onto the primary button like a normal button', function () {
    $html = Blade::render('<x-sigta::section.cta target="_blank" secondary-target="_blank" />');

    expect($html)
        ->toContain('target="_blank"')
        ->toContain('rel="noopener"');

    expect(substr_count($html, 'target="_blank"'))->toBe(2);
});

it('does not force a target when the prop is omitted', function () {
    $html = Blade::render('<x-sigta::section.cta />');

    expect($html)->not->toContain('target=');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section.cta data-test="cta" />');

    expect($html)->toContain('data-test="cta"');
});
