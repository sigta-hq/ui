import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'count',
    title: 'Count',
    tier: 'elements',
    page: 'motion',
    svelte: 'Count',
    description: 'A number that bumps when it changes — rising as it grows, dropping as it shrinks — whatever changes it (a re-render, a script, a plain DOM write). For unread badges and "12 open" headings. Tabular numerals.',
    aria: 'A plain <span>; the number is its text.',
    props: {
        value: { type: 'number', default: null, description: 'The number; falls back to the slot (children) when unset. Zero is a value.' },
    },
    slots: { default: { description: 'The number, when value is unset.' } },
    usage: {
        livewire: `<sigta:count :value="$openCount" /> open
<sigta:count x-text="items.length">{{ count($items) }}</sigta:count>`,
        svelte: `<Count value={openCount} /> open`,
    },
    notes: {
        shared: [
            'Use it for a count that changes (unread, open) instead of a bare number in a badge.',
            'The bump is a script animation from the shared core; with reduced motion the number just changes.',
        ],
        livewire: ['A Livewire morph or an x-text on the count moves it; give the slot the server-rendered number so it reads right before Alpine starts.'],
        svelte: ['Pass the reactive number as value; the component watches its own text, so any update bumps it.'],
    },
    examples: [
        {
            label: 'A value',
            tree: [el('span', { class: 'font-head text-[2rem] leading-none' }, c('count', { value: 12 }))],
        },
        {
            label: 'Zero, and the slot',
            tree: [el('div', { class: 'flex items-baseline gap-6 font-mono text-[0.68rem] uppercase tracking-[0.14em] text-graphite' },
                el('span', {}, c('count', { value: 0 }), ' open'),
                el('span', {}, c('count', {}, '3'), ' left'),
            )],
        },
        {
            label: 'Rises as it grows, drops as it shrinks',
            conformance: 'browser',
            blade: `<div class="flex items-center gap-4" x-data="{ n: 12 }">
    <x-sigta::button size="sm" x-on:click="n--" aria-label="One less">−</x-sigta::button>
    <span class="font-head text-[2rem] leading-none"><x-sigta::count x-text="n">12</x-sigta::count></span>
    <x-sigta::button size="sm" x-on:click="n++" aria-label="One more">+</x-sigta::button>
</div>`,
            svelte: `<script>
    import { Button, Count } from '@sigta-hq/svelte';

    let n = $state(12);
</script>

<div class="flex items-center gap-4">
    <Button size="sm" onclick={() => n--} aria-label="One less">−</Button>
    <span class="font-head text-[2rem] leading-none"><Count value={n} /></span>
    <Button size="sm" onclick={() => n++} aria-label="One more">+</Button>
</div>`,
            interactions: [{ click: '[aria-label="One more"]' }, { click: '[aria-label="One more"]', wait: 600 }],
        },
    ],
});
