import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'table.rows',
    title: 'Table rows',
    tier: 'components',
    page: 'table',
    svelte: 'Table.Rows',
    description: 'The body of a table: a <tbody> around the table.row rows. Its last row draws no rule under it.',
    aria: 'A native <tbody>.',
    props: {},
    slots: { default: { description: 'The table.row rows.', required: true } },
    uses: ['table', 'table.row'],
    usage: {
        livewire: `<sigta:table.rows>
    @foreach ($months as $month)
        <sigta:table.row wire:key="month-{{ $month->id }}">…</sigta:table.row>
    @endforeach
</sigta:table.rows>`,
        svelte: `<Table.Rows>
    {#each months as month (month.id)}
        <Table.Row>…</Table.Row>
    {/each}
</Table.Rows>`,
    },
    examples: [
        {
            label: 'Body rows',
            tree: [c('table', {}, c('table.rows', {},
                c('table.row', {}, c('table.cell', {}, 'July'), c('table.cell', {}, '$12,400')),
                c('table.row', {}, c('table.cell', {}, 'August'), c('table.cell', {}, '$13,150')),
            ))],
        },
    ],
});
