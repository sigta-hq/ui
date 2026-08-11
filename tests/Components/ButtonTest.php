<?php

use Illuminate\Support\Facades\Blade;

it('renders a button with its slot content', function () {
    $html = Blade::render('<x-sigta::button variant="primary">Save draft</x-sigta::button>');

    expect($html)
        ->toContain('Save draft')
        ->toContain('data-sigta="button"')
        ->toContain('<button')
        ->toContain('type="button"');
});

it('renders as an anchor when href is present', function () {
    $html = Blade::render('<x-sigta::button href="/contact">Book a project</x-sigta::button>');

    expect($html)
        ->toContain('<a')
        ->toContain('href="/contact"')
        ->not->toContain('<button');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::button wire:click="save" data-test="cta">Go</x-sigta::button>');

    expect($html)
        ->toContain('wire:click="save"')
        ->toContain('data-test="cta"');
});

it('renders the inverse variant as a paper fill for dark bands', function () {
    $html = Blade::render('<x-sigta::button variant="inverse">Book</x-sigta::button>');

    expect($html)
        ->toContain('data-sigta="button"')
        ->toContain('bg-surface')
        ->toContain('text-ink')
        ->toContain('border-surface');
});

it('renders the ghost-inverse variant as a hairline outline for dark bands', function () {
    $html = Blade::render('<x-sigta::button variant="ghost-inverse">Mail</x-sigta::button>');

    expect($html)
        ->toContain('bg-transparent')
        ->toContain('text-inverse-fg')
        ->toContain('border-inverse-line')
        ->toContain('hover:bg-inverse-fg/10');
});
