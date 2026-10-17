import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'progress',
    title: 'Progress',
    tier: 'elements',
    page: 'progress',
    svelte: 'Progress',
    description: 'Determinate or indeterminate progress bar: a hairline track filled in ink. A label adds a mono header row with the percentage. Attributes land on the progressbar element itself.',
    aria: 'role="progressbar" with aria-valuemin/max and aria-valuenow (omitted when indeterminate); aria-labelledby points at the label.',
    props: {
        value: { type: 'number', default: 0, description: 'Progress against max.' },
        max: { type: 'number', default: 100, description: 'The value that fills the bar.' },
        label: { type: 'string', default: null, description: 'Header text; adds the header row with the rounded percentage.' },
        indeterminate: { type: 'boolean', default: false, description: 'Sweep a short bar across instead of filling; no percentage.' },
    },
    parts: {
        field: { description: 'Stacks the header over the track (only with a label).' },
        header: { description: 'The mono header row.' },
        label: { description: 'The label text.' },
        value: { description: 'The rounded percentage.' },
        fill: { description: 'The ink fill; its width is --sigta-progress (0–100), set inline.' },
    },
    states: {
        indeterminate: { attribute: 'data-indeterminate', description: 'On the track: the fill sweeps.' },
    },
    usage: {
        livewire: '<sigta:progress :value="$upload->percent" label="Uploading" />',
        svelte: '<Progress value={upload.percent} label="Uploading" />',
    },
    notes: {
        shared: [
            'For long-running background work prefer {{tag:job}} (or {{tag:loader}} with a value), which carry the brand motion; use progress for a plain bar (uploads, steps).',
            'The percentage is value / max, clamped to 0–100.',
        ],
    },
    examples: [
        {
            label: 'With label',
            tree: [el('div', { class: 'max-w-md flex flex-col gap-6' },
                c('progress', { value: 24, label: 'Uploading' }),
                c('progress', { value: 68, label: 'Migration' }),
                c('progress', { value: 100, label: 'Complete' }),
            )],
        },
        { label: 'Bare', tree: [el('div', { class: 'max-w-md' }, c('progress', { value: 45, 'aria-label': 'Import' }))] },
        { label: 'Indeterminate', tree: [el('div', { class: 'max-w-md' }, c('progress', { indeterminate: true, label: 'Working' }))] },
        {
            label: 'Against a max',
            tree: [el('div', { class: 'max-w-md flex flex-col gap-6' },
                c('progress', { value: 30, max: 120, label: 'Seats used' }),
                c('progress', { value: 1, max: 3, label: 'Step' }),
            )],
        },
    ],
});
