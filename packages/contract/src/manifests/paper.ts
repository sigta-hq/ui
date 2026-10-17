import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'paper',
    title: 'Paper',
    tier: 'layout',
    page: 'paper',
    svelte: 'Paper',
    description: 'Page-ground overlay (dot grid + paper grain), fixed behind the page and never in the way of a click. Place it once per page.',
    aria: 'aria-hidden; pointer-events: none.',
    props: {
        dots: { type: 'boolean', default: true, description: 'The dot grid.' },
        grain: { type: 'boolean', default: true, description: 'The paper grain (an SVG noise filter).' },
    },
    parts: {
        dots: { description: 'The dot grid layer.' },
        grain: { description: 'The grain layer.' },
        noise: { description: 'The SVG inside the grain layer that draws the noise.' },
    },
    usage: {
        livewire: '<body><x-sigta::paper /> … </body>',
        svelte: '<!-- the root layout, once, before the page content -->\n<Paper />\n{@render children()}',
    },
    notes: {
        shared: [
            'It is fixed to the viewport, behind everything: give the page content position: relative and a z-index above 0 (relative z-10) so it sits on the paper.',
            'It takes no other attributes: it is the ground, not a styled box.',
        ],
        livewire: ['Place it once, directly inside <body>.'],
        svelte: ['Place it once, in the root layout before the page content.'],
    },
    examples: [
        {
            label: 'Page ground',
            // The overlay is fixed to the viewport; contain:paint holds it in the demo box.
            tree: [el('div', { class: 'relative h-48 rounded-panel border border-line bg-surface [contain:paint]' }, c('paper', {}))],
        },
        {
            label: 'Dots only',
            tree: [el('div', { class: 'relative h-48 rounded-panel border border-line bg-surface [contain:paint]' }, c('paper', { grain: false }))],
        },
    ],
});
