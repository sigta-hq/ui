import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'tabs.panel',
    title: 'Tab panel',
    tier: 'components',
    page: 'tabs',
    svelte: 'Tabs.Panel',
    description: 'The panel of the tab with the same name (role="tabpanel"), shown while that tab is selected.',
    aria: 'role="tabpanel" labelled by its tab; focusable (tabindex 0).',
    props: {
        name: { type: 'string', required: true, description: 'The tab it belongs to (its id: sigta-panel-<name>).' },
    },
    slots: { default: { description: 'The panel content.', required: true } },
    uses: ['tabs', 'tabs.tab'],
    examples: [
        {
            label: 'The second panel shown',
            tree: [c('tabs', { default: 'notes' },
                c('tabs.list', {}, c('tabs.tab', { name: 'summary' }, 'Summary'), c('tabs.tab', { name: 'notes' }, 'Notes')),
                c('tabs.panel', { name: 'summary' }, c('text', {}, 'The summary.')),
                c('tabs.panel', { name: 'notes' }, c('text', { variant: 'muted' }, 'No notes yet.')),
            )],
        },
    ],
});
