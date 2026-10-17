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
        ->toContain('data-scope="save-state" data-part="labels"')
        ->toContain('<span data-scope="save-state" data-part="label" data-when="dirty">Unsaved changes</span>')
        ->toContain('<span data-scope="save-state" data-part="label" data-when="saving">Saving…</span>')
        ->toContain('<span data-scope="save-state" data-part="label" data-when="saved">Saved</span>')
        ->toContain('<span data-scope="save-state" data-part="label" data-when="failed">Not saved</span>')
        // Switching labels is CSS visibility, never display, so nothing reflows.
        ->not->toContain('x-show')
        ->not->toContain('display: none');

    expect(contractCss('save-state'))
        ->toMatch('/\[data-scope="save-state"\]\[data-part="labels"\] \{\s*@apply grid min-h-5/')
        ->toMatch('/\[data-scope="save-state"\]\[data-part="label"\] \{\s*grid-area: 1 \/ 1;\s*visibility: hidden;/')
        ->toContain('&[data-when="saving"] { @apply sr-only motion-reduce:not-sr-only; }')
        ->toMatch('/&\[data-when="saved"\] \{ @apply font-hand[^}]*-rotate-3; \}/')
        ->toContain('&[data-when="failed"] { @apply text-danger; }')
        ->not->toContain('display: none');
});

it('inks the compass while it saves and once it has', function () {
    expect(Blade::render('<x-sigta::save-state />'))->toMatch('/data-sigta="loader"[^>]*data-scope="save-state" data-part="loader"|data-scope="save-state" data-part="loader"[^>]*data-sigta="loader"/');
    expect(contractCss('save-state'))->toMatch('/\[data-sigta="save-state"\]:is\(\[data-state="saving"\], \[data-state="saved"\]\) > \[data-scope="save-state"\]\[data-part="loader"\] \{\s*@apply text-ink;/');
});

it('sits at the start by default and flips for the end of a bar', function () {
    expect(Blade::render('<x-sigta::save-state />'))->toContain('data-align="start"');
    expect(Blade::render('<x-sigta::save-state align="end" />'))->toContain('data-align="end"');

    expect(contractCss('save-state'))
        ->toContain('&[data-align="end"] { @apply flex-row-reverse; }')
        ->toMatch('/@apply grid min-h-5 items-center justify-items-start;\s*\[data-sigta="save-state"\]\[data-align="end"\] > & \{ @apply justify-items-end text-right; \}/');
});

it('takes its own words, and holds an idle line only when given one', function () {
    $html = Blade::render('<x-sigta::save-state idle="All changes saved" saved="Gemt" failed="Ikke gemt" dirty="Ikke gemt endnu" saving="Gemmer…" />');

    expect($html)
        ->toContain('data-when="idle">All changes saved</span>')
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
