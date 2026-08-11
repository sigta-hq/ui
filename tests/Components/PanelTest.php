<?php

use Illuminate\Support\Facades\Blade;

it('renders slot content in a bordered surface', function () {
    $html = Blade::render('<x-sigta::panel>Prisindhold</x-sigta::panel>');

    expect($html)
        ->toContain('data-sigta="panel"')
        ->toContain('Prisindhold')
        ->toContain('bg-surface-raised')
        ->toContain('border border-line')
        ->toContain('rounded-panel')
        ->toContain('relative');
});

it('does not clip overhanging children', function () {
    $html = Blade::render('<x-sigta::panel>Body</x-sigta::panel>');

    expect($html)->not->toContain('overflow-hidden');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::panel wire:key="panel-1" data-test="panel">Body</x-sigta::panel>');

    expect($html)
        ->toContain('wire:key="panel-1"')
        ->toContain('data-test="panel"');
});
