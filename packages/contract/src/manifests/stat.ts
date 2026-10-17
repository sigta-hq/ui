import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'stat',
    title: 'Stat',
    tier: 'components',
    page: 'stat',
    svelte: 'Stat',
    description: 'A numeric figure: a mono label, the value in Fraunces with tabular numerals, and an optional delta with a trend arrow. panel boxes it on paper; inline is the borderless hero-meta figure, value above label.',
    aria: 'Plain text; the trend arrow is decorative (aria-hidden), so say the direction in the delta ("6.2% up").',
    props: {
        label: { type: 'string', default: null, description: 'Mono uppercase label.' },
        value: { type: 'string', default: null, description: 'The figure, formatted by you ("2,418", "$48.2k", "14 år").' },
        delta: { type: 'string', default: null, description: 'The change line under the value ("6.2% vs last 30d").' },
        trend: { type: 'enum', values: ['up', 'down'], default: null, description: 'up = success ink with ▲; down = danger ink with ▼; unset = a neutral graphite delta.' },
        variant: { type: 'enum', values: ['inline', 'panel'], default: 'panel', description: 'panel = boxed figure on paper (label above value); inline = bare hero-meta figure (value above label).' },
        tone: { type: 'enum', values: ['default', 'inverse'], default: 'default', description: 'inverse re-inks it for a dark surface.' },
    },
    parts: {
        label: { description: 'The label.' },
        value: { description: 'The value.' },
        delta: { description: 'The delta line, with the trend arrow.' },
        content: { description: 'The slot (a sparkline), under the value.' },
    },
    states: {
        variant: { attribute: 'data-variant', values: ['inline', 'panel'], description: 'On the root.' },
        tone: { attribute: 'data-tone', values: ['default', 'inverse'], description: 'On the root.' },
        trend: { attribute: 'data-trend', values: ['up', 'down'], description: 'On the delta.' },
    },
    slots: { default: { description: 'Extra content under the figure, typically a chart.sparkline (it inherits the value colour).' } },
    notes: {
        shared: [
            'tone="inverse" darkens it for a dark surface: the panel variant gets a dark ground; the inline variant stays bare, to sit on a dark {{tag:panel}} (variant="inverse"). Trend colours stay semantic in both tones.',
            'Put a {{tag:chart.sparkline}} in the slot for a trend line under the figure.',
        ],
    },
    examples: [
        {
            label: 'Trends — up, down & neutral',
            tree: [el('div', { class: 'grid gap-3 sm:grid-cols-3' },
                c('stat', { label: 'Active accounts', value: '2,418', delta: '6.2% vs last 30d', trend: 'up' }),
                c('stat', { label: 'Time to value', value: '3.1d', delta: '0.4d slower', trend: 'down' }),
                c('stat', { label: 'Seats in use', value: '14', delta: 'unchanged' }),
            )],
        },
        {
            label: 'With sparkline slot',
            tree: [el('div', { class: 'max-w-xs' },
                c('stat', { label: 'Monthly recurring', value: '$48.2k', delta: '9%', trend: 'up' },
                    c('chart.sparkline', { values: [12, 14, 13, 17, 16, 20, 19, 24, 23, 28] }),
                ),
            )],
        },
        {
            label: 'Label + value only',
            tree: [el('div', { class: 'max-w-xs' }, c('stat', { label: 'Net retention', value: '112%' }))],
        },
        {
            label: 'Inline variant — borderless hero-meta figure',
            tree: [el('div', { class: 'flex flex-wrap gap-[2.2rem]' },
                c('stat', { variant: 'inline', value: '14 år', label: 'bag tastaturet' }),
                c('stat', { variant: 'inline', value: '1', label: 'fast lav timepris' }),
                c('stat', { variant: 'inline', value: '2', label: 'måder at samarbejde på' }),
            )],
        },
        {
            label: 'Inverse tone — standalone boxed',
            tree: [el('div', { class: 'grid gap-3 sm:grid-cols-2 max-w-md' },
                c('stat', { tone: 'inverse', label: 'Logged this week', value: '12h 30m' }),
                c('stat', { tone: 'inverse', label: 'Billable value', value: '8.750 kr.' }),
            )],
        },
        {
            label: 'Inverse tone — inline, composed on a dark panel',
            tree: [c('panel', { variant: 'inverse', class: 'max-w-sm gap-4' },
                c('stat', { variant: 'inline', tone: 'inverse', value: '42h 10m', label: 'Logged this customer' }),
                c('stat', { variant: 'inline', tone: 'inverse', value: '29.750 kr.', label: 'Billable value', delta: '3 invoices open' }),
            )],
        },
    ],
});
