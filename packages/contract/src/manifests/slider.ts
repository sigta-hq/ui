import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'slider',
    title: 'Slider',
    tier: 'elements',
    page: 'slider',
    svelte: 'Slider',
    description: 'Brand-styled native range input in the field chrome: a hairline track and a raised paper thumb with an ink rim; optional live value readout. min, max, step, value and every other attribute land on the <input>.',
    aria: 'Native <input type="range">; aria-invalid with an error, aria-describedby for description and error; the readout is an <output for>.',
    props: {
        label: { type: 'string', default: null, description: 'Field label.' },
        description: { type: 'string', default: null, description: 'Help text.' },
        error: { type: 'string', default: null, description: 'Error text; rims the thumb in red.' },
        showValue: { type: 'boolean', default: false, description: 'Print the live value beside the track in mono.' },
        value: { type: 'number', default: null, native: true, bindable: true, description: 'The value.', blade: { note: 'wire:model, or value for the initial position.' } },
    },
    parts: {
        wrap: { description: 'Lines the input up with its readout (only with showValue).' },
        value: { description: 'The live readout (<output>).' },
    },
    states: { invalid: { attribute: 'aria-invalid', description: 'Set with an error: the thumb is rimmed in red.' } },
    uses: ['field'],
    usage: {
        livewire: '<sigta:slider label="Ink density" min="0" max="100" wire:model.live="density" show-value />',
        svelte: '<Slider label="Ink density" min={0} max={100} bind:value={density} showValue />',
    },
    notes: {
        shared: ['Use it instead of <input type="range"> with classes.'],
        livewire: ['wire:model is forwarded onto the native <input>; the readout follows it on input (Alpine).'],
        svelte: ['bind:value gives a number; min, max, step pass straight through to the <input>.'],
    },
    examples: [
        {
            label: 'With live value',
            conformance: 'browser',
            tree: [el('div', { class: 'max-w-md' }, c('slider', { label: 'Ink density', min: '0', max: '100', value: '60', showValue: true }))],
        },
        { label: 'Plain + description', tree: [el('div', { class: 'max-w-md' }, c('slider', { label: 'Compression', description: 'Higher is smaller, slower.', min: '0', max: '9', value: '4' }))] },
        {
            label: 'States',
            tree: [el('div', { class: 'max-w-md flex flex-col gap-6' },
                c('slider', { label: 'Disabled', value: '30', disabled: true }),
                c('slider', { label: 'Error', value: '90', error: 'Out of safe range.' }),
            )],
        },
        {
            label: 'Moved with the keyboard',
            conformance: 'browser',
            tree: [el('div', { class: 'max-w-md' }, c('slider', { label: 'Volume', min: '0', max: '10', value: '4', showValue: true }))],
            interactions: [{ focus: 'input[type="range"]' }, { press: ['ArrowRight', 'ArrowRight', 'ArrowRight'] }],
        },
    ],
});
