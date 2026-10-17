import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'shell.bar',
    title: 'Shell bar',
    tier: 'layout',
    page: 'shell',
    svelte: 'Shell.Bar',
    description: 'The top bar of a page in the shell: heading + mono sub on the left, an actions slot (segmented, pills, buttons) on the right; wraps on narrow screens.',
    aria: 'A <header>; the heading is an <h3>.',
    props: {
        heading: { type: 'string', default: null, description: 'The page heading.' },
        sub: { type: 'string', default: null, description: 'A mono line under the heading ("Q2 2026 · updated 6 min ago").' },
    },
    parts: {
        start: { description: 'The heading and sub, stacked.' },
        heading: { description: 'The heading.' },
        sub: { description: 'The mono sub line.' },
        actions: { description: 'The actions on the right.' },
    },
    slots: {
        actions: { description: 'Controls on the right: a segmented range, pills, buttons.' },
    },
    usage: {
        livewire: `<sigta:shell.bar heading="Overview" sub="Q2 2026 · updated 6 min ago">
    <x-slot:actions>
        <sigta:segmented name="range" label="Range" :options="['7d' => '7d', '30d' => '30d']" checked="30d" />
        <sigta:pill variant="success">On track</sigta:pill>
    </x-slot:actions>
</sigta:shell.bar>`,
        svelte: `<Shell.Bar heading="Overview" sub="Q2 2026 · updated 6 min ago">
    {#snippet actions()}
        <Segmented name="range" label="Range" options={{ '7d': '7d', '30d': '30d' }} checked="30d" />
        <Pill variant="success">On track</Pill>
    {/snippet}
</Shell.Bar>`,
    },
    notes: {
        shared: ['Put it first in a shell.main.'],
        livewire: ['The actions are a named slot: <x-slot:actions>.'],
        svelte: ['The actions are a snippet: {#snippet actions()}…{/snippet}.'],
    },
    examples: [
        {
            label: 'Heading, sub and actions',
            tree: [
                c('shell.bar', { heading: 'Overview', sub: 'Q2 2026 · updated 6 min ago' },
                    { actions: [
                        c('segmented', { name: 'bar-range', label: 'Range', options: { '7d': '7d', '30d': '30d', qtd: 'QTD' }, checked: '30d' }),
                        c('pill', { variant: 'success' }, 'On track'),
                    ] },
                ),
            ],
        },
        {
            label: 'Bar — heading only, actions only',
            tree: [
                el('div', { class: 'flex flex-col gap-5' },
                    c('shell.bar', { heading: 'Reports' }),
                    c('shell.bar', { sub: 'Filtered · 12 rows' },
                        { actions: [c('button', { size: 'sm' }, 'Export')] },
                    ),
                ),
            ],
        },
    ],
});
