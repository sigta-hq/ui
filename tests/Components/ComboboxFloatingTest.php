<?php

use Illuminate\Support\Facades\Blade;

/**
 * Guards the two combobox fixes:
 *  - floating panels use fixed positioning + x-ref anchor/panel so they escape
 *    ancestor overflow (a card/form bounding box) instead of being clipped;
 *  - (JS, in sigtaCombobox) options() queries $root not $el, so hovering
 *    clears the previous active option. That behavior is Alpine-runtime and
 *    not unit-testable here, but the markup contract below must hold for it.
 */

it('renders the autocomplete panel as a fixed, anchored floater', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::autocomplete label="Country">
            <x-sigta::autocomplete.option value="dk">Denmark</x-sigta::autocomplete.option>
        </x-sigta::autocomplete>
    BLADE);

    expect($html)
        ->toContain('x-ref="anchor"')
        ->toContain('x-ref="panel"')
        ->toContain('role="listbox"')
        ->toContain('data-part="listbox"');

    // fixed, not the old absolute-in-relative that clipped
    expect(contractCss('autocomplete'))->toContain('@apply fixed z-50')->not->toContain('absolute top-full inset-x-0');
});

it('renders the pillbox panel as a fixed, anchored floater', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::pillbox label="Tags">
            <x-sigta::autocomplete.option value="a">Alpha</x-sigta::autocomplete.option>
        </x-sigta::pillbox>
    BLADE);

    expect($html)
        ->toContain('x-ref="anchor"')
        ->toContain('x-ref="panel"')
        ->toContain('class="fixed z-50')
        ->not->toContain('absolute top-full inset-x-0');
});

it('prevents mousedown on options so selection registers before blur closes the panel', function () {
    $html = Blade::render('<x-sigta::autocomplete.option value="dk">Denmark</x-sigta::autocomplete.option>');

    // Without this, clicking an option blurs the field → focusout closes the
    // panel before the click fires → nothing gets selected.
    expect($html)
        ->toContain('mousedown.prevent')
        ->toContain('choose($el)');
});

it('renders the listbox panel as a fixed, anchored floater', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::listbox label="Country">
            <x-sigta::autocomplete.option value="dk">Denmark</x-sigta::autocomplete.option>
        </x-sigta::listbox>
    BLADE);

    expect($html)
        ->toContain('x-ref="anchor"')
        ->toContain('x-ref="panel"')
        ->toContain('class="fixed z-50')
        ->not->toContain('absolute top-full inset-x-0');
});
