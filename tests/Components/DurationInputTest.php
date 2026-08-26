<?php

use Illuminate\Support\Facades\Blade;

it('renders a compact display button and a hidden HH:MM editor', function () {
    $html = Blade::render('<x-sigta::duration-input label="Estimate" :value="119" />');

    expect($html)
        ->toContain('data-sigta="duration-input"')
        ->toContain('data-test="duration-display"')
        ->toContain('data-test="duration-editor"')
        ->toContain('>1h59m</button>')   // 119 min, compact, no space — matches the JS getter
        ->toContain('Estimate');         // field label
});

it('shows the placeholder when there is no value', function () {
    $html = Blade::render('<x-sigta::duration-input />');

    expect($html)
        ->toContain('>0m</button>')
        ->toContain('minutes: null');
});

it('accepts a custom placeholder', function () {
    expect(Blade::render('<x-sigta::duration-input placeholder="—" />'))
        ->toContain('>—</button>');
});

it('seeds the hidden total and Alpine state from the value', function () {
    $html = Blade::render('<x-sigta::duration-input :value="90" />');

    expect($html)
        ->toContain('type="hidden"')
        ->toContain('value="90"')
        ->toContain('minutes: 90')
        ->toContain('>1h30m</button>');
});

it('forwards wire:model onto the hidden total input', function () {
    $html = Blade::render('<x-sigta::duration-input wire:model="estimate" :value="60" />');

    expect($html)->toMatch('/<input type="hidden"[^>]*wire:model="estimate"/');
});

it('wires forgiving parsing and commit-on-enter/escape', function () {
    $html = Blade::render('<x-sigta::duration-input />');

    expect($html)
        ->toContain('parse(this.draft)')
        ->toContain("new Event('input'")
        ->toContain('x-on:keydown.enter.prevent="commit()"')
        ->toContain('x-on:keydown.escape.prevent="cancel()"');
});

it('forwards a value-keyed wire:key onto the field', function () {
    $html = Blade::render('<x-sigta::duration-input wire:model="estimate" wire:key="est-60" :value="60" />');

    expect($html)->toContain('wire:key="est-60"');
});
