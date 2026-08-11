<?php

use Illuminate\Support\Facades\Blade;

it('renders a hairline-gap grid with tiles and numbers', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::tile-grid cols="4">
            <x-sigta::tile-grid.tile num="01">Teknisk ledelse</x-sigta::tile-grid.tile>
            <x-sigta::tile-grid.tile num="02">Din mand i rummet</x-sigta::tile-grid.tile>
        </x-sigta::tile-grid>
    BLADE);

    expect($html)
        ->toContain('data-sigta="tile-grid"')
        ->toContain('data-sigta="tile"')
        ->toContain('gap-px')
        ->toContain('bg-line')
        ->toContain('border border-line')
        ->toContain('rounded-base')
        ->toContain('overflow-hidden')
        // tiles + numbers
        ->toContain('Teknisk ledelse')
        ->toContain('Din mand i rummet')
        ->toContain('01')
        ->toContain('02')
        ->toContain('bg-surface-raised');
});

it('maps the cols prop to responsive grid columns', function () {
    expect(Blade::render('<x-sigta::tile-grid cols="4" />'))->toContain('lg:grid-cols-4');
    expect(Blade::render('<x-sigta::tile-grid cols="4" />'))->toContain('sm:grid-cols-2');
    expect(Blade::render('<x-sigta::tile-grid cols="3" />'))->toContain('md:grid-cols-3');
    expect(Blade::render('<x-sigta::tile-grid cols="2" />'))->toContain('md:grid-cols-2');
});

it('renders a tile without a number', function () {
    $html = Blade::render('<x-sigta::tile-grid.tile>Bare indhold</x-sigta::tile-grid.tile>');

    expect($html)
        ->toContain('data-sigta="tile"')
        ->toContain('Bare indhold');
});

it('forwards arbitrary attributes on both grid and tile', function () {
    $grid = Blade::render('<x-sigta::tile-grid wire:key="grid-1" data-test="grid" />');
    expect($grid)
        ->toContain('wire:key="grid-1"')
        ->toContain('data-test="grid"');

    $tile = Blade::render('<x-sigta::tile-grid.tile class="min-h-[240px]" data-test="tile">X</x-sigta::tile-grid.tile>');
    expect($tile)
        ->toContain('min-h-[240px]')
        ->toContain('data-test="tile"');
});
