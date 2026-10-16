import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'icon',
    title: 'Icon',
    tier: 'elements',
    page: 'icon',
    svelte: 'Icon',
    description: 'A Lucide icon from the pinned set both runtimes share, at the brand stroke weight (1.75). Decorative unless labelled.',
    aria: 'aria-hidden by default; pass aria-label and aria-hidden="false" for a meaningful icon.',
    props: {
        name: { type: 'string', required: true, description: 'Lucide icon name, e.g. arrow-right, check, x, calendar.' },
        size: { type: 'enum', values: ['xs', 'sm', 'base', 'lg'], default: 'base', description: 'xs 14px, sm 16px, base 20px, lg 24px.' },
    },
    notes: {
        shared: ['Never import another icon library; every icon comes from <sigta:icon> / <Icon>.'],
    },
    examples: [
        {
            label: 'Sizes',
            tree: [
                c('icon', { name: 'check', size: 'xs' }),
                c('icon', { name: 'check', size: 'sm' }),
                c('icon', { name: 'check' }),
                c('icon', { name: 'check', size: 'lg' }),
            ],
        },
        {
            label: 'A few from the set',
            tree: ['arrow-right', 'calendar', 'search', 'x', 'settings', 'pencil'].map((name) => c('icon', { name })),
        },
    ],
});
