import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

// The kit's tab group; a prefix keeps each example's tab ids unique on one page.
const group = (prefix: string, props: Record<string, unknown> = {}) => c('tabs', props,
    c('tabs.list', { label: 'Sections' },
        c('tabs.tab', { name: `${prefix}overview` }, 'Overview'),
        c('tabs.tab', { name: `${prefix}trends` }, 'Trends'),
        c('tabs.tab', { name: `${prefix}reports` }, 'Reports'),
    ),
    c('tabs.panel', { name: `${prefix}overview` }, c('text', {}, 'The overview panel — keyboard: arrow keys move between tabs.')),
    c('tabs.panel', { name: `${prefix}trends` }, c('text', {}, 'Trends panel.')),
    c('tabs.panel', { name: `${prefix}reports` }, c('text', {}, 'Reports panel.')),
);

export default defineComponent({
    name: 'tabs',
    title: 'Tabs',
    tier: 'components',
    page: 'tabs',
    svelte: 'Tabs',
    description: 'A tab group: a tabs.list of tabs.tabs over their tabs.panels, one panel shown at a time. Mono tabs on a hairline; the selected one is ink with an ink underline.',
    aria: 'WAI-ARIA tabs with automatic activation: Left / Right move between tabs (wrapping), Home / End jump to the ends; only the selected tab is in the tab order.',
    props: {
        default: { type: 'string', default: null, description: 'The tab active on load (rendered selected on the server); the first tab when unset.' },
        value: { type: 'string', default: null, bindable: true, blade: false, description: 'bind:value reads and sets the active tab\'s name.' },
    },
    slots: { default: { description: 'A tabs.list and the tabs.panels.', required: true } },
    uses: ['tabs.list', 'tabs.tab', 'tabs.panel'],
    usage: {
        livewire: `<sigta:tabs default="overview">
    <sigta:tabs.list>
        <sigta:tabs.tab name="overview">Overview</sigta:tabs.tab>
        <sigta:tabs.tab name="billing">Billing</sigta:tabs.tab>
    </sigta:tabs.list>
    <sigta:tabs.panel name="overview">…</sigta:tabs.panel>
    <sigta:tabs.panel name="billing">…</sigta:tabs.panel>
</sigta:tabs>`,
        svelte: `<Tabs bind:value={tab} default="overview">
    <Tabs.List>
        <Tabs.Tab name="overview">Overview</Tabs.Tab>
        <Tabs.Tab name="billing">Billing</Tabs.Tab>
    </Tabs.List>
    <Tabs.Panel name="overview">…</Tabs.Panel>
    <Tabs.Panel name="billing">…</Tabs.Panel>
</Tabs>`,
    },
    notes: {
        shared: [
            'Use it instead of custom tab JS. A tab and its panel share a name; the ids (sigta-tab-<name>, sigta-panel-<name>) derive from it, so names are unique per page.',
        ],
        livewire: ['The active tab is client state (Alpine); `default` picks it on load.'],
        svelte: ['bind:value to read or switch the active tab; default only seeds it.'],
    },
    examples: [
        {
            label: 'Usage',
            tree: [c('tabs', { default: 'usage-overview' },
                c('tabs.list', {},
                    c('tabs.tab', { name: 'usage-overview' }, 'Overview'),
                    c('tabs.tab', { name: 'usage-billing' }, 'Billing'),
                ),
                c('tabs.panel', { name: 'usage-overview' }, '…'),
                c('tabs.panel', { name: 'usage-billing' }, '…'),
            )],
        },
        { label: 'Tab group', tree: [group('', { default: 'overview' })] },
        {
            label: 'Selected by a click',
            tree: [group('click-', { default: 'click-overview' })],
            interactions: [{ click: '[role="tab"][data-name="click-trends"]' }],
        },
        {
            label: 'Keyboard: arrows wrap, End jumps to the last tab',
            tree: [group('keys-', { default: 'keys-overview' })],
            interactions: [{ focus: '[role="tab"][data-name="keys-overview"]', press: ['ArrowLeft'] }, { press: ['Home', 'ArrowRight'] }, { press: 'End' }],
        },
        {
            label: 'No default — the first tab',
            tree: [group('first-')],
        },
    ],
});
