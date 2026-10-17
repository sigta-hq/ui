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
        ->toContain('x-data="sigtaDurationInput(\'0m\')"')
        ->toMatch('/<input type="hidden" x-ref="total" value=""/')    // no minutes yet
        ->toMatch('/data-empty=""[^>]*data-part="display"/s');       // drawn in graphite

    expect(contractCss('duration-input'))->toContain('&[data-empty] { @apply text-graphite; }');
});

it('accepts a custom placeholder', function () {
    expect(Blade::render('<x-sigta::duration-input placeholder="—" />'))
        ->toContain('>—</button>')
        ->toContain("sigtaDurationInput('—')");   // the runtime shows it again once cleared
});

it('seeds the hidden total and Alpine state from the value', function () {
    $html = Blade::render('<x-sigta::duration-input :value="90" />');

    expect($html)
        ->toContain('type="hidden"')
        ->toContain('value="90"')                     // the runtime reads its minutes from here
        ->toContain('>1h30m</button>')
        ->not->toMatch('/data-empty=""[^>]*data-part="display"/s');
});

it('forwards wire:model onto the hidden total input', function () {
    $html = Blade::render('<x-sigta::duration-input wire:model="estimate" :value="60" />');

    expect($html)->toMatch('/<input type="hidden"[^>]*wire:model="estimate"/');
});

it('wires forgiving parsing and commit-on-enter/escape', function () {
    $html = Blade::render('<x-sigta::duration-input />');

    expect($html)
        ->toContain('x-on:blur="commit()"')
        ->toContain('x-on:keydown.enter.prevent="commit()"')
        ->toContain('x-on:keydown.escape.prevent="cancel()"');

    // The parse is the core's parseDuration; commit emits onto the hidden input.
    $runtime = file_get_contents(dirname(__DIR__, 2).'/resources/js/sigta.js');
    expect($runtime)
        ->toContain("Alpine.data('sigtaDurationInput'")
        ->toContain('parseDuration(this.draft)')
        ->toContain("new Event('input'")
        ->toContain('if (!this.editing) return;');   // the blur after Escape does not save
});

it('renders contract parts and points its label at the editor', function () {
    $html = Blade::render('<x-sigta::duration-input label="Estimate" :value="119" />');

    expect($html)
        ->not->toContain('class=')
        ->toMatch('/data-scope="duration-input"\\s+data-part="display"/')
        ->toMatch('/data-scope="duration-input"\\s+data-part="editor"/');

    preg_match('/<label[^>]*for="([^"]+)"/', $html, $label);
    expect($html)->toMatch('/id="'.preg_quote($label[1], '/').'"[^>]*data-part="editor"/s');

    expect(contractCss('duration-input'))->toContain('cursor-text text-ink hover:border-ink');
});

it('forwards a value-keyed wire:key onto the field', function () {
    $html = Blade::render('<x-sigta::duration-input wire:model="estimate" wire:key="est-60" :value="60" />');

    expect($html)->toContain('wire:key="est-60"');
});
