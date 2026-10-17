<?php

use Illuminate\Support\Facades\Blade;

it('renders an info callout by default', function () {
    $html = Blade::render('<x-sigta::callout>Two seats are unassigned.</x-sigta::callout>');

    expect($html)
        ->toContain('Two seats are unassigned.')
        ->toContain('data-sigta="callout"')
        ->toContain('data-variant="info"')
        ->toContain('role="status"')
        ->toContain('<svg')
        ->toContain('data-scope="callout" data-part="icon"')
        ->toContain('aria-hidden="true"');

    expect(contractCss('callout'))
        ->toContain('border-l-4')
        ->toContain('[data-variant="danger"])) { @apply border-ink; }');
});

it('renders the success variant with success border and icon color', function () {
    $html = Blade::render('<x-sigta::callout variant="success">Deploy complete.</x-sigta::callout>');

    expect($html)
        ->toContain('data-variant="success"')
        ->toContain('role="status"');

    expect(contractCss('callout'))
        ->toContain('&[data-variant="success"] { @apply border-success; }')
        ->toContain('[data-sigta="callout"][data-variant="success"] > & { @apply text-success; }');
});

it('renders the danger variant as an alert', function () {
    $html = Blade::render('<x-sigta::callout variant="danger">Payment failed.</x-sigta::callout>');

    expect($html)
        ->toContain('data-variant="danger"')
        ->toContain('role="alert"');

    expect(contractCss('callout'))
        ->toContain('&[data-variant="danger"] { @apply border-danger; }')
        ->toContain('[data-sigta="callout"][data-variant="danger"] > & { @apply text-danger; }');
});

it('renders the heading as a bold Fraunces lead', function () {
    $html = Blade::render('<x-sigta::callout heading="Heads up.">Trial ends Friday.</x-sigta::callout>');

    expect($html)
        ->toContain('<b data-scope="callout" data-part="heading">Heads up.</b> Trial ends Friday.');

    expect(contractCss('callout'))->toContain('[data-scope="callout"][data-part="heading"] {'."\n".'    @apply font-head font-semibold;');
});

it('accepts an icon override', function () {
    $default = Blade::render('<x-sigta::callout>Note</x-sigta::callout>');
    $overridden = Blade::render('<x-sigta::callout icon="info">Note</x-sigta::callout>');

    expect($overridden)->toContain('<svg');
    expect($overridden)->not->toBe($default);
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::callout wire:key="notice" data-test="callout" role="note">Body</x-sigta::callout>');

    expect($html)
        ->toContain('wire:key="notice"')
        ->toContain('data-test="callout"')
        ->toContain('role="note"')
        ->not->toContain('role="status"');
});
