<?php

use Illuminate\Support\Carbon;
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
    expect(substr_count($html, 'data-part="day"'))->toBe(42);
    expect(substr_count($html, 'data-part="week"'))->toBe(6);
    expect(substr_count($html, 'data-part="weekday"'))->toBe(7);
});

it('carries no design classes; the look lives in the contract stylesheet', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-09" />');

    expect($html)->not->toContain('class=');
    expect(contractCss('calendar'))
        ->toContain('[data-sigta="calendar"] {')
        ->toContain('@apply inline-flex flex-col gap-2 bg-surface-raised border border-line rounded-panel shadow-paper p-3 w-[17.5rem] select-none;')
        ->toContain('[data-scope="calendar"][data-part="day"] {');
});

it('merges a consumer class onto the root', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-09" class="mt-4" />');

    expect($html)->toMatch('/<div\s+data-sigta="calendar"[^>]*class="mt-4"/');
});

it('opens on the month of the given value with a mono header', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-09" />');

    expect($html)->toContain('July 2026')->toContain('data-part="title"');
    expect(contractCss('calendar'))->toContain('@apply font-mono text-[0.72rem] tracking-[0.14em] uppercase text-ink;');
});

it('marks the value day as selected with a per-day aria-label', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-09" />');

    expect($html)->toMatch('/data-date="2026-07-09"\s+aria-label="9 July 2026"\s+aria-selected="true"/');
    expect(contractCss('calendar'))->toContain('&[aria-selected="true"] {')->toContain('@apply bg-ink text-surface;');
});

it('gives the tab stop to the selected day only (roving tabindex)', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-09" />');

    expect(substr_count($html, 'tabindex="0"'))->toBe(1);
    expect($html)->toMatch('/data-date="2026-07-09"[^>]*tabindex="0"/');
});

it('marks today, pinned by the today prop, with aria-current', function () {
    $html = Blade::render('<x-sigta::calendar today="2026-07-21" />');

    expect($html)
        ->toContain('July 2026')
        ->toMatch('/data-date="2026-07-21"[^>]*aria-current="date"/')
        ->toMatch('/data-date="2026-07-21"[^>]*tabindex="0"/');
    expect(substr_count($html, 'aria-current="date"'))->toBe(1);
    expect(contractCss('calendar'))->toContain('&[aria-current="date"]:not([aria-selected="true"]) {')->toContain('@apply border border-ink/50;');
});

it('opens on the current month without a value or a pinned today', function () {
    Carbon::setTestNow('2026-03-11 10:00:00');

    $html = Blade::render('<x-sigta::calendar />');

    expect($html)->toContain('March 2026')->toMatch('/data-date="2026-03-11"[^>]*aria-current="date"/');

    Carbon::setTestNow();
});

it('accepts a Carbon value and normalizes it to Y-m-d', function () {
    $html = Blade::render('<x-sigta::calendar name="due" :value="$value" />', ['value' => Carbon::parse('2026-07-09 14:30:00')]);

    expect($html)
        ->toMatch('/<input type="hidden"[^>]*value="2026-07-09"/')
        ->toMatch('/data-date="2026-07-09"\s+aria-label="9 July 2026"\s+aria-selected="true"/');
});

it('renders month navigation chevron buttons', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-09" />');

    expect($html)
        ->toContain('aria-label="Previous month"')
        ->toContain('aria-label="Next month"')
        ->toContain('data-part="prev"')
        ->toContain('data-part="next"');
});

it('disables days outside the min/max range', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-15" min="2026-07-10" max="2026-07-20" />');

    // 9 July is before the min → disabled; 15 July (selected) is in range.
    expect($html)->toMatch('/data-date="2026-07-09"[^>]*?\sdisabled\s/');
    expect($html)->not->toMatch('/data-date="2026-07-15"[^>]*?\sdisabled\s/');
    expect(contractCss('calendar'))->toContain('disabled:opacity-40 disabled:pointer-events-none');
});

it('disables the month buttons that would leave min/max', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-15" min="2026-07-06" max="2026-07-24" />');

    expect($html)
        ->toMatch('/aria-label="Previous month"[^>]*\sdisabled/')
        ->toMatch('/aria-label="Next month"[^>]*\sdisabled/');
});

it('hands the Alpine component its server state without design classes', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-07-15" min="2026-07-06" />');

    expect($html)
        ->toContain('x-data="sigtaCalendar(')
        ->toContain('x-on:keydown="key($event)"')
        ->toContain('x-bind:aria-selected=')
        ->not->toContain(':class=');
});

it('forwards name and wire:model onto the hidden input', function () {
    $html = Blade::render('<x-sigta::calendar name="due" wire:model="due" value="2026-07-09" />');

    expect($html)
        ->toMatch('/<input type="hidden"[^>]*name="due"/')
        ->toContain('wire:model="due"')
        ->toContain('value="2026-07-09"');
});
