import { defineComponent, type Example } from '../schema.ts';
import { c, el } from '../examples.ts';

const disciplines = { design: 'Product design', saas: 'SaaS engineering', tools: 'Internal tools', brand: 'Brand & systems' };

const box = (props: Record<string, unknown> = {}) => [el('div', { class: 'max-w-md' },
    c('pillbox', { label: 'Disciplines', name: 'disciplines', placeholder: 'Add a discipline…', ...props },
        ...Object.entries(disciplines).map(([value, text]) => c('autocomplete.option', { value }, text)),
    ),
)];

// Chips are drawn on the client (Alpine's x-for in Blade), so the
// examples that choose some compare in the browser.
const chosen = (label: string, interactions: Example['interactions']): Example => ({ label, tree: box(), conformance: 'browser', interactions });

export default defineComponent({
    name: 'pillbox',
    title: 'Pillbox',
    tier: 'components',
    page: 'pillbox',
    svelte: 'Pillbox',
    description: 'Multi-select combobox with removable chips: type to narrow the slotted <autocomplete.option>s, Enter or a click adds (or removes) the active one as a chip beside the query box; each chip\'s × removes it. The hidden input carries a JSON array of the selected values.',
    aria: 'WAI-ARIA combobox (list autocomplete) with a multi-selectable listbox; each chip\'s remove button is labelled "Remove …".',
    props: {
        label: { type: 'string', default: null, description: 'Label above the field.' },
        description: { type: 'string', default: null, description: 'Help text (hidden while there is an error).' },
        error: { type: 'string', default: null, description: 'Error text; marks the query box invalid and the field red.' },
        placeholder: { type: 'string', default: 'Add…', description: 'Query box placeholder.' },
        name: { type: 'string', default: null, description: 'Form name of the hidden value input (a JSON array).' },
        value: { type: 'array', default: [], native: true, bindable: true, shape: 'string[]', description: 'The selected values. Livewire: wire:model (lands on the hidden input, a JSON array); Svelte: bind:value.' },
    },
    parts: {
        label: { description: 'The label.' },
        control: { description: 'The bordered field holding the chips and the query box.' },
        chip: { description: 'One selected value.' },
        remove: { description: 'A chip\'s remove button.' },
        input: { description: 'The query box (role=combobox).' },
        listbox: { description: 'The floating panel of options.' },
        empty: { description: '"No matches." when the query filters everything out.' },
        description: { description: 'Help text.' },
        error: { description: 'Error text.' },
    },
    states: {
        open: { attribute: 'aria-expanded', description: 'On the query box.' },
        invalid: { attribute: 'aria-invalid', description: 'On the query box, with an error; the control turns red.' },
    },
    slots: { default: { description: 'The <autocomplete.option>s.', required: true } },
    events: {
        change: { description: 'The selection changed.', blade: 'input / change on the hidden input (wire:model hears it)', svelte: 'onchange', detail: 'string[]' },
    },
    uses: ['autocomplete.option', 'icon'],
    usage: {
        livewire: `<sigta:pillbox label="Disciplines" wire:model="disciplines" placeholder="Add a discipline…">
    <sigta:autocomplete.option value="design">Product design</sigta:autocomplete.option>
    <sigta:autocomplete.option value="saas">SaaS engineering</sigta:autocomplete.option>
</sigta:pillbox>`,
        svelte: `<Pillbox label="Disciplines" bind:value={disciplines} placeholder="Add a discipline…">
    <Autocomplete.Option value="design">Product design</Autocomplete.Option>
    <Autocomplete.Option value="saas">SaaS engineering</Autocomplete.Option>
</Pillbox>`,
    },
    notes: {
        shared: [
            'Choosing an option that is already a chip removes it again; the list stays open for the next one.',
            'The listbox floats with fixed positioning, so it escapes cards and forms that clip overflow.',
        ],
        livewire: [
            'wire:model and every attribute except class and id land on the hidden input, whose value is a JSON array: decode it (or type the property as an array) on the server.',
            'The chips start empty on every render: a Livewire value is not read back into them.',
        ],
        svelte: ['bind:value holds the array; other attributes (name, data-*) land on the hidden input.'],
    },
    examples: [
        { label: 'Multi-select with chips', tree: box() },
        { label: 'With description', tree: box({ description: 'Pick every discipline the engagement needs.' }) },
        { label: 'Error state', tree: box({ error: 'Pick at least one discipline.' }) },
        {
            label: 'Opened, typed to filter',
            tree: box(),
            interactions: [{ click: '[data-part="input"]' }, { fill: ['[data-part="input"]', 'ng'] }, { press: ['ArrowDown', 'ArrowDown'] }],
        },
        {
            label: 'No matches',
            tree: box(),
            interactions: [{ click: '[data-part="input"]' }, { fill: ['[data-part="input"]', 'zzz'] }],
        },
        chosen('Chips chosen with Enter and a click', [
            { click: '[data-part="input"]' },
            { press: 'Enter' },
            { press: ['ArrowDown', 'ArrowDown', 'Enter'] },
            { click: '[data-sigta="autocomplete.option"][data-value="brand"]' },
        ]),
        chosen('A chip removed', [
            { click: '[data-part="input"]' },
            { press: ['Enter', 'ArrowDown', 'Enter'] },
            { press: 'Escape' },
            { click: '[data-part="remove"]' },
        ]),
    ],
});
