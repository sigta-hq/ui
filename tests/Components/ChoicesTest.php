<?php

use Illuminate\Support\Facades\Blade;

it('renders a checkbox wrapped in its label', function () {
    $html = Blade::render('<x-sigta::checkbox label="Paper grain overlay" checked />');

    expect($html)
        ->toContain('data-sigta="checkbox"')
        ->toContain('<label')
        ->toContain('type="checkbox"')
        ->toContain('checked')
        ->toContain('data-sigta-check')
        ->toContain('Paper grain overlay');

    expect(contractCss('checkbox'))->toContain('rounded-[4px]')->toContain('checked:bg-ink');
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
        ->toContain('data-scope="radio" data-part="control"')
        ->toMatch('/<input[^>]*name="density"/')
        ->toContain('Compact density');

    expect(contractCss('radio'))->toContain('rounded-full')->toContain('checked:bg-ink');
});

it('renders a bare radio as the root and dims it when disabled', function () {
    expect(Blade::render('<x-sigta::radio name="d" disabled />'))
        ->toMatch('/<input data-sigta="radio" type="radio"[^>]*disabled/')
        ->not->toContain('<label');

    expect(contractCss('radio'))->toContain('input[data-sigta="radio"] {')->toContain('@apply disabled:opacity-40 disabled:pointer-events-none;');
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
        ->toContain('data-scope="switch" data-part="input"')
        ->toContain('data-scope="switch" data-part="track"')
        ->toContain('Grayscale mode');

    expect(contractCss('switch'))
        ->toContain('@apply sr-only;')
        ->toContain('[data-scope="switch"][data-part="input"]:checked ~ [data-scope="switch"][data-part="track"] {')
        ->toContain('@apply bg-ink border-ink after:translate-x-[20px];');
});

it('forwards wire:model and disabled to the native switch input', function () {
    $html = Blade::render('<x-sigta::switch label="Grayscale" wire:model="grayscale" disabled />');

    expect($html)
        ->toMatch('/<input[^>]*wire:model="grayscale"/')
        ->toMatch('/<input[^>]*disabled/');

    expect(contractCss('switch'))->toContain('has-[:disabled]:opacity-40');
});

it('draws the checkbox tick as pen strokes and gives on press', function () {
    expect(Blade::render('<x-sigta::checkbox />'))->toContain('data-sigta-check');

    expect(contractCss('checkbox'))
        ->toContain('[data-sigta-check]::before')
        ->toContain('active:scale-90')
        ->not->toContain('after:rotate-45');
});

it('squashes the switch knob while pressed', function () {
    expect(Blade::render('<x-sigta::switch label="Grain" />'))->toContain('data-sigta="switch"');

    expect(contractCss('switch'))
        ->toContain('[data-sigta="switch"]:active [data-scope="switch"][data-part="track"] {')
        ->toContain('@apply after:w-[22px];')
        ->toContain('[data-sigta="switch"]:active [data-scope="switch"][data-part="input"]:checked ~ [data-scope="switch"][data-part="track"] {')
        ->toContain('@apply after:translate-x-[16px];');
});
