import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'table.column',
    title: 'Table column',
    tier: 'components',
    page: 'table',
    svelte: 'Table.Column',
    description: 'A header cell: <th scope="col"> in mono uppercase graphite over a hairline.',
    aria: 'A native <th scope="col"> (pass scope to override).',
    props: {},
    slots: { default: { description: 'The column heading.', required: true } },
    uses: ['table.columns'],
    usage: {
        livewire: '<sigta:table.column>Revenue</sigta:table.column>',
        svelte: '<Table.Column>Revenue</Table.Column>',
    },
    examples: [
        {
            label: 'Header cells',
            tree: [c('table', {}, c('table.columns', {}, c('table.column', {}, 'Project'), c('table.column', { scope: 'colgroup' }, 'Margin')))],
        },
    ],
});
