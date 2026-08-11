<?php

use Illuminate\Support\Facades\Blade;

it('renders an outline badge by default', function () {
    $html = Blade::render('<x-sigta::badge>Outline</x-sigta::badge>');

    expect($html)
        ->toContain('Outline')
        ->toContain('data-sigta="badge"')
        ->toContain('border-ink')
        ->toContain('font-mono')
        ->toContain('rounded-full')
        ->not->toContain('bg-ink');
});

it('renders the solid variant as an ink fill', function () {
    $html = Blade::render('<x-sigta::badge variant="solid">Solid</x-sigta::badge>');

    expect($html)
        ->toContain('bg-ink')
        ->toContain('text-surface');
});

it('renders the accent variant with the accent border', function () {
    $html = Blade::render('<x-sigta::badge variant="accent">Highlight</x-sigta::badge>');

    expect($html)
        ->toContain('bg-accent')
        ->toContain('border-accent-line')
        ->toContain('text-ink');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::badge id="state" data-test="badge" class="mt-2">New</x-sigta::badge>');

    expect($html)
        ->toContain('id="state"')
        ->toContain('data-test="badge"')
        ->toContain('mt-2');
});
