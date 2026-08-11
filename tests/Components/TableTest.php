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
        ->toContain('overflow-x-auto')
        ->toContain('<table')
        ->toContain('<caption')
        ->toContain('Recent engagements')
        ->toContain('<thead')
        ->toContain('<tbody')
        ->toContain('<th ')
        ->toContain('<td ')
        ->toContain('Marginalia v3');
});

it('styles headers as mono uppercase and cells with tabular numerals', function () {
    $html = renderKitTable();

    expect($html)
        ->toContain('scope="col"')
        ->toContain('font-mono')
        ->toContain('uppercase')
        ->toContain('tabular-nums')
        ->toContain('[&amp;&gt;tr:last-child&gt;td]:border-b-0'); // escaped by the attribute bag; decodes to [&>tr:last-child>td]:border-b-0
});

it('omits the caption when none is given', function () {
    $html = Blade::render('<x-sigta::table><x-sigta::table.rows></x-sigta::table.rows></x-sigta::table>');

    expect($html)->not->toContain('<caption');
});

it('forwards attributes onto the native table element', function () {
    $html = renderKitTable('data-test="engagements" wire:key="t1"');

    expect($html)
        ->toContain('data-test="engagements"')
        ->toContain('wire:key="t1"');
});
