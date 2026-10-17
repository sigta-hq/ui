import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'otp',
    title: 'OTP',
    tier: 'components',
    page: 'otp',
    svelte: 'Otp',
    description: 'One-time-code input: a row of single-character boxes backed by one hidden input that carries the code. Supports paste (spread across the boxes), arrow keys and backspace.',
    aria: 'Each box is a labelled text input ("Digit 1 of 6"); the field label points at the first box.',
    props: {
        length: { type: 'number', default: 6, description: 'Number of boxes (at least 1).' },
        label: { type: 'string', default: null, description: 'Field label.' },
        description: { type: 'string', default: null, description: 'Help text.' },
        error: { type: 'string', default: null, description: 'Error text; marks every box invalid.' },
        autofocus: { type: 'boolean', default: false, description: 'Focus the first box on load.' },
        value: { type: 'string', default: null, native: true, bindable: true, description: 'The code. Livewire: wire:model (lands on the hidden input); Svelte: bind:value.' },
    },
    parts: {
        box: { description: 'One single-character box.' },
    },
    states: { invalid: { attribute: 'aria-invalid', description: 'On every box, with an error: red borders until focused.' } },
    events: {
        input: { description: 'The code changed.', blade: 'input on the hidden input (wire:model hears it)', svelte: 'bind:value, or oninput (dispatched on the hidden input)', detail: 'string' },
    },
    uses: ['field'],
    usage: {
        livewire: '<sigta:otp :length="6" name="code" label="Verification code" wire:model.live="code" />',
        svelte: '<Otp length={6} name="code" label="Verification code" bind:value={code} />',
    },
    notes: {
        shared: ['name, disabled and every attribute except class land on the hidden input; disabled also disables the boxes.'],
        livewire: ['The hidden input re-dispatches input whenever the code changes, so wire:model on the component follows it.'],
    },
    examples: [
        { label: 'Six digits', tree: [c('otp', { length: 6, name: 'code', label: 'Verification code', description: 'Paste the code from your email — it splits across the boxes.' })] },
        { label: 'Four digits', tree: [c('otp', { length: 4, name: 'pin', label: 'PIN' })] },
        { label: 'Error state', tree: [c('otp', { length: 6, name: 'badcode', label: 'Verification code', error: 'That code has expired.' })] },
        { label: 'Disabled', tree: [c('otp', { length: 4, name: 'locked', label: 'PIN', disabled: true })] },
        {
            label: 'Typed and stepped back',
            conformance: 'browser',
            tree: [c('otp', { length: 4, name: 'typed', label: 'PIN' })],
            interactions: [{ click: '[data-part="box"]' }, { press: ['4', '2', '7', 'Backspace', 'Backspace', 'ArrowLeft'] }],
        },
        {
            label: 'Prefilled',
            conformance: 'browser',
            tree: [c('otp', { length: 6, name: 'prefilled', label: 'Verification code', value: '314159' })],
        },
    ],
});
