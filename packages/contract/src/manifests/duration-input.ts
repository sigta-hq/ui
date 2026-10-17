import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

// The emitted minutes live in the hidden input's value, a property the
// browser comparison skips: copy it into an attribute so it is compared.
const emitted = { script: "document.querySelectorAll('input[type=hidden]').forEach((input) => input.setAttribute('data-emitted', input.value))" };

export default defineComponent({
    name: 'duration-input',
    title: 'Duration input',
    tier: 'components',
    page: 'duration-input',
    svelte: 'DurationInput',
    description: 'A single duration field that reads as a compact "1h59m" label and, on click, becomes an editable "HH:MM" input (e.g. "01:59"). On blur or Enter it parses the clock back to whole minutes (forgiving: "H:MM", "HH:MM", or a bare minute count) and emits them; Escape cancels.',
    aria: 'The compact label is a <button>; the editor is a text input labelled "<label> as hours and minutes, HH:MM".',
    props: {
        label: { type: 'string', default: null, description: 'Field label.' },
        value: { type: 'number', default: null, bindable: true, description: 'Whole minutes (null or 0 reads as no value). Livewire: the initial minutes, emitted onto the forwarded wire:model; Svelte: bind:value.' },
        placeholder: { type: 'string', default: null, description: 'Shown while there is no value; defaults to "0m".' },
    },
    parts: {
        display: { description: 'The compact label button.' },
        editor: { description: 'The HH:MM text input.' },
    },
    states: {
        empty: { attribute: 'data-empty', description: 'On the display while it shows the placeholder.' },
    },
    events: {
        change: { description: 'A committed edit (whole minutes, null when cleared).', blade: 'input on the hidden input wire:model sits on', svelte: 'onchange', detail: 'number | null' },
    },
    uses: ['field'],
    usage: {
        livewire: '<sigta:duration-input label="Estimate" :value="$task->estimate_minutes" wire:model="estimate" wire:key="est-{{ $task->estimate_minutes }}" />',
        svelte: '<DurationInput label="Estimate" bind:value={task.estimateMinutes} />',
    },
    notes: {
        shared: ['For a read-only duration use duration; for a value that counts up live, elapsed-clock.'],
        livewire: [
            'The forwarded wire:model lands on a hidden input that carries whole minutes (empty when cleared); every other attribute lands on the field.',
            'Give the field a value-keyed wire:key (wire:key="est-{{ $minutes }}") so switching context or resetting after a save gives Alpine fresh state instead of a stale in-place value.',
        ],
        svelte: ['bind:value holds whole minutes (null when cleared); other attributes land on the field.'],
    },
    examples: [
        {
            label: 'Compact field — click to edit as HH:MM',
            tree: [el('div', { class: 'flex flex-wrap items-start gap-8' },
                c('duration-input', { label: 'Estimate', value: 119, 'wire:key': 'est-119' }),
                c('duration-input', { label: 'Time logged', value: 90, 'wire:key': 'logged-90' }),
                c('duration-input', { label: 'No value yet', placeholder: 'Add time', 'wire:key': 'dur-empty' }),
            )],
        },
        {
            label: 'Forgiving parse, emits whole minutes',
            tree: [el('div', { class: 'max-w-xs' }, c('duration-input', { label: 'Estimate', value: 150, 'wire:key': 'parse-150' }))],
        },
        {
            label: 'Editing',
            tree: [el('div', { class: 'max-w-xs' }, c('duration-input', { label: 'Estimate', value: 119 }))],
            interactions: [{ click: '[data-part="display"]' }],
        },
        {
            label: 'Typed H:MM, committed with Enter',
            tree: [el('div', { class: 'max-w-xs' }, c('duration-input', { label: 'Estimate', placeholder: 'Add time' }))],
            interactions: [{ click: '[data-part="display"]' }, { fill: ['[data-part="editor"]', '1:30'] }, { press: 'Enter' }, emitted],
        },
        {
            label: 'Bare minutes, committed on blur',
            tree: [el('div', { class: 'max-w-xs' }, c('duration-input', { label: 'Estimate', value: 60 }), el('button', { type: 'button', class: 'mt-4 font-mono text-[0.7rem]' }, 'Elsewhere'))],
            interactions: [{ click: '[data-part="display"]' }, { fill: ['[data-part="editor"]', '125'] }, { click: 'button:not([data-part])' }, emitted],
        },
        {
            label: 'Escape cancels',
            tree: [el('div', { class: 'max-w-xs' }, c('duration-input', { label: 'Estimate', value: 45 }))],
            interactions: [{ click: '[data-part="display"]' }, { fill: ['[data-part="editor"]', '3:00'] }, { press: 'Escape' }, emitted],
        },
        {
            label: 'Cleared',
            tree: [el('div', { class: 'max-w-xs' }, c('duration-input', { label: 'Estimate', value: 45 }))],
            interactions: [{ click: '[data-part="display"]' }, { fill: ['[data-part="editor"]', ''] }, { press: 'Enter' }, emitted],
        },
    ],
});
