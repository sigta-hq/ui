import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'tile-grid.tile',
    title: 'Tile',
    tier: 'layout',
    page: 'tile-grid',
    svelte: 'TileGrid.Tile',
    description: 'One cell of a tile-grid: raised paper with generous padding and an optional mono number on top; the content stacks as a column.',
    aria: 'An <article>.',
    props: {
        num: { type: 'string', default: null, description: 'Mono number (or kicker) on top: "01", "02 · Projektet skrider".' },
    },
    parts: {
        num: { description: 'The number on top.' },
    },
    slots: { default: { description: 'The tile content.', required: true } },
    uses: ['tile-grid'],
    notes: {
        shared: ['Its root keeps data-sigta="tile" (the name it has always carried), so the parts are data-scope="tile".'],
    },
    examples: [
        {
            label: 'With and without a number',
            tree: [c('tile-grid', { cols: 2 },
                c('tile-grid.tile', { num: '01' }, el('p', {}, 'Teknisk ledelse')),
                c('tile-grid.tile', {}, el('p', {}, 'Bare indhold')),
            )],
        },
    ],
});
