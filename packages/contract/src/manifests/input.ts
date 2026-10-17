import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'input',
    title: 'Input',
    tier: 'elements',
    page: 'input',
    svelte: 'Input',
    description: 'A native text input in the field chrome: label, description, error and an optional leading icon. Every other attribute lands on the <input> itself.',
    aria: 'Native <input>; aria-invalid with an error, aria-describedby for description and error.',
    props: {
        label: { type: 'string', default: null, description: 'Field label.' },
        description: { type: 'string', default: null, description: 'Help text.' },
        error: { type: 'string', default: null, description: 'Error text; marks the input invalid.' },
        icon: { type: 'string', default: null, description: 'Leading icon name.' },
        type: { type: 'string', default: 'text', description: 'Native input type (text, email, password, number, date, …).' },
        value: { type: 'string', default: null, native: true, bindable: true, description: 'The value.' },
    },
    parts: {
        wrap: { description: 'Positions the leading icon (only with icon).' },
        icon: { description: 'The leading icon.' },
    },
    states: { invalid: { attribute: 'aria-invalid', description: 'Set with an error: red border until focused.' } },
    uses: ['field', 'icon'],
    usage: {
        livewire: '<sigta:input label="Email" type="email" icon="mail" wire:model="email" placeholder="you@studio.co" description="We only use this for the invoice." />',
        svelte: '<Input label="Email" type="email" icon="mail" bind:value={email} placeholder="you@studio.co" description="We only use this for the invoice." />',
    },
    notes: {
        shared: ['Use it instead of a raw <input>; it wraps itself in the field chrome.'],
        livewire: ['wire:model (with .live / .blur modifiers) is forwarded onto the native <input>. Under Livewire 4 write wire:model.live.blur for v3-style blur sync.'],
        svelte: ['bind:value for two-way binding; name, placeholder, required … pass straight through to the <input>.'],
    },
    examples: [
        { label: 'Basic', tree: [el('div', { class: 'max-w-sm' }, c('input', { label: 'Project name', value: 'Marginalia v3' }))] },
        { label: 'With description', tree: [el('div', { class: 'max-w-sm' }, c('input', { label: 'Email', type: 'email', placeholder: 'you@studio.co', description: 'We only use this for the invoice.' }))] },
        { label: 'Error', tree: [el('div', { class: 'max-w-sm' }, c('input', { label: 'Project name', value: '', error: 'A project needs a name.' }))] },
        {
            label: 'With leading icon',
            tree: [el('div', { class: 'flex max-w-sm flex-col gap-4' },
                c('input', { label: 'Search', icon: 'search', placeholder: 'Search engagements…' }),
                c('input', { label: 'Email', icon: 'mail', type: 'email', placeholder: 'you@studio.co' }),
            )],
        },
        {
            label: 'Types',
            tree: [el('div', { class: 'flex max-w-sm flex-col gap-4' },
                c('input', { label: 'Password', type: 'password', value: 'hunter2sigta' }),
                c('input', { label: 'Kickoff date', type: 'date' }),
                c('input', { label: 'Seats', type: 'number', value: '4' }),
            )],
        },
        { label: 'Disabled', tree: [el('div', { class: 'max-w-sm' }, c('input', { label: 'Engagement id', value: 'SGT-2026-014', disabled: true }))] },
        { label: 'Bare (no field chrome)', tree: [el('div', { class: 'max-w-sm' }, c('input', { placeholder: 'Quick note…', 'aria-label': 'Quick note' }))] },
    ],
});
