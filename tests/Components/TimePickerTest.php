<?php

use Illuminate\Support\Facades\Blade;

it('renders a combobox input anchoring a listbox of time options', function () {
    $html = Blade::render('<x-sigta::time-picker label="Start" />');

    expect($html)
        ->toContain('data-sigta="time-picker"')
        ->toContain('role="combobox"')
        ->toContain('aria-haspopup="listbox"')
        ->toContain('role="listbox"')
        ->toContain('role="option"')
        ->toContain('aria-label="Open time list"');
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
        ->toContain('data-value="13:30"');     // model value stays 24h
});

it('forwards name and wire:model onto the hidden input', function () {
    $html = Blade::render('<x-sigta::time-picker name="start" wire:model="start" value="09:00" />');

    expect($html)
        ->toMatch('/<input type="hidden"[^>]*name="start"/')
        ->toContain('wire:model="start"')
        ->toMatch('/<input type="hidden"[^>]*value="09:00"/');
});

it('shows the error state on the input', function () {
    $html = Blade::render('<x-sigta::time-picker label="Start" id="tp" error="Choose a time." />');

    expect($html)
        ->toContain('border-danger')
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="tp-error"')
        ->toContain('Choose a time.');
});
