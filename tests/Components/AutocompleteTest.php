<?php

use Illuminate\Support\Facades\Blade;

it('renders an autocomplete with combobox ARIA wiring', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::autocomplete label="Country" name="country" placeholder="Search…">
            <x-sigta::autocomplete.option value="dk">Denmark</x-sigta::autocomplete.option>
            <x-sigta::autocomplete.option value="se">Sweden</x-sigta::autocomplete.option>
        </x-sigta::autocomplete>
    BLADE);

    expect($html)
        ->toContain('data-sigta="autocomplete"')
        ->toContain('role="combobox"')
        ->toContain('aria-autocomplete="list"')
        ->toContain('role="listbox"')
        ->toContain('role="option"')
        ->toContain('data-value="dk"')
        ->toContain('name="country"')
        ->toContain('Country');
});

it('forwards wire:model to the hidden value input', function () {
    $html = Blade::render('<x-sigta::autocomplete wire:model="country"><x-sigta::autocomplete.option value="dk">Denmark</x-sigta::autocomplete.option></x-sigta::autocomplete>');

    expect($html)->toContain('type="hidden"')->toContain('wire:model="country"');
});

it('shows an error state', function () {
    $html = Blade::render('<x-sigta::autocomplete label="Country" error="Pick one." />');

    expect($html)->toContain('aria-invalid="true"')->toContain('data-part="error"')->toContain('Pick one.');
    expect(contractCss('autocomplete'))->toContain('&[aria-invalid="true"] { @apply border-danger focus:border-ink; }');
});
