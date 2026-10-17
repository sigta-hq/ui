import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'money-input',
    title: 'Money input',
    tier: 'components',
    page: 'money',
    svelte: 'MoneyInput',
    description: 'Amount field entered in major units with an inline currency switcher: a small dropdown button (code + chevron) sits inside the same field, not a separate toggle. The amount is emitted in MINOR units (×100, rounded; a comma counts as the decimal point); the currency binds separately. App logic (the currency enum, minor/major rules for exotic currencies) stays in your app.',
    aria: 'The amount is a text input labelled "<label> amount"; the switcher is a button (aria-haspopup="listbox", labelled "Currency") over a listbox of currency options with aria-selected.',
    props: {
        label: { type: 'string', default: null, description: 'Field label.' },
        value: { type: 'number', default: null, bindable: true, description: 'The amount in MINOR units (øre / cents).', blade: { note: 'The initial amount; edits are emitted onto the forwarded wire:model.' } },
        placeholder: { type: 'string', default: null, description: 'Amount placeholder; defaults to "0".' },
        currency: { type: 'string', default: 'DKK', bindable: true, description: 'The selected currency code.', blade: { note: 'Livewire binds it with currency-model, not wire:model (wire:model is the amount).' } },
        currencies: { type: 'object', default: null, shape: 'Record<string, string> — code ⇒ label', description: 'The switcher\'s currencies; defaults to DKK / EUR / USD. A label that differs from its code shows beside it.' },
        currencyName: { type: 'string', default: null, description: 'Form name of the hidden currency input; defaults to currencyModel, else "currency".' },
        currencyModel: { type: 'string', default: null, svelte: false, description: 'wire:model for the currency (through its hidden input).' },
    },
    parts: {
        amount: { description: 'The amount text input.' },
        switcher: { description: 'Positions the currency button and its menu.' },
        currency: { description: 'The currency button: the code and a chevron.' },
        menu: { description: 'The currency listbox.' },
        option: { description: 'One currency.' },
        label: { description: 'A currency\'s label, when it differs from its code.' },
    },
    states: {
        open: { attribute: 'aria-expanded', description: 'On the currency button.' },
        selected: { attribute: 'aria-selected', description: 'On the chosen currency\'s option.' },
    },
    events: {
        change: { description: 'The amount changed (minor units, null when empty).', blade: 'input on the hidden input wire:model sits on', svelte: 'onchange', detail: 'number | null' },
        currency: { description: 'The currency changed.', blade: 'input on the hidden currency input (currencyModel)', svelte: 'oncurrency', detail: 'string' },
    },
    uses: ['field', 'icon'],
    usage: {
        livewire: '<sigta:money-input label="Hourly rate" :value="$rate->amount_minor" wire:model="amountMinor" :currency="$currency" currency-model="currency" />',
        svelte: '<MoneyInput label="Hourly rate" bind:value={rate.amountMinor} bind:currency={rate.currency} />',
    },
    notes: {
        shared: [
            'Amounts stay app data in minor units: never pass or read a formatted string. Display amounts with {{tag:money}}.',
        ],
        livewire: [
            'The forwarded wire:model lands on a hidden input that carries the amount in minor units; every other attribute lands on the field.',
            'The currency binds through its own hidden input: currency-model="currency" puts wire:model on it.',
        ],
        svelte: ['bind:value (minor units) and bind:currency; other attributes land on the field.'],
    },
    examples: [
        {
            label: 'Money input — amount field + currency toggle',
            tree: [el('div', { class: 'max-w-md' }, c('money-input', { label: 'Hourly rate', value: 145000, currency: 'DKK' }))],
        },
        {
            label: 'Empty, with labelled currencies',
            tree: [el('div', { class: 'max-w-md' }, c('money-input', { label: 'Budget', placeholder: '0.00', currency: 'EUR', currencies: { DKK: 'kr.', EUR: '€', GBP: '£' } }))],
        },
        {
            label: 'Currency switched',
            tree: [el('div', { class: 'max-w-md' }, c('money-input', { label: 'Hourly rate', value: 145050, currency: 'DKK' }))],
            interactions: [{ click: '[data-part="currency"]' }, { click: '[data-part="option"]:nth-child(3)' }],
        },
        {
            label: 'Currency menu open',
            tree: [el('div', { class: 'max-w-md' }, c('money-input', { label: 'Budget', currency: 'EUR', currencies: { DKK: 'kr.', EUR: '€', GBP: '£' } }))],
            interactions: [{ click: '[data-part="currency"]' }],
        },
        {
            label: 'An amount typed',
            tree: [el('div', { class: 'max-w-md' }, c('money-input', { label: 'Hourly rate', value: 145000, currency: 'DKK' }))],
            interactions: [{ fill: ['[data-part="amount"]', '99,5'] }, { script: "document.querySelectorAll('input[type=hidden]').forEach((input) => input.setAttribute('data-emitted', input.value))" }],
        },
    ],
});
