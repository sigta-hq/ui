<?php

use Illuminate\Support\Facades\Blade;

it('renders the dark bar with a pulsing dot, title, project and clock', function () {
    $html = Blade::render('<x-sigta::timer-bar title="Write the spec" project="Atlas" since="2026-08-25T09:00:00+00:00" />');

    expect($html)
        ->toContain('data-sigta="timer-bar"')
        ->toContain('bg-inverse')
        ->toContain('animate-pulse')
        ->toContain('Write the spec')
        ->toContain('Atlas')
        ->toContain('data-sigta="elapsed-clock"')   // clock composed in
        ->toContain('2026-08-25T09:00:00');          // clock seeded from :since
});

it('renders a default stop button that dispatches sigta-timer-stop', function () {
    $html = Blade::render('<x-sigta::timer-bar title="Task" />');

    expect($html)
        ->toContain('data-test="timer-stop"')
        ->toContain("\$dispatch('sigta-timer-stop')")
        ->toContain('Stop');
});

it('lets a stop slot replace the default control', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::timer-bar title="Task">
            <x-slot:stop>
                <button type="button" wire:click="stop" data-test="custom-stop">Halt</button>
            </x-slot:stop>
        </x-sigta::timer-bar>
    BLADE);

    expect($html)
        ->toContain('data-test="custom-stop"')
        ->toContain('wire:click="stop"')
        ->not->toContain('data-test="timer-stop"');   // default button replaced
});

it('accepts a custom stop label', function () {
    expect(Blade::render('<x-sigta::timer-bar title="T" stop-label="End" />'))
        ->toContain('End');
});

it('forwards attributes onto the bar root', function () {
    $html = Blade::render('<x-sigta::timer-bar title="T" data-test="bar" wire:key="tb" />');

    expect($html)
        ->toContain('data-test="bar"')
        ->toContain('wire:key="tb"');
});
