import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'table.cell',
    title: 'Table cell',
    tier: 'components',
    page: 'table',
    svelte: 'Table.Cell',
    description: 'A body cell (<td>) over a hairline, in tabular numerals.',
    aria: 'A native <td>.',
    props: {},
    slots: { default: { description: 'The cell content: text, a figure, a pill.', required: true } },
    uses: ['table.row'],
    usage: {
        livewire: '<sigta:table.cell>$12,400</sigta:table.cell>',
        svelte: '<Table.Cell>$12,400</Table.Cell>',
    },
    examples: [
        {
            label: 'Cells',
            tree: [c('table', {}, c('table.rows', {}, c('table.row', {}, c('table.cell', {}, 'July'), c('table.cell', { colspan: '2' }, '$12,400'))))],
        },
    ],
});
