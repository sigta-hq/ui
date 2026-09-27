<?php

use Illuminate\Support\Facades\Blade;

it('renders a number that watches itself for changes', function () {
    $html = Blade::render('<x-sigta::count :value="12" />');

    expect($html)
        ->toContain('data-sigta="count"')
        ->toContain('x-data="sigtaCount"')
        ->toContain('tabular-nums')
        ->toContain('inline-block')
        ->toContain('>12</span>');
});

it('renders zero rather than falling back to the slot', function () {
    expect(Blade::render('<x-sigta::count :value="0">9</x-sigta::count>'))->toContain('>0</span>');
});

it('uses the slot when no value is given, for x-text', function () {
    expect(Blade::render('<x-sigta::count x-text="items.length">3</x-sigta::count>'))
        ->toContain('x-text="items.length"')
        ->toContain('>3</span>');
});
