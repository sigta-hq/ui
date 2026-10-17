import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'table.row',
    title: 'Table row',
    tier: 'components',
    page: 'table',
    svelte: 'Table.Row',
    description: 'One body row (<tr>) of table.cell cells.',
    aria: 'A native <tr>.',
    props: {},
    slots: { default: { description: 'The table.cell cells.', required: true } },
    uses: ['table.rows', 'table.cell'],
    usage: {
        livewire: `<sigta:table.row wire:key="month-{{ $month->id }}">
    <sigta:table.cell>{{ $month->name }}</sigta:table.cell>
    <sigta:table.cell>{{ $month->revenue }}</sigta:table.cell>
</sigta:table.row>`,
        svelte: `<Table.Row>
    <Table.Cell>{month.name}</Table.Cell>
    <Table.Cell>{month.revenue}</Table.Cell>
</Table.Row>`,
    },
    examples: [
        {
            label: 'A row',
            tree: [c('table', {}, c('table.rows', {}, c('table.row', { 'data-id': '7' }, c('table.cell', {}, 'July'), c('table.cell', {}, '$12,400'))))],
        },
    ],
});
