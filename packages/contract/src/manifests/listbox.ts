import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const countries = { dk: 'Denmark', se: 'Sweden', no: 'Norway', fi: 'Finland' };

export default defineComponent({
    name: 'listbox',
    title: 'Listbox',
    tier: 'components',
    page: 'listbox',
    svelte: 'Listbox',
    description: 'Rich single select: a button trigger showing the current choice opens a floating listbox of autocomplete.options (the option row shared with autocomplete and pillbox). Arrow keys move, Enter chooses, Escape closes. The chosen value lives in a hidden input, so it also posts with a plain form.',
    aria: 'A button with role="combobox" and aria-haspopup="listbox" controls a listbox; the active option is announced through aria-activedescendant.',
    props: {
        label: { type: 'string', default: null, description: 'Field label.' },
        description: { type: 'string', default: null, description: 'Help text below the trigger.' },
        error: { type: 'string', default: null, description: 'Error text; marks the trigger invalid.' },
        placeholder: { type: 'string', default: 'Select…', description: 'Shown on the trigger while nothing is chosen.' },
        name: { type: 'string', default: null, description: 'Form name of the hidden value input.' },
        value: { type: 'string', default: null, bindable: true, description: 'The chosen value (an option\'s value).', blade: { note: 'Pass the current value (:value) for the trigger to show it, and wire:model (it lands on the hidden input) to receive choices.' } },
    },
    parts: {
        trigger: { description: 'The button (role=combobox) in the field chrome; your id lands here.' },
        value: { description: 'The chosen option\'s text, or the placeholder.' },
        chevron: { description: 'The chevron; turns over while the list is open.' },
        listbox: { description: 'The floating panel of options.' },
    },
    states: {
        open: { attribute: 'aria-expanded', description: 'On the trigger.' },
        invalid: { attribute: 'aria-invalid', description: 'On the trigger, with an error.' },
        empty: { attribute: 'data-empty', description: 'On the value part while it shows the placeholder.' },
    },
    slots: { default: { description: 'The autocomplete.options.', required: true } },
    events: {
        change: { description: 'The value changed.', blade: 'input / change on the hidden input (wire:model hears it)', svelte: 'onchange', detail: 'string | null' },
    },
    uses: ['field', 'icon', 'autocomplete.option'],
    usage: {
        livewire: `<sigta:listbox label="Country" wire:model="country" placeholder="Pick one…">
    <sigta:autocomplete.option value="dk">Denmark</sigta:autocomplete.option>
    <sigta:autocomplete.option value="se">Sweden</sigta:autocomplete.option>
</sigta:listbox>`,
        svelte: `<Listbox label="Country" bind:value={country} placeholder="Pick one…">
    <Autocomplete.Option value="dk">Denmark</Autocomplete.Option>
    <Autocomplete.Option value="se">Sweden</Autocomplete.Option>
</Listbox>`,
    },
    notes: {
        shared: [
            'Use it for a rich single choice; a plain list of strings is better as a {{tag:select}}, a long searchable one as an {{tag:autocomplete}}.',
            'The listbox floats with fixed positioning, so it escapes cards and forms that clip overflow.',
            'The trigger shows the placeholder until the runtime starts, then the chosen option\'s text.',
        ],
        livewire: ['wire:model and every attribute except class and id land on the hidden value input; class lands on the root, id on the trigger.'],
        svelte: ['bind:value; class lands on the root, id on the trigger, other attributes (name, data-*) on the hidden value input.'],
    },
    examples: [
        {
            label: 'Rich single select',
            tree: [el('div', { class: 'max-w-xs' },
                c('listbox', { label: 'Country', name: 'country', placeholder: 'Pick a country…' },
                    ...Object.entries(countries).map(([value, text]) => c('autocomplete.option', { value }, text)),
                ),
            )],
        },
        {
            // The option's aria-selected is bound on the client in Blade.
            label: 'Preselected value + description',
            conformance: 'browser',
            tree: [el('div', { class: 'max-w-xs' },
                c('listbox', { label: 'Plan', name: 'plan', value: 'studio', description: 'Billed yearly. Change anytime.' },
                    c('autocomplete.option', { value: 'solo' }, 'Solo'),
                    c('autocomplete.option', { value: 'studio' }, 'Studio'),
                    c('autocomplete.option', { value: 'org' }, 'Organisation'),
                ),
            )],
        },
        {
            label: 'Error state',
            tree: [el('div', { class: 'max-w-xs' },
                c('listbox', { label: 'Assignee', name: 'assignee', error: 'Pick someone before saving.' },
                    c('autocomplete.option', { value: 'sf' }, 'Søren Frisk'),
                    c('autocomplete.option', { value: 'mk' }, 'Mette Kaas'),
                ),
            )],
        },
        {
            label: 'Opened, moved with the arrow keys',
            tree: [el('div', { class: 'max-w-xs' },
                c('listbox', { label: 'Country', name: 'country', placeholder: 'Pick a country…' },
                    ...Object.entries(countries).map(([value, text]) => c('autocomplete.option', { value }, text)),
                ),
            )],
            interactions: [{ click: '[data-part="trigger"]' }, { press: ['ArrowDown', 'ArrowDown'] }],
        },
        {
            label: 'Chosen with Enter',
            tree: [el('div', { class: 'max-w-xs' },
                c('listbox', { label: 'Country', name: 'country', placeholder: 'Pick a country…' },
                    ...Object.entries(countries).map(([value, text]) => c('autocomplete.option', { value }, text)),
                ),
            )],
            interactions: [{ focus: '[data-part="trigger"]' }, { press: 'Enter' }, { press: ['ArrowDown', 'ArrowDown', 'Enter'] }],
        },
        {
            label: 'Closed with Escape',
            conformance: 'browser',
            tree: [el('div', { class: 'max-w-xs' },
                c('listbox', { label: 'Plan', name: 'plan', value: 'studio' },
                    c('autocomplete.option', { value: 'solo' }, 'Solo'),
                    c('autocomplete.option', { value: 'studio' }, 'Studio'),
                    c('autocomplete.option', { value: 'org' }, 'Organisation'),
                ),
            )],
            interactions: [{ click: '[data-part="trigger"]' }, { press: ['ArrowUp', 'Escape'] }],
        },
    ],
});
