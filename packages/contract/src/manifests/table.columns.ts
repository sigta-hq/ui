import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'table.columns',
    title: 'Table columns',
    tier: 'components',
    page: 'table',
    svelte: 'Table.Columns',
    description: 'The header row of a table: a <thead> with one <tr> around the table.column cells.',
    aria: 'A native <thead> holding one <tr>.',
    props: {},
    slots: { default: { description: 'The table.column header cells.', required: true } },
    uses: ['table', 'table.column'],
    usage: {
        livewire: `<sigta:table.columns>
    <sigta:table.column>Month</sigta:table.column>
    <sigta:table.column>Revenue</sigta:table.column>
</sigta:table.columns>`,
        svelte: `<Table.Columns>
    <Table.Column>Month</Table.Column>
    <Table.Column>Revenue</Table.Column>
</Table.Columns>`,
    },
    examples: [
        {
            label: 'Header row',
            tree: [c('table', {}, c('table.columns', {}, c('table.column', {}, 'Month'), c('table.column', {}, 'Revenue')))],
        },
    ],
});
