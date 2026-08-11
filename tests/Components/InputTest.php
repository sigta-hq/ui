<?php

use Illuminate\Support\Facades\Blade;

it('renders a bare native input without field chrome', function () {
    $html = Blade::render('<x-sigta::input placeholder="Marginalia v3" />');

    expect($html)
        ->toContain('data-sigta="input"')
        ->toContain('type="text"')
        ->toContain('bg-surface')
        ->toContain('rounded-[5px]')
        ->toContain('placeholder="Marginalia v3"')
        ->not->toContain('data-sigta="field"');
});

it('self-wraps in a field and wires the label to the input', function () {
    $html = Blade::render('<x-sigta::input label="Project name" />');

    expect($html)->toContain('data-sigta="field"');

    preg_match('/for="([^"]+)"/', $html, $for);
    expect($for)->not->toBeEmpty();
    expect($for[1])->toStartWith('sigta-');
    expect($html)->toContain('id="'.$for[1].'"');
});

it('honours a caller-supplied id in the label wiring', function () {
    $html = Blade::render('<x-sigta::input label="Email" id="email-input" />');

    expect($html)
        ->toContain('for="email-input"')
        ->toContain('id="email-input"');
});

it('shows the error state on the native input', function () {
    $html = Blade::render('<x-sigta::input label="Email" id="e" error="Required." />');

    expect($html)
        ->toContain('border-danger')
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="e-error"')
        ->toContain('text-danger')
        ->toContain('Required.')
        ->not->toContain('border-line');
});

it('renders a description and points aria-describedby at it', function () {
    $html = Blade::render('<x-sigta::input label="Email" id="e" description="We never share it." />');

    expect($html)
        ->toContain('We never share it.')
        ->toContain('id="e-description"')
        ->toContain('aria-describedby="e-description"');
});

it('forwards wire:model and arbitrary attributes to the native input', function () {
    $html = Blade::render('<x-sigta::input label="Email" wire:model="email" data-test="input" />');

    expect($html)
        ->toMatch('/<input[^>]*wire:model="email"/')
        ->toMatch('/<input[^>]*data-test="input"/');
});

it('renders a leading icon and pads the input for it', function () {
    $html = Blade::render('<x-sigta::input icon="search" />');

    expect($html)
        ->toContain('<svg')
        ->toContain('pl-[2.4em]');
});

it('allows overriding the type', function () {
    $html = Blade::render('<x-sigta::input type="password" />');

    expect($html)->toContain('type="password"');
});

it('renders a textarea with default rows and field wiring', function () {
    $html = Blade::render('<x-sigta::textarea label="Notes" id="notes">Draft copy</x-sigta::textarea>');

    expect($html)
        ->toContain('data-sigta="textarea"')
        ->toContain('rows="4"')
        ->toContain('for="notes"')
        ->toContain('id="notes"')
        ->toContain('Draft copy');
});

it('forwards wire:model and rows to the native textarea', function () {
    $html = Blade::render('<x-sigta::textarea rows="8" wire:model="body" error="Too short." />');

    expect($html)
        ->toContain('rows="8"')
        ->toMatch('/<textarea[^>]*wire:model="body"/')
        ->toContain('border-danger')
        ->toContain('Too short.');
});
