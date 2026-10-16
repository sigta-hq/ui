<?php

use Illuminate\Support\Facades\Blade;

it('renders the working compass by default', function () {
    $html = Blade::render('<x-sigta::loader />');

    expect($html)
        ->toContain('data-sigta="loader"')
        ->toContain('data-state="working"')
        ->toContain('data-variant="compass"')
        ->toContain('role="progressbar"')
        ->toContain('aria-label="Working"')
        ->toContain('data-size="base"')
        ->toContain('sigta-loader-pencil')
        ->toContain('sigta-loader-needle')
        ->toContain('sigta-loader-ring-front')
        ->toContain('sigta-loader-ring-back')
        ->toContain('sigta-loader-arc')
        ->toContain('sigta-loader-pivot')
        ->toContain('sigta-loader-frame')
        ->not->toContain('data-progress')
        ->not->toContain('aria-valuenow');
});

it('maps each size to its box and names it for the pen weight', function () {
    expect(Blade::render('<x-sigta::loader size="xs" />'))->toContain('data-size="xs"');
    expect(Blade::render('<x-sigta::loader size="sm" />'))->toContain('data-size="sm"');
    expect(Blade::render('<x-sigta::loader size="lg" />'))->toContain('data-size="lg"');
    expect(Blade::render('<x-sigta::loader size="xl" />'))->toContain('data-size="xl"');
    expect(Blade::render('<x-sigta::loader size="huge" />'))->toContain('data-size="base"');

    expect(contractCss('loader'))
        ->toContain('[data-sigta="loader"][data-size="xs"] { @apply size-4; }')
        ->toContain('[data-sigta="loader"][data-size="xl"] { @apply size-[4.5rem]; }');
});

it('draws the circle as far round as its value', function () {
    $html = Blade::render('<x-sigta::loader :value="42" />');

    expect($html)
        ->toContain('data-progress')
        ->toContain('sigta-loader-ring-front')
        ->toContain('sigta-loader-frame')
        ->toContain('--sigta-progress: 42')
        ->toContain('aria-valuemin="0"')
        ->toContain('aria-valuemax="100"')
        ->toContain('aria-valuenow="42"');
});

it('clamps progress and fills it when done', function () {
    expect(Blade::render('<x-sigta::loader :value="140" />'))->toContain('--sigta-progress: 100');
    expect(Blade::render('<x-sigta::loader :value="-5" />'))->toContain('--sigta-progress: 0');
    expect(Blade::render('<x-sigta::loader :value="30" state="done" />'))->toContain('--sigta-progress: 100');
});

it('labels each state and falls back to working for an unknown one', function () {
    expect(Blade::render('<x-sigta::loader state="queued" />'))->toContain('data-state="queued"')->toContain('aria-label="Queued"');
    expect(Blade::render('<x-sigta::loader state="done" />'))->toContain('data-state="done"')->toContain('aria-label="Done"');
    expect(Blade::render('<x-sigta::loader state="failed" />'))->toContain('data-state="failed"')->toContain('aria-label="Failed"');
    expect(Blade::render('<x-sigta::loader state="idle" />'))->toContain('data-state="idle"')->toContain('aria-label="Ready"');
    expect(Blade::render('<x-sigta::loader state="spinning" />'))->toContain('data-state="working"');
    expect(Blade::render('<x-sigta::loader label="Importing contacts" />'))->toContain('aria-label="Importing contacts"');
});

it('hides itself from assistive tech when decorative', function () {
    $html = Blade::render('<x-sigta::loader decorative />');

    expect($html)
        ->toContain('aria-hidden="true"')
        ->not->toContain('role="progressbar"')
        ->not->toContain('aria-label=');
});

it('renders the sketch variant', function () {
    expect(Blade::render('<x-sigta::loader variant="sketch" />'))->toContain('data-variant="sketch"');
    expect(Blade::render('<x-sigta::loader variant="nope" />'))->toContain('data-variant="compass"');
});

it('forwards attributes so a binding can drive its state', function () {
    $html = Blade::render('<x-sigta::loader x-bind:data-state="state" class="text-danger" id="import-loader" />');

    expect($html)
        ->toContain('x-bind:data-state="state"')
        ->toContain('text-danger')
        ->toContain('inline-block')
        ->toContain('id="import-loader"');
});
