<?php

use Illuminate\Support\Facades\Blade;

it('renders an info callout by default', function () {
    $html = Blade::render('<x-sigta::callout>Two seats are unassigned.</x-sigta::callout>');

    expect($html)
        ->toContain('Two seats are unassigned.')
        ->toContain('data-sigta="callout"')
        ->toContain('border-ink')
        ->toContain('border-l-4')
        ->toContain('role="status"')
        ->toContain('<svg')
        ->toContain('aria-hidden="true"');
});

it('renders the success variant with success border and icon color', function () {
    $html = Blade::render('<x-sigta::callout variant="success">Deploy complete.</x-sigta::callout>');

    expect($html)
        ->toContain('border-success')
        ->toContain('text-success')
        ->toContain('role="status"');
});

it('renders the danger variant as an alert', function () {
    $html = Blade::render('<x-sigta::callout variant="danger">Payment failed.</x-sigta::callout>');

    expect($html)
        ->toContain('border-danger')
        ->toContain('text-danger')
        ->toContain('role="alert"');
});

it('renders the heading as a bold Fraunces lead', function () {
    $html = Blade::render('<x-sigta::callout heading="Heads up.">Trial ends Friday.</x-sigta::callout>');

    expect($html)
        ->toContain('font-head')
        ->toContain('Heads up.')
        ->toContain('Trial ends Friday.');

    expect(strpos($html, 'Heads up.'))->toBeLessThan(strpos($html, 'Trial ends Friday.'));
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
