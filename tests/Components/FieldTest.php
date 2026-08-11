<?php

use Illuminate\Support\Facades\Blade;

it('renders label, description, error and the slotted control', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::field label="Project name" description="Shown on invoices." error="Required." for="proj">
            <input id="proj" type="text" />
        </x-sigta::field>
    BLADE);

    expect($html)
        ->toContain('data-sigta="field"')
        ->toContain('for="proj"')
        ->toContain('Project name')
        ->toContain('Shown on invoices.')
        ->toContain('Required.')
        ->toContain('id="proj-description"')
        ->toContain('id="proj-error"')
        ->toContain('text-danger')
        ->toContain('<input id="proj"');
});

it('generates an id for the label when none is given', function () {
    $html = Blade::render('<x-sigta::field label="Email"><input /></x-sigta::field>');

    expect($html)->toMatch('/for="sigta-[A-Za-z0-9]{8}"/');
});

it('styles the label as uppercase mono graphite', function () {
    $html = Blade::render('<x-sigta::field label="Email" for="e"><input id="e" /></x-sigta::field>');

    expect($html)
        ->toContain('font-mono')
        ->toContain('uppercase')
        ->toContain('text-graphite');
});

it('renders only the slot when it has no chrome', function () {
    $html = Blade::render('<x-sigta::field><input type="text" /></x-sigta::field>');

    expect($html)
        ->toContain('<input type="text" />')
        ->not->toContain('data-sigta="field"');
});

it('forwards attributes onto the wrapper', function () {
    $html = Blade::render('<x-sigta::field label="Email" class="mt-4" data-test="field"><input /></x-sigta::field>');

    expect($html)
        ->toContain('data-test="field"')
        ->toContain('mt-4')
        ->toContain('flex flex-col');
});

it('renders a standalone label with the field label style', function () {
    $html = Blade::render('<x-sigta::label for="kf1">Engagement type</x-sigta::label>');

    expect($html)
        ->toContain('data-sigta="label"')
        ->toContain('<label')
        ->toContain('for="kf1"')
        ->toContain('font-mono')
        ->toContain('uppercase')
        ->toContain('Engagement type');
});
