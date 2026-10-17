import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'skeleton',
    title: 'Skeleton',
    tier: 'elements',
    page: 'skeleton',
    svelte: 'Skeleton',
    description: 'Loading placeholder, aria-hidden: a pulsing hairline-tinted shape — a line, a heading, a 16:9 block or a circle. Size it with classes (w-1/3, h-3).',
    aria: 'aria-hidden.',
    props: {
        variant: { type: 'enum', values: ['line', 'heading', 'block', 'circle'], default: 'line', description: 'line (1rem), heading (2rem, two thirds wide), block (16:9) or circle (40px).' },
    },
    notes: {
        shared: ['Content that replaces a skeleton can land with animate-sigta-settle (fade, rise, unblur).'],
    },
    examples: [
        {
            label: 'Variants',
            tree: [el('div', { class: 'flex flex-col gap-4 max-w-md' },
                c('skeleton', { variant: 'heading' }),
                c('skeleton', {}),
                c('skeleton', { class: 'w-4/5' }),
                c('skeleton', { variant: 'block' }),
                c('skeleton', { variant: 'circle' }),
            )],
        },
        {
            label: 'Composed — card loading state',
            tree: [el('div', { class: 'max-w-md' },
                c('card', { hover: false },
                    { thumb: [c('skeleton', { variant: 'block', class: 'rounded-none size-full' })] },
                    el('div', { class: 'flex items-center gap-3 mb-4' },
                        c('skeleton', { variant: 'circle' }),
                        el('div', { class: 'flex-1 flex flex-col gap-2' },
                            c('skeleton', { class: 'w-1/3' }),
                            c('skeleton', { class: 'w-1/2' }),
                        ),
                    ),
                    c('skeleton', { variant: 'heading' }),
                    el('div', { class: 'flex flex-col gap-2 mt-3' },
                        c('skeleton', {}),
                        c('skeleton', { class: 'w-5/6' }),
                    ),
                ),
            )],
        },
    ],
});
