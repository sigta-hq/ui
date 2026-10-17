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

it('stays a button when href is null or empty, like the Svelte twin', function () {
    foreach (['<x-sigta::button :href="null">Go</x-sigta::button>', '<x-sigta::button href="">Go</x-sigta::button>'] as $source) {
        $html = Blade::render($source);

        expect($html)
            ->toContain('<button')
            ->not->toContain('<a ');
    }
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
        ->toContain('data-variant="inverse"');

    expect(contractCss('button'))
        ->toContain('&[data-variant="inverse"] {')
        ->toContain('@apply bg-surface text-ink border-surface');
});

it('renders the ghost-inverse variant as a hairline outline for dark bands', function () {
    expect(Blade::render('<x-sigta::button variant="ghost-inverse">Mail</x-sigta::button>'))
        ->toContain('data-variant="ghost-inverse"');

    expect(contractCss('button'))
        ->toContain('@apply bg-transparent text-inverse-fg border-inverse-line hover:bg-inverse-fg/10');
});

it('carries its variant and size as contract attributes, never as classes', function () {
    $html = Blade::render('<x-sigta::button variant="primary" size="sm">Save</x-sigta::button>');

    expect($html)
        ->toContain('data-variant="primary"')
        ->toContain('data-size="sm"')
        ->not->toContain('class=');

    expect(Blade::render('<x-sigta::button>Preview</x-sigta::button>'))
        ->toContain('data-variant="secondary"')
        ->toContain('data-size="base"');
});

it('merges a consumer class onto the root', function () {
    expect(Blade::render('<x-sigta::button class="w-full">Go</x-sigta::button>'))
        ->toContain('class="w-full"');
});

it('settles on press and springs back', function () {
    expect(contractCss('button'))
        ->toContain('active:scale-[0.97]')
        ->toContain('active:translate-y-0')
        ->toContain('ease-sigta-spring');
});

it('carries the compass for a Livewire action, shown only once the runtime marks it busy', function () {
    $html = Blade::render('<x-sigta::button wire:click="save">Save</x-sigta::button>');

    expect($html)
        ->toContain('<span data-scope="button" data-part="label">Save</span>')
        ->toContain('data-part="busy"')
        ->toContain('data-sigta="loader"')
        ->not->toContain('data-loading="true"')
        ->not->toContain('data-sigta-busy');

    expect(Blade::render('<x-sigta::button type="submit">Send</x-sigta::button>'))->toContain('data-part="busy"');
    expect(Blade::render('<x-sigta::button x-bind:data-loading="busy">Send</x-sigta::button>'))->toContain('data-part="busy"');
});

it('keeps the label and writes the compass in beside it, never over it', function () {
    $html = Blade::render('<x-sigta::button wire:click="save">Save</x-sigta::button>');

    expect(strpos($html, 'data-part="busy"'))->toBeLessThan(strpos($html, 'data-part="label"'))
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
    expect(contractCss('button'))
        ->toContain('disabled:not-in-data-loading:opacity-40')
        ->not->toContain(' disabled:opacity-40');
});

it('adds no loader markup to a plain button', function () {
    expect(Blade::render('<x-sigta::button>Cancel</x-sigta::button>'))
        ->not->toContain('data-part="busy"')
        ->not->toContain('data-sigta="loader"');
});

it('shows the busy state now with loading, and never with loading false', function () {
    expect(Blade::render('<x-sigta::button loading>Exporting</x-sigta::button>'))
        ->toContain('data-loading="true"')
        ->toContain('data-sigta-busy')
        ->toContain('aria-busy="true"')
        ->toContain('data-part="busy"');

    expect(Blade::render('<x-sigta::button wire:click="save" :loading="false">Save</x-sigta::button>'))
        ->not->toContain('data-part="busy"');
});
