<?php

use Illuminate\Support\Facades\Blade;

function renderKitTable(string $tableAttributes = ''): string
{
    return Blade::render(<<<BLADE
        <x-sigta::table caption="Recent engagements" {$tableAttributes}>
            <x-sigta::table.columns>
                <x-sigta::table.column>Project</x-sigta::table.column>
                <x-sigta::table.column>Margin</x-sigta::table.column>
            </x-sigta::table.columns>
            <x-sigta::table.rows>
                <x-sigta::table.row>
                    <x-sigta::table.cell>Marginalia v3</x-sigta::table.cell>
                    <x-sigta::table.cell>62%</x-sigta::table.cell>
                </x-sigta::table.row>
            </x-sigta::table.rows>
        </x-sigta::table>
        BLADE);
}

it('renders semantic table structure with a caption', function () {
    $html = renderKitTable();

    expect($html)
        ->toContain('data-sigta="table"')
        ->toContain('<table data-scope="table" data-part="table"')
        ->toContain('<caption data-scope="table" data-part="caption"')
        ->toContain('Recent engagements')
        ->toContain('<thead')
        ->toContain('<tbody')
        ->toContain('<th ')
        ->toContain('<td ')
        ->toContain('Marginalia v3');

    // The wrapper scrolls sideways when the table does not fit.
    expect(contractCss('table'))->toMatch('/\[data-sigta="table"\] \{\s*@apply w-full overflow-x-auto;/');
});

it('styles headers as mono uppercase and cells with tabular numerals', function () {
    $html = renderKitTable();

    expect($html)
        ->toContain('<th data-sigta="table.column" scope="col"')
        ->toContain('<td data-sigta="table.cell"')
        ->toContain('<tbody data-sigta="table.rows"')
        ->toContain('<thead data-sigta="table.columns"')
        ->toContain('<tr data-sigta="table.row"');

    $css = contractCss('table');
    expect($css)
        ->toMatch('/\[data-sigta="table\.column"\] \{\s*@apply[^;]*font-mono[^;]*uppercase[^;]*;/')
        ->toMatch('/\[data-sigta="table\.cell"\] \{\s*@apply[^;]*tabular-nums[^;]*;/')
        // The last row draws no rule under it.
        ->toMatch('/\[data-sigta="table\.rows"\] > tr:last-child > td \{\s*@apply border-b-0;/');
});

it('omits the caption when none is given', function () {
    $html = Blade::render('<x-sigta::table><x-sigta::table.rows></x-sigta::table.rows></x-sigta::table>');

    expect($html)->not->toContain('<caption');
});

it('forwards attributes onto the native table element', function () {
    $html = renderKitTable('data-test="engagements" wire:key="t1"');

    expect($html)
        ->toMatch('/<table data-scope="table" data-part="table"[^>]*data-test="engagements"/')
        ->toMatch('/<table [^>]*wire:key="t1"/');
});

it('lets a header cell override its scope', function () {
    expect(Blade::render('<x-sigta::table.column scope="colgroup">Q3</x-sigta::table.column>'))
        ->toContain('scope="colgroup"')
        ->not->toContain('scope="col"');
});
