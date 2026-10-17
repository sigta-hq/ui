import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

// The calendar a picker without a value opens on is today's month; the
// examples pin today so they render the same on every day.
const today = '2026-07-21';
const narrow = (...children: Parameters<typeof el>[2][]) => el('div', { class: 'max-w-sm' }, ...children);

export default defineComponent({
    name: 'date-picker',
    title: 'Date picker',
    tier: 'components',
    page: 'date-picker',
    svelte: 'DatePicker',
    description: 'Text input + calendar-icon button opening a calendar popover, in the field chrome (label, description, error). Picking a day fills the field ("9 Jul 2026") and the YYYY-MM-DD value; the input also accepts typed dates within min/max, which the calendar then shows.',
    aria: 'The input is a combobox (aria-haspopup="dialog", aria-expanded) controlling a dialog labelled "Choose date" that holds the calendar\'s date grid; Escape closes it and returns focus to the input.',
    props: {
        label: { type: 'string', default: null, description: 'Field label.' },
        description: { type: 'string', default: null, description: 'Help text.' },
        error: { type: 'string', default: null, description: 'Error text; marks the input invalid.' },
        name: { type: 'string', default: null, description: 'Form name of the hidden value input.' },
        value: {
            type: 'date',
            default: null,
            bindable: true,
            description: 'The date, YYYY-MM-DD.',
            blade: { accepts: 'string|\\DateTimeInterface (Carbon)', note: 'wire:model lands on the hidden input. A Carbon/DateTimeInterface is read as its Y-m-d day.' },
        },
        min: { type: 'date', default: null, description: 'First selectable day, YYYY-MM-DD (the calendar disables earlier days; a typed earlier date is refused).' },
        max: { type: 'date', default: null, description: 'Last selectable day, YYYY-MM-DD (the calendar disables later days; a typed later date is refused).' },
        placeholder: { type: 'string', default: 'Select a date', description: 'Input placeholder.' },
        today: { type: 'date', default: null, description: 'Passed to the calendar: the day marked as today, and opened on without a value. Unset means the current date.' },
    },
    parts: {
        anchor: { description: 'Positions the panel under the input.' },
        trigger: { description: 'The input with the calendar button tucked into its right padding.' },
        input: { description: 'The text input (role=combobox).' },
        toggle: { description: 'The calendar-icon button ("Open calendar"); out of the tab order.' },
        panel: { description: 'The dialog that drops below, holding the calendar.' },
    },
    states: {
        open: { attribute: 'aria-expanded', description: 'On the input, while the calendar is open.' },
        invalid: { attribute: 'aria-invalid', description: 'On the input, with an error: red border until focused.' },
    },
    events: {
        change: {
            description: 'The date changed, YYYY-MM-DD (null when cleared).',
            blade: 'input / change on the hidden input (wire:model hears it)',
            svelte: 'onchange',
            detail: 'string | null',
        },
    },
    uses: ['field', 'calendar', 'icon'],
    usage: {
        livewire: '<sigta:date-picker label="Due date" wire:model="due" :value="$due" min="2026-07-06" />',
        svelte: '<DatePicker label="Due date" bind:value={due} min="2026-07-06" />',
    },
    notes: {
        shared: [
            'Typed dates: YYYY-MM-DD, or anything the browser reads ("9 Jul 2026", "July 9, 2026"); Enter or leaving the field takes it. A date outside min/max or no date at all reverts the field; an emptied field clears the value.',
            'Open the calendar with the button or ArrowDown in the field; Escape, a click outside or focus leaving closes it. Picking a day closes it and returns focus to the field.',
            'disabled disables the input, the button and the hidden input.',
        ],
        livewire: [
            'wire:model and every attribute except class and id land on the hidden value input, which hears input and change on every pick or typed date. Pass :value as well: wire:model carries changes back, the value prop seeds what the field shows (and a change from the server, like a reset, shows when it comes back through :value).',
        ],
        svelte: ['bind:value (YYYY-MM-DD) and onchange(value); name and other attributes land on the hidden input for plain form posts.'],
    },
    examples: [
        { label: 'Basic', tree: [narrow(c('date-picker', { label: 'Due date', name: 'due', placeholder: 'Pick a date', today }))] },
        { label: 'Pre-filled value', tree: [narrow(c('date-picker', { label: 'Kickoff', name: 'kickoff', value: '2026-07-09' }))] },
        { label: 'With description', tree: [narrow(c('date-picker', { label: 'Invoice date', name: 'invoice', value: '2026-07-09', description: 'Type a date or pick one from the calendar.' }))] },
        { label: 'Bounded (min/max)', tree: [narrow(c('date-picker', { label: 'Delivery window', name: 'delivery', value: '2026-07-15', min: '2026-07-06', max: '2026-07-24' }))] },
        { label: 'Error', tree: [narrow(c('date-picker', { label: 'Start date', name: 'start', error: 'Choose a start date.', today }))] },
        { label: 'Disabled', tree: [narrow(c('date-picker', { label: 'Locked', name: 'locked', value: '2026-07-09', disabled: true }))] },
        {
            label: 'Opened',
            tree: [narrow(c('date-picker', { label: 'Delivery window', name: 'open', value: '2026-07-15', min: '2026-07-06', max: '2026-07-24' }))],
            interactions: [{ click: '[data-part="toggle"]' }],
        },
        {
            label: 'Picked with the keyboard',
            tree: [narrow(c('date-picker', { label: 'Kickoff', name: 'keyed', value: '2026-07-09' }))],
            interactions: [{ click: '[data-part="toggle"]' }, { focus: '[data-part="day"][tabindex="0"]' }, { press: ['ArrowRight', 'ArrowDown', 'PageDown'] }, { press: 'Enter' }],
        },
        {
            label: 'A typed date, shown in the calendar',
            tree: [narrow(c('date-picker', { label: 'Kickoff', name: 'typed', value: '2026-07-09', today }))],
            interactions: [{ fill: ['[data-part="input"]', '2026-12-24'] }, { press: 'Enter' }, { click: '[data-part="toggle"]' }],
        },
    ],
});
