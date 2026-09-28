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

it('settles on press and springs back', function () {
    $html = Blade::render('<x-sigta::button variant="primary">Save</x-sigta::button>');

    expect($html)
        ->toContain('active:scale-[0.97]')
        ->toContain('active:translate-y-0')
        ->toContain('ease-sigta-spring');
});

it('carries the compass for a Livewire action, shown only once the runtime marks it busy', function () {
    $html = Blade::render('<x-sigta::button wire:click="save">Save</x-sigta::button>');

    expect($html)
        ->toContain('<span class="sigta-button-label contents">Save</span>')
        ->toContain('sigta-button-busy')
        ->toContain('data-sigta="loader"')
        ->not->toContain('data-loading="true"')
        ->not->toContain('data-sigta-busy');

    expect(Blade::render('<x-sigta::button type="submit">Send</x-sigta::button>'))->toContain('sigta-button-busy');
    expect(Blade::render('<x-sigta::button x-bind:data-loading="busy">Send</x-sigta::button>'))->toContain('sigta-button-busy');
});

it('keeps the label and writes the compass in beside it, never over it', function () {
    $html = Blade::render('<x-sigta::button wire:click="save">Save</x-sigta::button>');

    expect(strpos($html, 'sigta-button-busy'))->toBeLessThan(strpos($html, 'sigta-button-label'))
        ->and($html)->not->toContain('absolute inset-0');
});

it('stands the compass in for a leading icon, and only a leading one', function () {
    expect(Blade::render('<x-sigta::button wire:click="save"><x-sigta::icon name="check" size="sm" /> Save</x-sigta::button>'))
        ->toContain('data-lead-icon');

    expect(Blade::render('<x-sigta::button wire:click="save">Save</x-sigta::button>'))
        ->not->toContain('data-lead-icon');

    expect(Blade::render('<x-sigta::button wire:click="next">Next <x-sigta::icon name="arrow-right" size="sm" /></x-sigta::button>'))
        ->not->toContain('data-lead-icon');

    expect(Blade::render('<x-sigta::button><x-sigta::icon name="check" size="sm" /> Plain</x-sigta::button>'))
        ->not->toContain('data-lead-icon');
});

it('keeps a submitting form\'s button in ink while it is disabled for the submit', function () {
    expect(Blade::render('<x-sigta::button type="submit">Send</x-sigta::button>'))
        ->toContain('disabled:not-in-data-loading:opacity-40')
        ->not->toContain(' disabled:opacity-40');
});

it('adds no loader markup to a plain button', function () {
    expect(Blade::render('<x-sigta::button>Cancel</x-sigta::button>'))
        ->not->toContain('sigta-button-busy')
        ->not->toContain('data-sigta="loader"');
});

it('shows the busy state now with loading, and never with loading false', function () {
    expect(Blade::render('<x-sigta::button loading>Exporting</x-sigta::button>'))
        ->toContain('data-loading="true"')
        ->toContain('data-sigta-busy')
        ->toContain('aria-busy="true"')
        ->toContain('sigta-button-busy');

    expect(Blade::render('<x-sigta::button wire:click="save" :loading="false">Save</x-sigta::button>'))
        ->not->toContain('sigta-button-busy');
});
