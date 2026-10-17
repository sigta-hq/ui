import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'tabs.tab',
    title: 'Tab',
    tier: 'components',
    page: 'tabs',
    svelte: 'Tabs.Tab',
    description: 'One tab (role="tab"): a mono button, graphite until hovered, ink with an ink underline while selected. Its name pairs it with the tabs.panel of the same name.',
    aria: 'role="tab" with aria-selected and aria-controls; roving tabindex.',
    props: {
        name: { type: 'string', required: true, description: 'Pairs the tab with its panel (and makes its id: sigta-tab-<name>).' },
    },
    states: {
        selected: { attribute: 'aria-selected', values: ['true', 'false'], description: 'The selected tab.' },
    },
    slots: { default: { description: 'The tab label.', required: true } },
    uses: ['tabs', 'tabs.panel'],
    examples: [
        {
            label: 'Hovering an unselected tab',
            tree: [c('tabs', { default: 'one' },
                c('tabs.list', {}, c('tabs.tab', { name: 'one' }, 'One'), c('tabs.tab', { name: 'two' }, 'Two')),
                c('tabs.panel', { name: 'one' }, 'First.'),
                c('tabs.panel', { name: 'two' }, 'Second.'),
            )],
            interactions: [{ hover: '[role="tab"][data-name="two"]' }],
        },
    ],
});
