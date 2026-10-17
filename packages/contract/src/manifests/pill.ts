import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'pill',
    title: 'Pill',
    tier: 'elements',
    page: 'badge',
    svelte: 'Pill',
    description: 'Status pill with a leading dot and a tinted background in the status colour: success, danger or neutral.',
    aria: 'The dot is decorative (aria-hidden); the label carries the status.',
    props: {
        variant: { type: 'enum', values: ['neutral', 'success', 'danger'], default: 'neutral', description: 'The status colour (neutral is graphite).' },
    },
    parts: {
        dot: { description: 'The leading dot, in the current colour.' },
    },
    slots: { default: { description: 'The status label.', required: true } },
    notes: {
        shared: ['Use it for a status (operational, at risk, paused); for a plain label use the badge.'],
    },
    examples: [
        {
            label: 'Status pills',
            tree: [el('div', { class: 'flex flex-wrap items-center gap-3' },
                c('pill', { variant: 'success' }, 'Operational'),
                c('pill', { variant: 'danger' }, 'Degraded'),
                c('pill', {}, 'Paused'),
            )],
        },
        {
            label: 'In context — table stage column',
            tree: [el('div', { class: 'flex flex-wrap items-center gap-3' },
                c('pill', { variant: 'success' }, 'Shipping'),
                c('pill', { variant: 'success' }, 'Active'),
                c('pill', { variant: 'danger' }, 'At risk'),
                c('badge', { variant: 'accent' }, 'New'),
            )],
        },
    ],
});
