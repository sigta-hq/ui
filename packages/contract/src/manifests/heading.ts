import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'heading',
    title: 'Heading',
    tier: 'elements',
    page: 'typography',
    svelte: 'Heading',
    description: 'A Fraunces heading at the brand scale. The level picks the tag (h1–h6) and a default size; size overrides the look without changing the outline.',
    aria: 'Native h1–h6; keep levels in document order and pick the look with size.',
    props: {
        level: { type: 'number', default: 2, description: 'Heading level 1–6 (the tag).' },
        size: { type: 'enum', values: ['display', 'xl', 'lg', 'base', 'sm'], default: null, description: 'The look; defaults by level (1 → xl, 2 → lg, 3 → base, else sm).' },
    },
    slots: { default: { description: 'The heading text; a <sigta:mark> can highlight a word.', required: true } },
    examples: [
        {
            label: 'Scale',
            tree: [
                c('heading', { level: 2, size: 'display' }, 'Written by hand'),
                c('heading', { level: 2, size: 'xl' }, 'Software, shipped like a product'),
                c('heading', { level: 3, size: 'lg' }, 'What we do, and keep doing'),
                c('heading', { level: 4 }, 'Panel heading'),
            ],
        },
    ],
});
