import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'textarea',
    title: 'Textarea',
    tier: 'elements',
    page: 'textarea',
    svelte: 'Textarea',
    description: 'A native <textarea> in the field chrome: label, description and error, resizable in height. Every other attribute lands on the <textarea> itself.',
    aria: 'Native <textarea>; aria-invalid with an error, aria-describedby for description and error.',
    props: {
        label: { type: 'string', default: null, description: 'Field label.' },
        description: { type: 'string', default: null, description: 'Help text.' },
        error: { type: 'string', default: null, description: 'Error text; marks the textarea invalid.' },
        rows: { type: 'number', default: 4, description: 'Visible text lines.' },
        value: { type: 'string', default: null, native: true, bindable: true, description: 'The text.', blade: { note: 'wire:model, or the slot / a value attribute for the initial text.' } },
    },
    states: { invalid: { attribute: 'aria-invalid', description: 'Set with an error: red border until focused.' } },
    slots: { default: { description: 'The initial text (a value attribute works too).', svelte: false } },
    uses: ['field'],
    usage: {
        livewire: '<sigta:textarea label="Notes" wire:model="notes" rows="3" description="Visible to the whole team." />',
        svelte: '<Textarea label="Notes" bind:value={notes} rows={3} description="Visible to the whole team." />',
    },
    notes: {
        shared: ['Use it instead of a raw <textarea> with classes; it wraps itself in the field chrome.'],
        livewire: ['wire:model (and any wire:* / x-* attribute) is forwarded onto the native <textarea>. The slot is the initial text; with an empty slot a value attribute fills it.'],
        svelte: ['bind:value for two-way binding; name, placeholder, required … pass straight through to the <textarea>.'],
    },
    examples: [
        { label: 'Basic', tree: [el('div', { class: 'max-w-sm' }, c('textarea', { label: 'Project brief', placeholder: 'What are we drawing?' }))] },
        { label: 'With description', tree: [el('div', { class: 'max-w-sm' }, c('textarea', { label: 'Notes', description: 'Visible to the whole team.', rows: 3, value: 'Kickoff moved to Tuesday.' }))] },
        { label: 'Error', tree: [el('div', { class: 'max-w-sm' }, c('textarea', { label: 'Project brief', error: 'Give us at least a sentence.' }))] },
        {
            label: 'Taller (rows=8) & disabled',
            tree: [el('div', { class: 'flex max-w-sm flex-col gap-4' },
                c('textarea', { label: 'Scope', rows: 8, placeholder: 'Everything we agreed to build…' }),
                c('textarea', { label: 'Archived brief', disabled: true, value: 'Locked after sign-off.' }),
            )],
        },
        { label: 'Bare (no field chrome)', tree: [el('div', { class: 'max-w-sm' }, c('textarea', { rows: 3, 'aria-label': 'Note', placeholder: 'Quick note…' }))] },
        {
            label: 'Typed into',
            tree: [el('div', { class: 'max-w-sm' }, c('textarea', { label: 'Notes', rows: 3 }))],
            interactions: [{ fill: ['textarea', 'Ship the invoice on Friday.'] }],
        },
    ],
});
