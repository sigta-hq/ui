import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const kicker = el('div', { class: 'font-mono text-[0.7rem] tracking-[0.12em] uppercase text-graphite flex justify-between' }, el('span', {}, 'Our product'), el('span', {}, 'SaaS · live'));

export default defineComponent({
    name: 'card',
    title: 'Card',
    tier: 'components',
    page: 'card',
    svelte: 'Card',
    description: 'Raised surface on a hairline with an optional thumb above a padded body. Lifts on hover when hover=true; clips its content (use the panel where something must overhang).',
    aria: 'A <div>; put the heading and any link inside.',
    props: {
        hover: { type: 'boolean', default: true, description: 'Lift with a paper shadow on hover (for a card that is or holds an action); false for a static card.' },
    },
    parts: {
        thumb: { description: 'The 16:9 media area above the body (only with a thumb).' },
        body: { description: 'The padded content.' },
    },
    states: {
        hover: { attribute: 'data-hover', description: 'Lifts on hover.' },
        padding: { attribute: 'data-padding', values: ['custom'], description: 'The consumer\'s class sets a padding, which replaces the body\'s own.' },
    },
    slots: {
        default: { description: 'The body.', required: true },
        thumb: { description: 'Media above the body (an SVG, an image, a skeleton), clipped to 16:9.' },
    },
    usage: {
        livewire: `<sigta:card>
    <x-slot:thumb><img src="/shots/marginalia.png" alt="" class="size-full object-cover"></x-slot:thumb>
    <sigta:heading level="3">Marginalia</sigta:heading>
    <sigta:text variant="muted">A reading-and-notes app for teams.</sigta:text>
</sigta:card>
<sigta:card :hover="false" class="p-5">Static, with its own padding</sigta:card>`,
        svelte: `<Card>
    {#snippet thumb()}<img src="/shots/marginalia.png" alt="" class="size-full object-cover" />{/snippet}
    <Heading level={3}>Marginalia</Heading>
    <Text variant="muted">A reading-and-notes app for teams.</Text>
</Card>
<Card hover={false} class="p-5">Static, with its own padding</Card>`,
    },
    notes: {
        shared: [
            'Its body has fluid padding. Pass any padding class on the card (p-5, sm:p-4) and it replaces the body padding instead of adding to it.',
        ],
        livewire: ['The thumb is a named slot: <x-slot:thumb>…</x-slot:thumb>. Attributes on the slot tag land on the thumb wrapper.'],
        svelte: ['The thumb is a snippet: {#snippet thumb()}…{/snippet}.'],
    },
    examples: [
        {
            label: 'With thumb — hover lift',
            tree: [el('div', { class: 'grid gap-6 sm:grid-cols-2' },
                c('card', {},
                    { thumb: [el('svg', { viewBox: '0 0 400 225', preserveAspectRatio: 'xMidYMid slice', class: 'absolute inset-0 size-full text-ink', 'aria-hidden': 'true' },
                        el('g', { stroke: 'var(--sigta-line)', 'stroke-width': '1' }, el('path', { d: 'M0 56 H400' }), el('path', { d: 'M0 112 H400' }), el('path', { d: 'M0 168 H400' })),
                        el('rect', { x: '290', y: '40', width: '60', height: '34', rx: '3', fill: 'var(--sigta-accent)', opacity: '.9' }),
                        el('path', { d: 'M30 170 C90 160 110 120 170 110 S270 80 320 60 380 48 380 48', fill: 'none', stroke: 'currentColor', 'stroke-width': '3', 'stroke-linecap': 'round' }),
                        el('circle', { cx: '380', cy: '48', r: '5', fill: 'currentColor' }),
                    )] },
                    kicker,
                    el('h3', { class: 'font-head font-medium text-[1.5rem] mt-2 mb-1.5' }, 'Marginalia'),
                    el('p', { class: 'text-graphite text-[0.96rem] m-0' }, 'A reading-and-notes app for teams — annotate any doc, search every margin.'),
                ),
                c('card', {},
                    { thumb: [el('svg', { viewBox: '0 0 400 225', preserveAspectRatio: 'xMidYMid slice', class: 'absolute inset-0 size-full text-ink', 'aria-hidden': 'true' },
                        el('g', { fill: 'currentColor', opacity: '.85' },
                            el('rect', { x: '40', y: '120', width: '36', height: '70', rx: '3' }),
                            el('rect', { x: '100', y: '90', width: '36', height: '100', rx: '3' }),
                            el('rect', { x: '160', y: '105', width: '36', height: '85', rx: '3' }),
                            el('rect', { x: '220', y: '64', width: '36', height: '126', rx: '3' }),
                        ),
                        el('rect', { x: '280', y: '80', width: '36', height: '110', rx: '3', fill: 'var(--sigta-accent)' }),
                        el('rect', { x: '280', y: '80', width: '36', height: '110', rx: '3', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }),
                        el('path', { d: 'M58 110 118 80 178 96 238 56 298 70', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-dasharray': '2 6' }),
                    )] },
                    kicker,
                    el('h3', { class: 'font-head font-medium text-[1.5rem] mt-2 mb-1.5' }, 'Plumb'),
                    el('p', { class: 'text-graphite text-[0.96rem] m-0' }, 'Metrics without the dashboard fatigue. One honest number per question.'),
                ),
            )],
            interactions: [{ hover: '[data-sigta="card"]' }],
        },
        {
            label: 'Body only — static (no hover lift)',
            tree: [el('div', { class: 'max-w-md' },
                c('card', { hover: false },
                    el('h3', { class: 'font-head font-medium text-[1.28rem] mb-1.5' }, 'Halyard OS'),
                    el('p', { class: 'text-graphite text-[0.96rem] m-0' }, 'A dispatch and routing console for a freight client. 4,000 trucks, one calm screen.'),
                ),
            )],
        },
        {
            label: 'Own padding (replaces the body\'s)',
            tree: [el('div', { class: 'max-w-md' },
                c('card', { class: 'p-5' }, el('p', { class: 'text-graphite text-[0.96rem] m-0' }, 'A p-5 on the card replaces the body padding instead of stacking with it.')),
            )],
        },
    ],
});
