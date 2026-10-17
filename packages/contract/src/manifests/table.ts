import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

const row = (...cells: (string | ReturnType<typeof c>)[]) => c('table.row', {}, ...cells.map((cell) => c('table.cell', {}, cell)));

export default defineComponent({
    name: 'table',
    title: 'Table',
    tier: 'components',
    page: 'table',
    svelte: 'Table',
    description: 'Brand data table: mono uppercase caption and headers, hairline rows, tabular numerals. Scrolls sideways when it does not fit.',
    aria: 'A native <table>, captioned with <caption>; header cells are <th scope="col">.',
    props: {
        caption: { type: 'string', default: null, description: 'Mono uppercase caption above the table.' },
    },
    parts: {
        table: { description: 'The <table>; your attributes and class land here (the root is the scrolling wrapper).' },
        caption: { description: 'The caption.' },
    },
    slots: { default: { description: 'A table.columns header row and table.rows.', required: true } },
    uses: ['table.columns', 'table.column', 'table.rows', 'table.row', 'table.cell'],
    usage: {
        livewire: `<sigta:table caption="Q3 revenue">
    <sigta:table.columns>
        <sigta:table.column>Month</sigta:table.column>
        <sigta:table.column>Revenue</sigta:table.column>
    </sigta:table.columns>
    <sigta:table.rows>
        <sigta:table.row>
            <sigta:table.cell>July</sigta:table.cell>
            <sigta:table.cell>$12,400</sigta:table.cell>
        </sigta:table.row>
    </sigta:table.rows>
</sigta:table>`,
        svelte: `<Table caption="Q3 revenue">
    <Table.Columns>
        <Table.Column>Month</Table.Column>
        <Table.Column>Revenue</Table.Column>
    </Table.Columns>
    <Table.Rows>
        <Table.Row>
            <Table.Cell>July</Table.Cell>
            <Table.Cell>$12,400</Table.Cell>
        </Table.Row>
    </Table.Rows>
</Table>`,
    },
    notes: {
        shared: [
            'Build it from table.columns (the <thead> row) with a table.column per header, and table.rows (the <tbody>) with table.row and table.cell.',
            'Cells use tabular numerals, so figures line up; the last row draws no rule under it.',
        ],
        livewire: ['Attributes (class, wire:key, data-*) land on the <table>, not on the scrolling wrapper. Loop rows with @foreach and give each table.row a wire:key.'],
        svelte: ['Attributes land on the <table>, not on the scrolling wrapper. Loop rows with a keyed {#each}.'],
    },
    examples: [
        {
            label: 'Data table — mono caption and headers, tabular numerals',
            tree: [
                c('table', { caption: 'Recent engagements' },
                    c('table.columns', {}, c('table.column', {}, 'Project'), c('table.column', {}, 'Stage'), c('table.column', {}, 'Margin')),
                    c('table.rows', {},
                        row('Marginalia v3', 'Shipping', '62%'),
                        row('Halyard OS', 'Active', '41%'),
                        row('Ledger rebuild', 'At risk', '28%'),
                    ),
                ),
            ],
        },
        {
            label: 'Without caption',
            tree: [
                c('table', {},
                    c('table.columns', {}, c('table.column', {}, 'Metric'), c('table.column', {}, 'Value')),
                    c('table.rows', {}, row('Projects shipped', '34'), row('Avg. margin', '44%')),
                ),
            ],
        },
        {
            label: 'Q3 revenue',
            tree: [
                c('table', { caption: 'Q3 revenue' },
                    c('table.columns', {}, c('table.column', {}, 'Month'), c('table.column', {}, 'Revenue')),
                    c('table.rows', {}, row('July', '$12,400'), row('August', '$13,150'), row('September', '$15,820')),
                ),
            ],
        },
    ],
});
