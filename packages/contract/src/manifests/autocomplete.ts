import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const people = ['Ada Lovelace', 'Grace Hopper', 'Margaret Hamilton', 'Katherine Johnson'];

export default defineComponent({
    name: 'autocomplete',
    title: 'Autocomplete',
    tier: 'components',
    page: 'autocomplete',
    svelte: 'Autocomplete',
    description: 'A filtering combobox: type to narrow the slotted options, move with the arrow keys, Enter to choose. The chosen value lives in a hidden input (name, wire:model) or bind:value.',
    aria: 'WAI-ARIA combobox (list autocomplete) with a listbox; the active option is announced through aria-activedescendant.',
    props: {
        label: { type: 'string', default: null, description: 'Label above the query box.' },
        description: { type: 'string', default: null, description: 'Help text (hidden while there is an error).' },
        error: { type: 'string', default: null, description: 'Error text; marks the query box invalid.' },
        placeholder: { type: 'string', default: null, description: 'Query box placeholder.' },
        name: { type: 'string', default: null, description: 'Form name of the hidden value input.' },
        value: { type: 'string', default: null, native: true, bindable: true, description: 'The chosen value. Livewire: wire:model (lands on the hidden input); Svelte: bind:value.' },
    },
    parts: {
        label: { description: 'The label.' },
        input: { description: 'The query box (role=combobox).' },
        listbox: { description: 'The floating panel of options.' },
        empty: { description: '"No matches." when the query filters everything out.' },
        description: { description: 'Help text.' },
        error: { description: 'Error text.' },
    },
    states: {
        open: { attribute: 'aria-expanded', description: 'On the query box.' },
        invalid: { attribute: 'aria-invalid', description: 'On the query box, with an error.' },
    },
    slots: { default: { description: 'The <autocomplete.option>s.', required: true } },
    events: {
        change: { description: 'The value changed.', blade: 'input / change on the hidden input (wire:model hears it)', svelte: 'onchange', detail: 'string | null' },
    },
    uses: ['autocomplete.option'],
    usage: {
        livewire: `<sigta:autocomplete label="Country" wire:model="country" placeholder="Search…">
    <sigta:autocomplete.option value="dk">Denmark</sigta:autocomplete.option>
    <sigta:autocomplete.option value="se">Sweden</sigta:autocomplete.option>
</sigta:autocomplete>`,
        svelte: `<Autocomplete label="Country" bind:value={country} placeholder="Search…">
    <Autocomplete.Option value="dk">Denmark</Autocomplete.Option>
    <Autocomplete.Option value="se">Sweden</Autocomplete.Option>
</Autocomplete>`,
    },
    notes: {
        shared: ['The listbox floats with fixed positioning, so it escapes cards and forms that clip overflow.'],
        livewire: ['wire:model and every attribute except class and id land on the hidden value input.'],
        svelte: ['bind:value; other attributes (name, data-*) land on the hidden value input.'],
    },
    examples: [
        {
            label: 'Basic',
            tree: [el('div', { class: 'max-w-sm' },
                c('autocomplete', { label: 'Assign to', placeholder: 'Search people…', name: 'owner' },
                    ...people.map((name) => c('autocomplete.option', { value: name.split(' ')[0].toLowerCase() }, name)),
                ),
            )],
        },
        {
            label: 'Filtered with the keyboard',
            tree: [el('div', { class: 'max-w-sm' },
                c('autocomplete', { label: 'Assign to', placeholder: 'Search people…', description: 'Type to narrow the list.' },
                    ...people.map((name) => c('autocomplete.option', { value: name.split(' ')[0].toLowerCase() }, name)),
                ),
            )],
            interactions: [{ click: '[data-part="input"]' }, { fill: ['[data-part="input"]', 'ha'] }, { press: ['ArrowDown', 'ArrowDown'] }],
        },
        {
            label: 'No matches, with an error',
            tree: [el('div', { class: 'max-w-sm' },
                c('autocomplete', { label: 'Workspace', error: 'Pick a workspace.' },
                    c('autocomplete.option', {}, 'Marginalia'),
                    c('autocomplete.option', {}, 'Plumb'),
                ),
            )],
            interactions: [{ click: '[data-part="input"]' }, { fill: ['[data-part="input"]', 'zzz'] }],
        },
    ],
});
