import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

// A transformed frame holds the fixed bar, so the demo shows it in place
// rather than at the bottom of the window.
const frame = (...children: ReturnType<typeof c>[]) => el('div', { class: 'relative h-28 transform-gpu rounded-panel border border-dashed border-line' }, ...children);

const timer = [
    el('span', { class: 'size-2 shrink-0 rounded-full bg-danger', 'aria-hidden': 'true' }),
    el('span', { class: 'truncate font-head text-[0.95rem]' }, 'Redesign the pricing page'),
    c('button', { size: 'sm', variant: 'inverse' }, 'Stop'),
];

export default defineComponent({
    name: 'floating-bar',
    title: 'Floating bar',
    tier: 'components',
    page: 'floating-bar',
    svelte: 'FloatingBar',
    description: 'A dark pill pinned to the bottom centre of the viewport that slides up when show turns true and away when it turns false — for state that lasts while the member works (a running timer, a bulk selection). A first render that is already shown appears without animating.',
    aria: 'With a label the bar is a named region (role="region").',
    props: {
        show: { type: 'boolean', default: false, description: 'Whether the bar is up.' },
        label: { type: 'string', default: null, description: 'Names the bar as a region ("Running timer").' },
    },
    parts: {
        bar: { description: 'The pill; the slot sits in it.' },
    },
    states: {
        show: { attribute: 'data-show', values: ['true', 'false'], description: 'On the root; the runtime shows or hides the bar when it changes.' },
    },
    slots: { default: { description: 'The bar\'s content: a status dot, a title, a clock, a button.', required: true } },
    usage: {
        livewire: '<sigta:floating-bar :show="$entry !== null" label="Running timer">…</sigta:floating-bar>',
        svelte: '<FloatingBar show={entry !== null} label="Running timer">…</FloatingBar>',
    },
    notes: {
        shared: [
            'Content is your slot. While it is up it sets --sigta-floating-space on <html>, which shell.main and the bottom toasts add to their bottom edge, so nothing ends up under it; if your app scrolls another element, add pb-[var(--sigta-floating-space,0px)] to it.',
        ],
        livewire: [
            'Drive show from Livewire state; the morph updates data-show in place and the bar animates. Wrap it in @persist when it lives in a wire:navigate layout, so it does not replay on every page.',
        ],
        svelte: ['Drive show from your state; keep it in a layout that persists across page visits so it does not replay on every page.'],
    },
    examples: [
        {
            label: 'Shown — a running timer',
            tree: [frame(c('floating-bar', { show: true, label: 'Running timer' }, ...timer))],
        },
        {
            label: 'Hidden until shown',
            tree: [frame(c('floating-bar', { label: 'Running timer' }, ...timer))],
        },
        {
            label: 'Shown and hidden by state',
            conformance: 'browser',
            blade: `<div x-data="{ running: false }" class="relative flex h-40 transform-gpu flex-wrap items-start gap-3">
    <x-sigta::button x-on:click="running = ! running">Toggle bar</x-sigta::button>
    <x-sigta::floating-bar x-bind:data-show="running ? 'true' : 'false'" label="Running timer">
        <span class="truncate font-head text-[0.95rem]">Redesign the pricing page</span>
        <x-sigta::button size="sm" variant="inverse">Stop</x-sigta::button>
    </x-sigta::floating-bar>
</div>`,
            svelte: `<script>
    import { Button, FloatingBar } from '@sigta-hq/svelte';

    let running = $state(false);
</script>

<div class="relative flex h-40 transform-gpu flex-wrap items-start gap-3">
    <Button onclick={() => (running = !running)}>Toggle bar</Button>
    <FloatingBar show={running} label="Running timer">
        <span class="truncate font-head text-[0.95rem]">Redesign the pricing page</span>
        <Button size="sm" variant="inverse">Stop</Button>
    </FloatingBar>
</div>`,
            interactions: [{ click: '[data-sigta="button"]', wait: 600 }],
        },
    ],
});
