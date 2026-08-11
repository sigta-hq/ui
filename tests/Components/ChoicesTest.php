<?php

use Illuminate\Support\Facades\Blade;

it('renders a checkbox wrapped in its label', function () {
    $html = Blade::render('<x-sigta::checkbox label="Paper grain overlay" checked />');

    expect($html)
        ->toContain('data-sigta="checkbox"')
        ->toContain('<label')
        ->toContain('type="checkbox"')
        ->toContain('checked')
        ->toContain('rounded-[4px]')
        ->toContain('checked:bg-ink')
        ->toContain('Paper grain overlay');
});

it('renders a bare checkbox without a label wrapper', function () {
    $html = Blade::render('<x-sigta::checkbox />');

    expect($html)
        ->toContain('data-sigta="checkbox"')
        ->not->toContain('<label');
});

it('forwards wire:model and disabled to the native checkbox', function () {
    $html = Blade::render('<x-sigta::checkbox label="Grain" wire:model="grain" disabled />');

    expect($html)
        ->toMatch('/<input[^>]*wire:model="grain"/')
        ->toMatch('/<input[^>]*disabled/');
});

it('renders a round radio that forwards its name', function () {
    $html = Blade::render('<x-sigta::radio name="density" label="Compact density" />');

    expect($html)
        ->toContain('data-sigta="radio"')
        ->toContain('type="radio"')
        ->toContain('rounded-full')
        ->toContain('checked:bg-ink')
        ->toMatch('/<input[^>]*name="density"/')
        ->toContain('Compact density');
});

it('forwards wire:model to the native radio', function () {
    $html = Blade::render('<x-sigta::radio name="density" value="compact" wire:model="density" />');

    expect($html)->toMatch('/<input[^>]*wire:model="density"/');
});

it('renders a switch as a real checkbox with a peer track', function () {
    $html = Blade::render('<x-sigta::switch label="Grayscale mode" />');

    expect($html)
        ->toContain('data-sigta="switch"')
        ->toContain('role="switch"')
        ->toContain('type="checkbox"')
        ->toContain('sr-only')
        ->toContain('peer-checked:bg-ink')
        ->toContain('peer-checked:after:translate-x-[20px]')
        ->toContain('Grayscale mode');
});

it('forwards wire:model and disabled to the native switch input', function () {
    $html = Blade::render('<x-sigta::switch label="Grayscale" wire:model="grayscale" disabled />');

    expect($html)
        ->toMatch('/<input[^>]*wire:model="grayscale"/')
        ->toMatch('/<input[^>]*disabled/')
        ->toContain('has-[:disabled]:opacity-40');
});
