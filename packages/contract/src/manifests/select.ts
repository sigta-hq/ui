import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const options = (...labels: string[]) => labels.map((label) => el('option', {}, label));

export default defineComponent({
    name: 'select',
    title: 'Select',
    tier: 'components',
    page: 'select',
    svelte: 'Select',
    description: 'Native select with a chevron overlay, wrapped in the field chrome: label, description and error. The <option>s are the slot; every other attribute lands on the <select> itself.',
    aria: 'Native <select>; aria-invalid with an error, aria-describedby for description and error. The chevron is decorative.',
    props: {
        label: { type: 'string', default: null, description: 'Field label.' },
        description: { type: 'string', default: null, description: 'Help text.' },
        error: { type: 'string', default: null, description: 'Error text; marks the select invalid.' },
        value: { type: 'string', default: null, native: true, bindable: true, description: 'The chosen value.' },
    },
    parts: {
        wrap: { description: 'Positions the chevron over the select.' },
        icon: { description: 'The chevron; dims with a disabled select.' },
    },
    states: { invalid: { attribute: 'aria-invalid', description: 'Set with an error: red border until focused.' } },
    slots: { default: { description: 'The native <option>s (and <optgroup>s).', required: true } },
    uses: ['field', 'icon'],
    usage: {
        livewire: `<sigta:select label="Engagement type" wire:model="type">
    <option value="build">Full build</option>
    <option value="sprint">Design sprint</option>
</sigta:select>`,
        svelte: `<Select label="Engagement type" bind:value={type}>
    <option value="build">Full build</option>
    <option value="sprint">Design sprint</option>
</Select>`,
    },
    notes: {
        shared: ['Use it instead of a raw <select> with classes. For a searchable or rich single-select use {{tag:listbox}} or {{tag:autocomplete}}.'],
        livewire: ['wire:model is forwarded onto the native <select>.'],
        svelte: ['bind:value for two-way binding; name, required … pass straight through to the <select>.'],
    },
    examples: [
        { label: 'Basic', tree: [el('div', { class: 'max-w-sm' }, c('select', { label: 'Engagement type' }, ...options('Full build', 'Design sprint', 'Embedded team', 'Brand & system')))] },
        { label: 'With description', tree: [el('div', { class: 'max-w-sm' }, c('select', { label: 'Billing cadence', description: 'Changeable until the first invoice.' }, ...options('Monthly', 'Quarterly', 'Per milestone')))] },
        {
            label: 'Error',
            tree: [el('div', { class: 'max-w-sm' }, c('select', { label: 'Workspace', error: 'Pick a workspace before continuing.' },
                el('option', { value: '' }, '—'), ...options('Marginalia', 'Plumb', 'Halyard OS'),
            ))],
        },
        { label: 'Disabled', tree: [el('div', { class: 'max-w-sm' }, c('select', { label: 'Region', disabled: true }, ...options('Europe (locked)')))] },
        { label: 'Bare (no field chrome)', tree: [el('div', { class: 'max-w-sm' }, c('select', { 'aria-label': 'Range' }, ...options('7d', '30d', 'QTD')))] },
    ],
});
