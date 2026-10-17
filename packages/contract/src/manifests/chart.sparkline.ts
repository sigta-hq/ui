import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const card = (...children: ReturnType<typeof c>[]) => el('div', { class: 'bg-surface border border-line rounded-panel p-4' }, ...children);

export default defineComponent({
    name: 'chart.sparkline',
    title: 'Sparkline',
    tier: 'components',
    page: 'chart',
    svelte: 'Chart.Sparkline',
    description: 'Tiny smooth hand-drawn sparkline in a 200×40 viewBox with a dot on the latest value. Inherits currentColor; decorative unless given a label.',
    aria: 'aria-hidden by default; with label it is role="img" with that accessible name.',
    props: {
        values: { type: 'array', default: [], shape: 'number[]', description: 'The values, oldest first. One value draws just the dot; equal values draw a flat line.' },
        label: { type: 'string', default: null, description: 'Accessible name; makes the sparkline meaningful (role="img") instead of decorative.' },
    },
    notes: {
        shared: [
            'Charts are hand-drawn inline SVG in currentColor: recolour one with a text colour on it or a parent (class="text-graphite"), never a hex.',
            'It stretches to the width of its container; put it in a stat\'s slot for a trend under the figure.',
            'The geometry is computed the same way in both runtimes (@sigta-hq/core chart.ts), so the paths are identical.',
        ],
    },
    examples: [
        {
            label: 'Sparkline — inherits currentColor',
            tree: [el('div', { class: 'grid gap-3 sm:grid-cols-3' },
                c('stat', { label: 'Monthly recurring', value: '$48.2k', delta: '9%', trend: 'up' },
                    c('chart.sparkline', { values: [12, 14, 13, 17, 16, 20, 19, 24, 23, 28] }),
                ),
                c('stat', { label: 'Churned seats', value: '41', delta: '12 fewer', trend: 'up' },
                    c('chart.sparkline', { values: [80, 74, 78, 66, 60, 62, 52, 48, 44, 41], class: 'text-graphite' }),
                ),
                c('stat', { label: 'Support load', value: '3.4h', delta: 'steady' },
                    c('chart.sparkline', { values: [3.2, 3.6, 3.1, 3.5, 3.3, 3.6, 3.2, 3.4], label: 'Sparkline of support load hovering around 3.4 hours' }),
                ),
            )],
        },
        {
            label: 'Degenerate data — single point & all-equal',
            tree: [el('div', { class: 'grid gap-3 sm:grid-cols-3' },
                card(c('chart.sparkline', { values: [42] })),
                card(c('chart.sparkline', { values: [5, 5, 5, 5, 5] })),
                card(c('chart.sparkline', { values: [] })),
            )],
        },
    ],
});
