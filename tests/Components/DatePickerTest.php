<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;

it('renders a combobox input that anchors a calendar dialog', function () {
    $html = Blade::render('<x-sigta::date-picker label="Due date" value="2026-07-09" />');

    expect($html)
        ->toContain('data-sigta="date-picker"')
        ->toContain('role="combobox"')
        ->toContain('aria-haspopup="dialog"')
        ->toContain('aria-expanded="false"')
        ->toContain('role="dialog"')
        ->toContain('aria-label="Choose date"')
        ->toContain('aria-label="Open calendar"')
        // the shared calendar engine is embedded
        ->toContain('role="grid"')
        ->toContain('data-sigta="calendar"');
});

it('carries contract parts instead of design classes', function () {
    $html = Blade::render('<x-sigta::date-picker label="Due date" value="2026-07-09" />');

    expect($html)
        ->not->toContain('class=')
        ->toContain('data-part="anchor"')
        ->toContain('data-part="trigger"')
        ->toContain('data-part="input"')
        ->toContain('data-part="toggle"')
        ->toMatch('/x-cloak[^>]*data-part="panel"/');

    expect(contractCss('date-picker'))
        ->toContain('[data-scope="date-picker"][data-part="input"] {')
        ->toContain('border-[1.5px] border-line rounded-[5px] px-[0.8em] py-[0.62em] pr-[2.6em]')
        ->toContain('[data-scope="date-picker"][data-part="panel"] {')
        ->toContain('@apply absolute top-full left-0 mt-2 z-50;')
        ->toContain('animation: sigta-menu-in');
});

it('merges a consumer class onto the root', function () {
    $html = Blade::render('<x-sigta::date-picker class="w-64" />');

    expect($html)->toMatch('/<div\s+data-sigta="date-picker"[^>]*class="w-64"/');
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
        ->toMatch('/value="9 Jul 2026"\s+data-scope="date-picker"\s+data-part="input"/') // the field, seeded
        ->toMatch('/<input type="hidden"[^>]*value="2026-07-09"/'); // model value
});

it('accepts a Carbon value and normalizes it to Y-m-d', function () {
    $html = Blade::render('<x-sigta::date-picker name="due" :value="$value" />', ['value' => Carbon::parse('2026-07-09 16:45:00')]);

    expect($html)
        ->toContain('value="9 Jul 2026"')
        ->toMatch('/<input type="hidden"[^>]*value="2026-07-09"/');
});

it('forwards name and wire:model onto the hidden input', function () {
    $html = Blade::render('<x-sigta::date-picker name="due" wire:model="due" />');

    expect($html)
        ->toMatch('/<input type="hidden"[^>]*name="due"/')
        ->toMatch('/<input type="hidden"[^>]*wire:model="due"/')
        ->toContain('wire:model="due"');
});

it('passes min/max through to the embedded calendar', function () {
    $html = Blade::render('<x-sigta::date-picker value="2026-07-15" min="2026-07-10" max="2026-07-20" />');

    expect($html)->toMatch('/data-date="2026-07-09"[^>]*?\sdisabled\s/');
});

it('passes a pinned today through to the embedded calendar', function () {
    $html = Blade::render('<x-sigta::date-picker today="2026-07-21" />');

    expect($html)->toContain('July 2026')->toMatch('/data-date="2026-07-21"[^>]*aria-current="date"/');
});

it('disables the input, the button and the hidden input', function () {
    $html = Blade::render('<x-sigta::date-picker name="locked" value="2026-07-09" disabled />');

    expect($html)
        ->toMatch('/data-part="input"\s+disabled/')
        ->toMatch('/data-part="toggle"\s+disabled/')
        ->toMatch('/<input type="hidden"[^>]*disabled/');
    expect(contractCss('date-picker'))->toContain('disabled:opacity-40 disabled:pointer-events-none');
});

it('shows the error state on the input', function () {
    $html = Blade::render('<x-sigta::date-picker label="Due date" id="dp" error="Pick a date." />');

    expect($html)
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="dp-error"')
        ->toContain('Pick a date.');
    expect(contractCss('date-picker'))->toContain('&[aria-invalid="true"] {')->toContain('@apply border-danger focus:border-ink;');
});

it('drives the picker from the sigtaDatePicker and sigtaDropdown Alpine components', function () {
    $html = Blade::render('<x-sigta::date-picker value="2026-07-09" />');

    expect($html)
        ->toContain('x-data="sigtaDatePicker(')
        ->toContain('x-data="sigtaDropdown"')
        ->toContain('x-on:calendar-change="pick($event.detail); close(true)"')
        ->not->toContain('x-transition');
});
