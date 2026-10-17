import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'chart.bars',
    title: 'Bar chart',
    tier: 'components',
    page: 'chart',
    svelte: 'Chart.Bars',
    description: 'Hand-drawn bar chart: ink bars, an optional highlighter swipe behind one bar and an optional dashed trend line across the bar tops. Optional mono labels, one under each bar.',
    aria: 'aria-hidden by default; with label the SVG is role="img" with that accessible name. The bar labels are decorative.',
    props: {
        values: { type: 'array', default: [], shape: 'number[]', description: 'One value per bar. Bars rise from zero (or from the lowest value when it is negative).' },
        labels: { type: 'array', default: null, shape: 'string[]', description: 'One label per bar, under it.' },
        highlight: { type: 'number', default: null, description: 'Index of the bar to highlight with the accent swipe (it is drawn at full ink; the others a touch lighter).' },
        trend: { type: 'boolean', default: false, description: 'Draw a dashed hand-drawn trend line across the bar tops.' },
        height: { type: 'number', default: 160, description: 'Height of the 720-wide viewBox (at least 40); the chart scales to its container\'s width.' },
        label: { type: 'string', default: null, description: 'Accessible name; makes the chart meaningful (role="img").' },
    },
    parts: {
        plot: { description: 'The <svg>.' },
        labels: { description: 'The row of bar labels (a grid with one column per bar, from --sigta-chart-columns).' },
    },
    usage: {
        livewire: '<sigta:chart.bars :values="$monthly" :labels="[\'Jan\', \'Feb\', \'Mar\', \'Apr\', \'May\', \'Jun\']" :highlight="5" trend label="Monthly builds, rising" />',
        svelte: '<Chart.Bars values={monthly} labels={[\'Jan\', \'Feb\', \'Mar\', \'Apr\', \'May\', \'Jun\']} highlight={5} trend label="Monthly builds, rising" />',
    },
    notes: {
        shared: [
            'currentColor-driven: bars take the text colour (ink by default). The highlighter swipe is the accent, so highlight one bar, in one chart per view.',
            'The geometry is computed the same way in both runtimes (@sigta-hq/core chart.ts), so the paths are identical.',
        ],
    },
    examples: [
        {
            label: 'Bars — highlight swipe + dashed trend',
            tree: [el('div', { class: 'bg-surface border border-line rounded-panel p-4' },
                c('chart.bars', {
                    values: [54, 80, 72, 110, 96, 132],
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    highlight: 5,
                    trend: true,
                    label: 'Bar chart of monthly builds rising from 54 to a highlighted 132',
                }),
            )],
        },
        {
            label: 'Bars — plain',
            tree: [el('div', { class: 'max-w-md bg-surface border border-line rounded-panel p-4' },
                c('chart.bars', { values: [18, 26, 22, 31], height: 120 }),
            )],
        },
        {
            label: 'Bars — below zero',
            tree: [el('div', { class: 'max-w-md bg-surface border border-line rounded-panel p-4' },
                c('chart.bars', { values: [-4, 6, 2, 9], labels: ['Q1', 'Q2', 'Q3', 'Q4'], highlight: 3, trend: true, height: 120 }),
            )],
        },
    ],
});
