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

it('shows only the initial state and maps it onto the compass', function () {
    $saving = Blade::render('<x-sigta::save-state state="saving" />');

    expect($saving)
        ->toContain('data-state="saving"')
        ->toContain('data-state="working"')
        ->toMatch('/<span x-show="state === \'saving\'"[^>]*sr-only motion-reduce:not-sr-only[^>]*>Saving…<\/span>/')
        ->toMatch('/<span x-show="state === \'saved\'"[^>]*style="display: none"[^>]*>Saved<\/span>/');

    expect(Blade::render('<x-sigta::save-state state="dirty" />'))->toContain('data-state="queued"')->toContain('Unsaved changes');
    expect(Blade::render('<x-sigta::save-state state="saved" />'))->toContain('data-state="done"');
    expect(Blade::render('<x-sigta::save-state state="failed" />'))->toContain('data-state="failed"')->toContain('Not saved');
    expect(Blade::render('<x-sigta::save-state state="exploded" />'))->toContain('data-state="idle"');
});

it('writes "Saved" by hand', function () {
    expect(Blade::render('<x-sigta::save-state />'))->toMatch('/class="sigta-write font-hand[^"]*"[^>]*>Saved</');
});

it('takes its own words, and shows an idle line only when given one', function () {
    $html = Blade::render('<x-sigta::save-state idle="All changes saved" saved="Gemt" failed="Ikke gemt" dirty="Ikke gemt endnu" saving="Gemmer…" />');

    expect($html)
        ->toContain('All changes saved')
        ->toContain('Gemt')
        ->toContain('Ikke gemt')
        ->toContain('Ikke gemt endnu')
        ->toContain('Gemmer…');

    expect(Blade::render('<x-sigta::save-state />'))->not->toContain("state === 'idle'");
});

it('forwards attributes to its root', function () {
    expect(Blade::render('<x-sigta::save-state class="ml-auto" data-test="saver" />'))
        ->toContain('ml-auto')
        ->toContain('data-test="saver"');
});
