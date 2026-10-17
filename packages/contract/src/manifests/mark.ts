import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'mark',
    title: 'Mark',
    tier: 'elements',
    page: 'typography',
    svelte: 'Mark',
    description: 'Highlighter swipe behind a word or two: a slightly skewed accent stroke drawn under the text. Use sparingly — one per view.',
    aria: 'A plain <span>; the swipe is a pseudo-element, so the text reads as it is.',
    props: {},
    slots: { default: { description: 'The word or two to highlight.', required: true } },
    usage: {
        livewire: '<sigta:heading level="2">One brand, <sigta:mark>every surface</sigta:mark>.</sigta:heading>',
        svelte: '<Heading level={2}>One brand, <Mark>every surface</Mark>.</Heading>',
    },
    notes: {
        shared: [
            'Use sparingly — one mark per view. Accent (highlighter yellow) is scarce: status highlights only, never large fills.',
            'It does not wrap (white-space: nowrap); keep it to a word or two inside a heading or a sentence.',
        ],
    },
    examples: [
        {
            label: 'Highlighter',
            tree: [c('heading', { level: 2, size: 'xl' }, 'Technology, translated to ', c('mark', {}, 'business'))],
        },
        {
            label: 'In a heading',
            tree: [c('heading', { level: 2 }, 'One brand, ', c('mark', {}, 'every surface'))],
        },
    ],
});
