import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const narrow = (...children: Parameters<typeof el>[2][]) => el('div', { class: 'max-w-xs' }, ...children);

export default defineComponent({
    name: 'time-picker',
    title: 'Time picker',
    tier: 'components',
    page: 'time-picker',
    svelte: 'TimePicker',
    description: 'Text input + clock-icon button opening a listbox of times every step minutes between min and max, in the field chrome (label, description, error). The model stays 24h HH:MM regardless of the display format; typed times are accepted too.',
    aria: 'The input is a combobox (aria-haspopup="listbox", aria-expanded) controlling a listbox of option buttons with aria-selected; arrow keys move the active option, Enter picks it, Escape closes the list.',
    props: {
        label: { type: 'string', default: null, description: 'Field label (also names the listbox; "Times" without one).' },
        description: { type: 'string', default: null, description: 'Help text.' },
        error: { type: 'string', default: null, description: 'Error text; marks the input invalid.' },
        name: { type: 'string', default: null, description: 'Form name of the hidden value input.' },
        value: {
            type: 'string',
            default: null,
            bindable: true,
            description: 'The time, HH:MM (24h). Livewire: wire:model (lands on the hidden input); Svelte: bind:value.',
            blade: { accepts: 'string|\\DateTimeInterface (Carbon)', note: 'A Carbon/DateTimeInterface is read as its H:i time; "9:30" reads as 09:30.' },
        },
        min: { type: 'string', default: null, description: 'First time listed, HH:MM (default 00:00); a typed earlier time is refused.' },
        max: { type: 'string', default: null, description: 'Last time listed at most, HH:MM (default 23:59); a typed later time is refused.' },
        step: { type: 'number', default: 30, description: 'Minutes between listed times (at least 1).' },
        format: { type: 'enum', values: ['24', '12'], default: '24', description: 'Labels as 13:30 or 1:30 PM. The model stays 24h HH:MM regardless of display format.' },
        placeholder: { type: 'string', default: 'Select a time', description: 'Input placeholder.' },
    },
    parts: {
        trigger: { description: 'The input with the clock button tucked into its right padding.' },
        input: { description: 'The text input (role=combobox).' },
        toggle: { description: 'The clock-icon button ("Open time list"); out of the tab order.' },
        listbox: { description: 'The scrolling list of times that drops below.' },
        option: { description: 'One time (role=option), data-value="HH:MM".' },
    },
    states: {
        open: { attribute: 'aria-expanded', description: 'On the input, while the list is open.' },
        invalid: { attribute: 'aria-invalid', description: 'On the input, with an error: red border until focused.' },
        selected: { attribute: 'aria-selected', values: ['true', 'false'], description: 'On each option: the chosen time is inked.' },
        active: { attribute: 'data-active', description: 'On the option the arrow keys or the pointer are on: a faint ink wash.' },
    },
    events: {
        change: {
            description: 'The time changed, HH:MM (null when cleared).',
            blade: 'input / change on the hidden input (wire:model hears it)',
            svelte: 'onchange',
            detail: 'string | null',
        },
    },
    uses: ['field', 'icon'],
    usage: {
        livewire: '<sigta:time-picker label="Start time" wire:model="start" :value="$start" step="15" min="09:00" max="17:00" />',
        svelte: '<TimePicker label="Start time" bind:value={start} step={15} min="09:00" max="17:00" />',
    },
    notes: {
        shared: [
            'Typed times: 14:45, 9:05 or 2:45 pm; Enter on the open list picks the active time, leaving the field takes a typed one. A time outside min/max or no time at all reverts the field; an emptied field clears the value. A typed time need not be on the step.',
            'Click the field, the button, ArrowDown or Enter to open the list (on the chosen time, else the first); arrows wrap, Escape or focus leaving closes it.',
            'disabled disables the input, the button and the hidden input.',
        ],
        livewire: [
            'wire:model and every attribute except class and id land on the hidden value input, which hears input and change on every pick or typed time. Pass :value as well: wire:model carries changes back, the value prop seeds what the field shows.',
        ],
        svelte: ['bind:value (HH:MM) and onchange(value); name and other attributes land on the hidden input for plain form posts.'],
    },
    examples: [
        { label: 'Basic (30-minute steps)', tree: [narrow(c('time-picker', { label: 'Start time', name: 'start', placeholder: 'Pick a time' }))] },
        { label: 'Pre-filled value', tree: [narrow(c('time-picker', { label: 'Standup', name: 'standup', value: '09:30' }))] },
        { label: '12-hour labels (model stays 24h)', tree: [narrow(c('time-picker', { label: 'Lunch', name: 'lunch', value: '13:30', format: '12' }))] },
        { label: '15-minute steps, bounded 09:00–17:00', tree: [narrow(c('time-picker', { label: 'Meeting', name: 'meeting', step: 15, min: '09:00', max: '17:00', value: '10:15' }))] },
        { label: 'With description', tree: [narrow(c('time-picker', { label: 'Reminder', name: 'reminder', description: 'Type a time (e.g. 14:45) or choose from the list.' }))] },
        { label: 'Error', tree: [narrow(c('time-picker', { label: 'Cutoff', name: 'cutoff', error: 'Choose a cutoff time.' }))] },
        { label: 'Disabled', tree: [narrow(c('time-picker', { label: 'Locked', name: 'locked', value: '12:00', disabled: true }))] },
        {
            label: 'Opened and stepped',
            tree: [narrow(c('time-picker', { label: 'Meeting', name: 'stepped', step: 15, min: '09:00', max: '17:00', value: '10:15' }))],
            interactions: [{ click: '[data-part="input"]' }, { press: ['ArrowDown', 'ArrowDown'] }],
        },
        {
            label: 'Picked with Enter',
            tree: [narrow(c('time-picker', { label: 'Standup', name: 'picked', value: '09:30' }))],
            interactions: [{ click: '[data-part="input"]' }, { press: ['ArrowUp', 'ArrowUp', 'ArrowUp', 'Enter'] }],
        },
        {
            label: 'A typed 12-hour time',
            tree: [narrow(c('time-picker', { label: 'Lunch', name: 'typed', value: '13:30', format: '12' }))],
            interactions: [{ fill: ['[data-part="input"]', '2:45 pm'] }, { press: 'Tab' }],
        },
    ],
});
