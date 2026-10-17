<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;

it('renders a combobox input anchoring a listbox of time options', function () {
    $html = Blade::render('<x-sigta::time-picker label="Start" />');

    expect($html)
        ->toContain('data-sigta="time-picker"')
        ->toContain('role="combobox"')
        ->toContain('aria-haspopup="listbox"')
        ->toContain('aria-expanded="false"')
        ->toContain('role="listbox"')
        ->toContain('role="option"')
        ->toContain('aria-label="Open time list"');
});

it('carries contract parts instead of design classes', function () {
    $html = Blade::render('<x-sigta::time-picker label="Start" value="09:30" />');

    expect($html)
        ->not->toContain('class=')
        ->not->toContain(':class=')
        ->toContain('data-part="trigger"')
        ->toContain('data-part="input"')
        ->toContain('data-part="toggle"')
        ->toMatch('/x-cloak[^>]*data-part="listbox"/')
        ->toContain('data-part="option"')
        ->toContain('x-bind:data-active="activeIndex === 0 ? \'\' : null"');

    expect(contractCss('time-picker'))
        ->toContain('[data-scope="time-picker"][data-part="listbox"] {')
        ->toContain('min-w-40 max-h-60 overflow-y-auto bg-surface-raised border border-line rounded-panel shadow-paper py-1.5')
        ->toContain('&[aria-selected="true"] {')
        ->toContain('@apply bg-ink text-surface;')
        ->toContain('&[data-active]:not([aria-selected="true"]) {')
        ->toContain('@apply bg-ink/5;');
});

it('generates half-hourly options across the day by default', function () {
    $html = Blade::render('<x-sigta::time-picker label="Start" />');

    expect(substr_count($html, 'role="option"'))->toBe(48); // 24h / 30min
    expect($html)
        ->toContain('data-value="00:00"')
        ->toContain('data-value="23:30"');
});

it('honours step and min/max bounds', function () {
    $html = Blade::render('<x-sigta::time-picker min="09:00" max="17:00" step="60" />');

    expect(substr_count($html, 'role="option"'))->toBe(9); // 09:00 … 17:00 inclusive
    expect($html)
        ->toContain('data-value="09:00"')
        ->toContain('data-value="17:00"')
        ->not->toContain('data-value="08:00"')
        ->not->toContain('data-value="18:00"');
});

it('marks the current value option as selected', function () {
    $html = Blade::render('<x-sigta::time-picker value="09:30" />');

    expect($html)->toMatch('/data-value="09:30"\s+aria-selected="true"/');
});

it('formats labels in 12-hour mode without changing the model', function () {
    $html = Blade::render('<x-sigta::time-picker value="13:30" format="12" />');

    expect($html)
        ->toContain('1:30 PM')                 // display label
        ->toContain('value="1:30 PM"')         // the field, seeded
        ->toContain('data-value="13:30"');     // model value stays 24h
});

it('reads a Carbon or a loose time as HH:MM', function () {
    $carbon = Blade::render('<x-sigta::time-picker name="t" :value="$value" />', ['value' => Carbon::parse('2026-07-09 09:30:00')]);
    $loose = Blade::render('<x-sigta::time-picker name="t" value="9:30" />');

    foreach ([$carbon, $loose] as $html) {
        expect($html)
            ->toMatch('/<input type="hidden"[^>]*value="09:30"/')
            ->toMatch('/data-value="09:30"\s+aria-selected="true"/');
    }
});

it('forwards name and wire:model onto the hidden input', function () {
    $html = Blade::render('<x-sigta::time-picker name="start" wire:model="start" value="09:00" />');

    expect($html)
        ->toMatch('/<input type="hidden"[^>]*name="start"/')
        ->toContain('wire:model="start"')
        ->toMatch('/<input type="hidden"[^>]*value="09:00"/');
});

it('disables the input, the button and the hidden input', function () {
    $html = Blade::render('<x-sigta::time-picker name="locked" value="12:00" disabled />');

    expect($html)
        ->toMatch('/data-part="input"\s+disabled/')
        ->toMatch('/data-part="toggle"\s+disabled/')
        ->toMatch('/<input type="hidden"[^>]*disabled/');
});

it('shows the error state on the input', function () {
    $html = Blade::render('<x-sigta::time-picker label="Start" id="tp" error="Choose a time." />');

    expect($html)
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="tp-error"')
        ->toContain('Choose a time.');
    expect(contractCss('time-picker'))->toContain('&[aria-invalid="true"] {')->toContain('@apply border-danger focus:border-ink;');
});

it('drives the list from the sigtaTimePicker Alpine component', function () {
    $html = Blade::render('<x-sigta::time-picker value="09:30" />');

    expect($html)
        ->toContain('x-data="sigtaTimePicker(')
        ->toContain('x-on:change="typed()"')
        ->not->toContain('x-transition');
});
