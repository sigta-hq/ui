import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'tabs.list',
    title: 'Tabs list',
    tier: 'components',
    page: 'tabs',
    svelte: 'Tabs.List',
    description: 'The row of tabs on a hairline (role="tablist"), with arrow-key roving: Left / Right wrap round, Home / End jump to the ends.',
    aria: 'role="tablist" labelled by label.',
    props: {
        label: { type: 'string', default: 'Tabs', description: 'The tablist\'s accessible name (aria-label).' },
    },
    slots: { default: { description: 'The tabs.tabs.', required: true } },
    uses: ['tabs', 'tabs.tab'],
    examples: [
        {
            label: 'Labelled',
            tree: [c('tabs', { default: 'week' },
                c('tabs.list', { label: 'Range' }, c('tabs.tab', { name: 'week' }, 'Week'), c('tabs.tab', { name: 'month' }, 'Month')),
                c('tabs.panel', { name: 'week' }, 'This week.'),
                c('tabs.panel', { name: 'month' }, 'This month.'),
            )],
        },
    ],
});
