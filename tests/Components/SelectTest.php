<?php

use Illuminate\Support\Facades\Blade;

it('renders a native select with slotted options and a chevron', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::select label="Engagement type">
            <option>Full build</option>
            <option>Design sprint</option>
        </x-sigta::select>
    BLADE);

    expect($html)
        ->toContain('data-sigta="select"')
        ->toContain('<select')
        ->toContain('appearance-none')
        ->toContain('Full build')
        ->toContain('Design sprint')
        ->toContain('<svg');
});

it('wires the label to the native select', function () {
    $html = Blade::render('<x-sigta::select label="Type"><option>A</option></x-sigta::select>');

    preg_match('/for="([^"]+)"/', $html, $for);
    expect($for)->not->toBeEmpty();
    expect($for[1])->toStartWith('sigta-');
    expect($html)->toContain('id="'.$for[1].'"');
});

it('forwards wire:model to the native select', function () {
    $html = Blade::render('<x-sigta::select wire:model="type"><option>A</option></x-sigta::select>');

    expect($html)->toMatch('/<select[^>]*wire:model="type"/');
});

it('shows the error state', function () {
    $html = Blade::render('<x-sigta::select label="Type" id="t" error="Pick one."><option>A</option></x-sigta::select>');

    expect($html)
        ->toContain('border-danger')
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="t-error"')
        ->toContain('Pick one.');
});

it('renders bare without field chrome', function () {
    $html = Blade::render('<x-sigta::select><option>A</option></x-sigta::select>');

    expect($html)
        ->toContain('<select')
        ->not->toContain('data-sigta="field"');
});

it('renders an inline variant whose chevron waits for hover or focus', function () {
    $html = Blade::render('<x-sigta::select variant="inline" wire:model="column"><option>Todo</option></x-sigta::select>');

    expect($html)
        ->toContain('data-variant="inline"')
        ->toContain('group/select')
        ->toContain('-mx-[0.8em]')
        ->toContain('border-transparent')
        ->toContain('group-hover/select:opacity-100')
        ->toContain('peer-focus:opacity-100')
        ->toMatch('/<select[^>]*wire:model="column"/');
});

it('keeps the boxed select as the default variant', function () {
    $html = Blade::render('<x-sigta::select><option>A</option></x-sigta::select>');

    expect($html)->toContain('border-line')->not->toContain('group/select');
});
