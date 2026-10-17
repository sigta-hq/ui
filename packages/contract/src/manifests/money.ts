import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'money',
    title: 'Money',
    tier: 'components',
    page: 'money',
    svelte: 'Money',
    description: 'Formats a minor-unit amount (øre/cents) in tabular numerals. Known codes (DKK/EUR/USD/GBP/SEK/NOK) carry symbol, position and separators; override any piece, or pass an unknown code (trailing symbol). suffix renders a muted per-unit tail (e.g. "/ hour").',
    aria: 'A plain <span>; the amount is its text.',
    props: {
        minor: { type: 'number', default: null, description: 'The amount in MINOR units (øre / cents); null reads as 0.' },
        currency: { type: 'string', default: 'DKK', description: 'ISO code; drives symbol, position and separators. Printed upper case in data-currency.' },
        decimals: { type: 'number', default: 2, description: 'Fraction digits to show (0 for whole-major display; rounds half away from zero).' },
        symbol: { type: 'string', default: null, description: 'Override the mapped symbol.' },
        position: { type: 'enum', values: ['before', 'after'], default: null, description: 'Override the mapped symbol position.' },
        thousands: { type: 'string', default: null, description: 'Override the mapped thousands separator.' },
        decimal: { type: 'string', default: null, description: 'Override the mapped decimal separator.' },
        suffix: { type: 'string', default: null, description: 'Muted per-unit tail, e.g. "/ hour".' },
    },
    parts: {
        suffix: { description: 'The muted per-unit tail.' },
    },
    states: {
        currency: { attribute: 'data-currency', description: 'The upper-cased ISO code.' },
    },
    usage: {
        livewire: '<sigta:money :minor="$rate->amount_minor" :currency="$rate->currency->value" suffix="/ hour" />',
        svelte: '<Money minor={rate.amountMinor} currency={rate.currency} suffix="/ hour" />',
    },
    notes: {
        shared: [
            'The amount and currency stay app data: pass minor units and an ISO code, never a preformatted string.',
            'Both runtimes format with the same rules (the core\'s formatMoney, PHP number_format semantics), so a server render and a client render agree.',
        ],
    },
    examples: [
        {
            label: 'Minor units, formatted per currency',
            tree: [el('div', { class: 'flex flex-col gap-2 font-head text-2xl' },
                ...['DKK', 'EUR', 'USD', 'GBP'].map((currency) => el('div', {}, c('money', { minor: 123456, currency }))),
            )],
        },
        {
            label: 'Whole-major & per-unit suffix',
            tree: [el('div', { class: 'flex flex-wrap items-baseline gap-10 font-head text-2xl' },
                c('money', { minor: 145000, currency: 'DKK', decimals: 0, suffix: '/ hour' }),
                c('money', { minor: 99900, currency: 'USD', suffix: '/ seat' }),
            )],
        },
        {
            label: 'Overrides and unknown codes',
            tree: [el('div', { class: 'flex flex-col gap-2 font-mono text-[0.82rem]' },
                el('div', {}, c('money', { minor: 100000, currency: 'USD', symbol: 'US$', thousands: ' ' })),
                el('div', {}, c('money', { minor: 123456, currency: 'sek' })),
                el('div', {}, c('money', { minor: 5000, currency: 'JPY' })),
                el('div', {}, c('money', { minor: -4250, currency: 'EUR', position: 'after', symbol: 'EUR' })),
                el('div', {}, c('money', { currency: 'USD' })),
            )],
        },
    ],
});
