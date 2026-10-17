import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'rule',
    title: 'Rule',
    tier: 'elements',
    page: 'typography',
    svelte: 'Rule',
    description: 'The brand\'s hand-drawn squiggle divider: an inline SVG hairline in currentColor. Decorative divider, aria-hidden.',
    aria: 'Decorative: aria-hidden.',
    props: {
        variant: { type: 'number', default: 1, description: 'Which of the three hand-drawn squiggle paths: 1, 2 or 3 (other numbers wrap round).' },
    },
    parts: {
        line: { description: 'The SVG that draws the squiggle.' },
    },
    usage: {
        livewire: '<sigta:rule variant="2" />',
        svelte: '<Rule variant={2} />',
    },
    notes: {
        shared: ['Use it between page sections; inside content, a plain {{tag:separator}} (an <hr>) is the quieter choice.'],
    },
    examples: [
        {
            label: 'Variants',
            tree: [el('div', { class: 'flex flex-col gap-6' },
                c('rule', { variant: 1 }),
                c('rule', { variant: 2 }),
                c('rule', { variant: 3 }),
            )],
        },
    ],
});
