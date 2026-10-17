import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'switch',
    title: 'Switch',
    tier: 'elements',
    page: 'switch',
    svelte: 'Switch',
    description: 'A role="switch" toggle: a real checkbox behind a pill track whose paper knob slides across; the knob squashes toward its travel while pressed. Every attribute but class lands on the native <input>.',
    aria: 'Native <input type="checkbox" role="switch">, visually hidden inside its <label>; the track is aria-hidden.',
    props: {
        label: { type: 'string', default: null, description: 'Label text beside the track; without it pass aria-label.' },
        checked: { type: 'boolean', default: false, native: true, bindable: true, description: 'On. Livewire: wire:model or checked; Svelte: bind:checked.' },
    },
    parts: {
        input: { description: 'The visually hidden native checkbox.' },
        track: { description: 'The drawn pill and knob (::after).' },
    },
    states: { checked: { attribute: 'checked', description: 'On: the track fills with ink and the knob slides right.' } },
    usage: {
        livewire: '<sigta:switch label="Weekly digest" wire:model.live="digest" />',
        svelte: '<Switch label="Weekly digest" bind:checked={digest} />',
    },
    notes: {
        shared: ['Use it instead of custom toggle markup. Without a visible label, pass aria-label.'],
        livewire: ['wire:model, disabled and every attribute except class land on the native <input>; class lands on the label.'],
        svelte: ['bind:checked for the state; other attributes (name, disabled, onchange) land on the native <input>.'],
    },
    examples: [
        {
            label: 'Basic',
            tree: [el('div', { class: 'flex flex-col gap-4' },
                c('switch', { label: 'Grayscale mode' }),
                c('switch', { label: 'Weekly digest', checked: true }),
            )],
        },
        {
            label: 'Disabled',
            tree: [el('div', { class: 'flex flex-col gap-4' },
                c('switch', { label: 'Locked on', checked: true, disabled: true }),
                c('switch', { label: 'Locked off', disabled: true }),
            )],
        },
        { label: 'Without visible label', tree: [c('switch', { 'aria-label': 'Toggle paper grain', checked: true })] },
        {
            label: 'In a field',
            tree: [el('div', { class: 'max-w-sm' },
                c('field', { label: 'Notifications', description: 'Delivered every Friday, never more.', for: 'kit-digest' },
                    c('switch', { label: 'Weekly digest', checked: true, id: 'kit-digest' }),
                ),
            )],
        },
        {
            label: 'Toggled by hand',
            tree: [c('switch', { label: 'Grayscale mode' })],
            interactions: [{ click: '[data-sigta="switch"]', wait: 500 }],
        },
    ],
});
