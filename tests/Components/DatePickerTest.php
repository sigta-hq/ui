<?php

use Illuminate\Support\Facades\Blade;

it('renders a combobox input that anchors a calendar dialog', function () {
    $html = Blade::render('<x-sigta::date-picker label="Due date" value="2026-07-09" />');

    expect($html)
        ->toContain('data-sigta="date-picker"')
        ->toContain('role="combobox"')
        ->toContain('aria-haspopup="dialog"')
        ->toContain('role="dialog"')
        ->toContain('aria-label="Choose date"')
        ->toContain('aria-label="Open calendar"')
        // the shared calendar engine is embedded
        ->toContain('role="grid"')
        ->toContain('data-sigta="calendar"');
});

it('wires the label to the visible input', function () {
    $html = Blade::render('<x-sigta::date-picker label="Due date" id="dp" value="2026-07-09" />');

    expect($html)
        ->toContain('for="dp"')
        ->toContain('id="dp"')
        ->toContain('aria-controls="dp-panel"');
});

it('seeds the display with a formatted date and the hidden input with Y-m-d', function () {
    $html = Blade::render('<x-sigta::date-picker name="due" value="2026-07-09" />');

    expect($html)
        ->toContain('9 Jul 2026')                               // formatted display seed
        ->toMatch('/<input type="hidden"[^>]*value="2026-07-09"/'); // model value
});

it('forwards name and wire:model onto the hidden input', function () {
    $html = Blade::render('<x-sigta::date-picker name="due" wire:model="due" />');

    expect($html)
        ->toMatch('/<input type="hidden"[^>]*name="due"/')
        ->toContain('wire:model="due"');
});

it('passes min/max through to the embedded calendar', function () {
    $html = Blade::render('<x-sigta::date-picker value="2026-07-15" min="2026-07-10" max="2026-07-20" />');

    expect($html)->toMatch('/data-date="2026-07-09"[^>]*?\sdisabled\s/');
});

it('shows the error state on the input', function () {
    $html = Blade::render('<x-sigta::date-picker label="Due date" id="dp" error="Pick a date." />');

    expect($html)
        ->toContain('border-danger')
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="dp-error"')
        ->toContain('Pick a date.');
});

it('renders an inline variant whose calendar button waits for hover or focus', function () {
    $html = Blade::render('<x-sigta::date-picker variant="inline" placeholder="No due date" wire:model="due" />');

    expect($html)
        ->toContain('data-variant="inline"')
        ->toContain('-mx-[0.8em]')
        ->toContain('group/date')
        ->toContain('border-transparent')
        ->toContain('group-hover/date:opacity-100')
        ->toContain('placeholder="No due date"')
        ->toMatch('/<input type="hidden"[^>]*wire:model="due"/');
});

it('keeps the boxed date picker as the default variant', function () {
    $html = Blade::render('<x-sigta::date-picker />');

    expect($html)->toContain('border-line')->not->toContain('group/date');
});
