<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

// An app swaps the compass for its own loader with config('sigta.loader').

beforeEach(function () {
    View::addNamespace('fixtures', __DIR__.'/../fixtures/views');
    config(['sigta.loader' => 'fixtures::spinner']);
});

it('draws the configured view inside the loader box instead of the compass', function () {
    $html = Blade::render('<x-sigta::loader />');

    expect($html)
        ->toContain('data-sigta="loader"')
        ->toContain('data-variant="custom"')
        ->toContain('data-spinner')
        ->toContain('group/loader')
        ->toContain('data-state="working"')
        ->toContain('role="progressbar"')
        ->toContain('aria-label="Working"')
        ->toContain('size-7')
        ->not->toContain('sigta-loader-pencil')
        ->not->toContain('sigta-loader-frame');
});

it('hands the view its state, value, size and label', function () {
    $html = Blade::render('<x-sigta::loader size="lg" state="done" :value="30" label="Imported" />');

    expect($html)
        ->toContain('data-seen-state="done"')
        ->toContain('data-seen-value="100"')
        ->toContain('data-seen-size="lg"')
        ->toContain('data-seen-label="Imported"');
});

it('keeps the progress contract on the box', function () {
    $html = Blade::render('<x-sigta::loader :value="42" />');

    expect($html)
        ->toContain('data-progress')
        ->toContain('--sigta-progress: 42')
        ->toContain('aria-valuenow="42"')
        ->toContain('data-seen-value="42"');
});

it('keeps the box attributes on the box, not on the view', function () {
    $html = Blade::render('<x-sigta::loader id="import-loader" x-bind:data-state="state" />');

    expect(substr_count($html, 'id="import-loader"'))->toBe(1);
    expect(substr_count($html, 'x-bind:data-state="state"'))->toBe(1);
    expect($html)->toMatch('/<span data-sigta="loader"[^>]*id="import-loader"/');
});

it('still draws a built-in loader asked for by name', function () {
    expect(Blade::render('<x-sigta::loader variant="compass" />'))
        ->toContain('data-variant="compass"')
        ->toContain('sigta-loader-pencil')
        ->not->toContain('data-spinner');

    expect(Blade::render('<x-sigta::loader variant="sketch" />'))
        ->toContain('data-variant="sketch"')
        ->not->toContain('data-spinner');
});

it('shows the configured loader in busy buttons, save states and jobs', function (string $tag) {
    expect(Blade::render($tag))
        ->toContain('data-spinner')
        ->not->toContain('sigta-loader-pencil');
})->with([
    'button' => '<x-sigta::button loading>Save</x-sigta::button>',
    'save-state' => '<x-sigta::save-state state="saving" />',
    'job' => '<x-sigta::job heading="Importing contacts" :value="62" />',
]);

it('falls back to the compass when no loader is configured', function () {
    config(['sigta.loader' => null]);

    expect(Blade::render('<x-sigta::loader />'))
        ->toContain('data-variant="compass"')
        ->toContain('sigta-loader-pencil');
});
