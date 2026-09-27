<?php

use Illuminate\Support\Facades\Blade;

it('renders an idle save state with a quiet compass', function () {
    $html = Blade::render('<x-sigta::save-state />');

    expect($html)
        ->toContain('data-sigta="save-state"')
        ->toContain('x-data="sigtaSaveState"')
        ->toContain('data-state="idle"')
        ->toContain('x-bind:data-state="state"')
        ->toContain('data-sigta="loader"')
        ->toContain('x-bind:data-state="mark"')
        ->toContain('aria-hidden="true"')
        ->toContain('role="status"')
        ->toContain('aria-live="polite"')
        ->not->toContain('data-target=')
        ->not->toContain('data-name=');
});

it('carries the Livewire target and the event name', function () {
    expect(Blade::render('<x-sigta::save-state target="form.title, form.body" />'))->toContain('data-target="form.title, form.body"');
    expect(Blade::render('<x-sigta::save-state name="profile" />'))->toContain('data-name="profile"');
});

it('maps the initial state onto the compass', function () {
    expect(Blade::render('<x-sigta::save-state state="saving" />'))->toContain('data-state="saving"')->toContain('data-state="working"');
    expect(Blade::render('<x-sigta::save-state state="dirty" />'))->toContain('data-state="queued"');
    expect(Blade::render('<x-sigta::save-state state="saved" />'))->toContain('data-state="done"');
    expect(Blade::render('<x-sigta::save-state state="failed" />'))->toContain('data-state="failed"');
    expect(Blade::render('<x-sigta::save-state state="exploded" />'))->toContain('data-state="idle"');
});

it('keeps every label in the markup so it never changes width', function () {
    $html = Blade::render('<x-sigta::save-state state="saving" />');

    expect($html)
        ->toContain('sigta-save-labels grid')
        ->toMatch('/<span data-when="dirty">Unsaved changes<\/span>/')
        ->toMatch('/<span data-when="saving" class="sr-only motion-reduce:not-sr-only">Saving…<\/span>/')
        ->toMatch('/<span data-when="saved" class="font-hand[^"]*">Saved<\/span>/')
        ->toMatch('/<span data-when="failed" class="text-danger">Not saved<\/span>/')
        // Switching labels is CSS visibility, never display, so nothing reflows.
        ->not->toContain('x-show')
        ->not->toContain('display: none');
});

it('sits at the start by default and flips for the end of a bar', function () {
    $start = Blade::render('<x-sigta::save-state />');

    expect($start)->not->toContain('flex-row-reverse')->toContain('justify-items-start');

    expect(Blade::render('<x-sigta::save-state align="end" />'))
        ->toContain('flex-row-reverse')
        ->toContain('justify-items-end text-right')
        ->not->toContain('justify-items-start');
});

it('takes its own words, and holds an idle line only when given one', function () {
    $html = Blade::render('<x-sigta::save-state idle="All changes saved" saved="Gemt" failed="Ikke gemt" dirty="Ikke gemt endnu" saving="Gemmer…" />');

    expect($html)
        ->toContain('<span data-when="idle">All changes saved</span>')
        ->toContain('>Gemt</span>')
        ->toContain('>Ikke gemt</span>')
        ->toContain('>Ikke gemt endnu</span>')
        ->toContain('>Gemmer…</span>');

    expect(Blade::render('<x-sigta::save-state />'))->not->toContain('data-when="idle"');
});

it('forwards attributes to its root', function () {
    expect(Blade::render('<x-sigta::save-state class="ml-auto" data-test="saver" />'))
        ->toContain('ml-auto')
        ->toContain('data-test="saver"');
});
