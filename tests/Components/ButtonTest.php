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

it('forwards target on link buttons and adds rel=noopener for _blank', function () {
    $blank = Blade::render('<x-sigta::button href="https://example.com" target="_blank">Open</x-sigta::button>');
    $self = Blade::render('<x-sigta::button href="/contact" target="_self">Stay</x-sigta::button>');
    $customRel = Blade::render('<x-sigta::button href="https://example.com" target="_blank" rel="noreferrer">Open</x-sigta::button>');

    expect($blank)
        ->toContain('target="_blank"')
        ->toContain('rel="noopener"')
        ->toContain('href="https://example.com"');

    expect($self)
        ->toContain('target="_self"')
        ->not->toContain('rel=');

    expect($customRel)
        ->toContain('rel="noreferrer"')
        ->not->toContain('rel="noopener"');
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
