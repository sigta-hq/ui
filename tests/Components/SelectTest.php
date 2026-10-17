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
        ->toContain('Full build')
        ->toContain('Design sprint')
        ->toContain('<svg')
        ->toContain('data-scope="select" data-part="icon"');

    expect(contractCss('select'))->toContain('appearance-none')->toContain('[data-scope="select"][data-part="icon"]');
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
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="t-error"')
        ->toContain('Pick one.');

    expect(contractCss('select'))->toContain('&[aria-invalid="true"] {')->toContain('@apply border-danger focus:border-ink;');
});

it('dims the chevron with a disabled select', function () {
    expect(Blade::render('<x-sigta::select disabled><option>A</option></x-sigta::select>'))->toMatch('/<select[^>]*disabled/');

    expect(contractCss('select'))->toContain('[data-sigta="select"]:disabled ~ [data-scope="select"][data-part="icon"]');
});

it('renders bare without field chrome', function () {
    $html = Blade::render('<x-sigta::select><option>A</option></x-sigta::select>');

    expect($html)
        ->toContain('<select')
        ->not->toContain('data-sigta="field"');
});
