<?php

use Illuminate\Support\Facades\Blade;

it('renders a grid with weekday column headers and 42 day cells', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-09" />');

    expect($html)
        ->toContain('data-sigta="calendar"')
        ->toContain('role="grid"')
        ->toContain('role="gridcell"')
        ->toContain('role="columnheader"')
        ->toContain('role="row"')
        ->toContain('Mo')->toContain('Tu')->toContain('We')
        ->toContain('Th')->toContain('Fr')->toContain('Sa')->toContain('Su')
        ->toContain('aria-label="Monday"');

    expect(substr_count($html, 'role="gridcell"'))->toBe(42);
});

it('opens on the month of the given value with a mono header', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-09" />');

    expect($html)->toContain('July 2026');
});

it('marks the value day as selected with a per-day aria-label', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-09" />');

    expect($html)->toMatch('/data-date="2026-07-09"\s+aria-label="9 July 2026"\s+aria-selected="true"/');
});

it('renders month navigation chevron buttons', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-09" />');

    expect($html)
        ->toContain('aria-label="Previous month"')
        ->toContain('aria-label="Next month"');
});

it('disables days outside the min/max range', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-15" min="2026-07-10" max="2026-07-20" />');

    // 9 July is before the min → disabled; 15 July (selected) is in range.
    // \sdisabled\s targets the standalone boolean attribute, not the
    // disabled:… utility classes in the shared class string.
    expect($html)->toMatch('/data-date="2026-07-09"[^>]*?\sdisabled\s/');
    expect($html)->not->toMatch('/data-date="2026-07-15"[^>]*?\sdisabled\s/');
});

it('forwards name and wire:model onto the hidden input', function () {
    $html = Blade::render('<x-sigta::calendar name="due" wire:model="due" value="2026-07-09" />');

    expect($html)
        ->toMatch('/<input type="hidden"[^>]*name="due"/')
        ->toContain('wire:model="due"')
        ->toContain('value="2026-07-09"');
});
