import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'badge',
    title: 'Badge',
    tier: 'elements',
    page: 'badge',
    svelte: 'Badge',
    description: 'Small mono pill with a 1.5px border: an ink outline, a solid ink fill, or the accent highlight.',
    aria: 'A <span>.',
    props: {
        variant: { type: 'enum', values: ['outline', 'solid', 'accent'], default: 'outline', description: 'outline (ink border), solid (ink fill) or accent (highlighter, sparingly).' },
    },
    slots: { default: { description: 'The label.', required: true } },
    usage: {
        livewire: '<sigta:badge>Draft</sigta:badge>\n<sigta:badge variant="solid">Live</sigta:badge>',
        svelte: '<Badge>Draft</Badge>\n<Badge variant="solid">Live</Badge>',
    },
    notes: {
        shared: [
            'Use it instead of a <span class="rounded-full …">. For a status with a coloured dot (live, failed, paused) use the pill.',
        ],
        livewire: ['A number that changes (unread, open) belongs in <sigta:count>, not a bare number in a badge.'],
        svelte: ['A number that changes (unread, open) belongs in <Count>, not a bare number in a badge.'],
    },
    examples: [
        {
            label: 'Badges — outline, solid & accent',
            tree: [el('div', { class: 'flex flex-wrap items-center gap-3' },
                c('badge', {}, 'Outline'),
                c('badge', { variant: 'solid' }, 'Solid'),
                c('badge', { variant: 'accent' }, 'Highlight'),
            )],
        },
        {
            label: 'Draft and live',
            tree: [el('div', { class: 'flex flex-wrap items-center gap-3' },
                c('badge', {}, 'Draft'),
                c('badge', { variant: 'solid' }, 'Live'),
            )],
        },
    ],
});
