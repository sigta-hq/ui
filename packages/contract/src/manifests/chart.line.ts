import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'chart.line',
    title: 'Line chart',
    tier: 'components',
    page: 'chart',
    svelte: 'Chart.Line',
    description: 'Hand-drawn line/area chart: a smooth ink line over a faded currentColor area and three hairline gridlines; the latest point is highlighted with an accent halo and a dashed drop line to the axis. Optional mono tick labels underneath.',
    aria: 'aria-hidden by default; with label the SVG is role="img" with that accessible name. The tick labels are decorative.',
    props: {
        values: { type: 'array', default: [], shape: 'number[]', description: 'The values, oldest first.' },
        labels: { type: 'array', default: null, shape: 'string[]', description: 'Tick labels spread evenly under the chart (first … last); any number of them.' },
        height: { type: 'number', default: 200, description: 'Height of the 720-wide viewBox (at least 40); the chart scales to its container\'s width.' },
        label: { type: 'string', default: null, description: 'Accessible name; makes the chart meaningful (role="img").' },
    },
    parts: {
        plot: { description: 'The <svg>.' },
        labels: { description: 'The row of tick labels.' },
    },
    usage: {
        livewire: '<sigta:chart.line :values="$weekly" :labels="[\'W1\', \'W13\', \'W26\']" label="Weekly active workspaces, rising" />',
        svelte: '<Chart.Line values={weekly} labels={[\'W1\', \'W13\', \'W26\']} label="Weekly active workspaces, rising" />',
    },
    notes: {
        shared: [
            'currentColor-driven: the line and area take the text colour (ink by default); the halo is the accent, so keep one highlighted chart per view.',
            'The geometry is computed the same way in both runtimes (@sigta-hq/core chart.ts), so the paths are identical.',
        ],
    },
    examples: [
        {
            label: 'Line — area fill, gridlines, highlighted endpoint',
            tree: [el('div', { class: 'bg-surface border border-line rounded-panel p-4' },
                el('div', { class: 'flex justify-between items-center mb-2 font-mono text-[0.7rem] tracking-[0.06em] text-graphite' },
                    el('span', {}, 'Weekly active workspaces'),
                    el('span', {}, '12 weeks'),
                ),
                c('chart.line', {
                    values: [112, 118, 116, 124, 130, 128, 141, 139, 152, 149, 163, 170],
                    labels: ['W1', 'W4', 'W8', 'W12'],
                    label: 'Area chart of weekly active workspaces rising from 112 to 170 over 12 weeks',
                }),
            )],
        },
        {
            label: 'Line — short height, no labels',
            tree: [el('div', { class: 'max-w-md bg-surface border border-line rounded-panel p-4' },
                c('chart.line', { values: [9, 7, 8, 6, 7, 5, 6, 4], height: 120 }),
            )],
        },
        {
            label: 'Degenerate data — a single point',
            tree: [el('div', { class: 'max-w-xs bg-surface border border-line rounded-panel p-4' },
                c('chart.line', { values: [7], height: 120 }),
            )],
        },
    ],
});
