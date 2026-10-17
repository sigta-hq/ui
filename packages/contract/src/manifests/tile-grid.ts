import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const quote = 'font-head italic font-normal text-[clamp(1.15rem,1.7vw,1.4rem)] leading-[1.3]';
const answer = "flex items-baseline gap-[0.55em] text-[0.95rem] text-graphite before:content-['→'] before:shrink-0 before:font-mono before:text-ink";
const service = (num: string, heading: string, text: string) =>
    c('tile-grid.tile', { num, class: 'min-h-[200px]' },
        c('heading', { level: 3, class: 'mt-[18px] mb-2' }, heading),
        el('p', { class: 'text-[0.95rem] leading-[1.5] text-graphite' }, text),
    );

export default defineComponent({
    name: 'tile-grid',
    title: 'Tile grid',
    tier: 'layout',
    page: 'tile-grid',
    svelte: 'TileGrid',
    description: 'Hairline-gap grid of tiles: a 1px gap over a bg-line ground reads as thin dividers between them, inside a hairline frame.',
    aria: 'A <div> grid; each tile is an <article>.',
    props: {
        cols: { type: 'number', default: 3, description: 'Columns on a wide screen: 2, 3 or 4. They collapse on smaller screens — 4 → 2 → 1, 3 → 1, 2 → 1.' },
    },
    states: {
        cols: { attribute: 'data-cols', values: ['2', '3', '4'], description: 'On the root; any other count reads as 3.' },
    },
    slots: { default: { description: 'The tile-grid.tile cells.', required: true } },
    uses: ['tile-grid.tile'],
    examples: [
        {
            label: 'Two columns — recognition cards',
            tree: [c('tile-grid', { cols: 2 },
                c('tile-grid.tile', { num: '01 · Tilbuddet i indbakken', class: 'gap-[0.9rem]' },
                    el('p', { class: quote }, '"Vi har fået et tilbud på 750.000 kr. Er det rimeligt?"'),
                    el('p', { class: answer }, 'Vi læser det med dig, linje for linje.'),
                ),
                c('tile-grid.tile', { num: '02 · Projektet skrider', class: 'gap-[0.9rem]' },
                    el('p', { class: quote }, '"Udviklingen er bagud, og ingen kan forklare hvorfor."'),
                    el('p', { class: answer }, 'Vi finder ud af, hvor det knirker.'),
                ),
            )],
        },
        {
            label: 'Four columns — services (4 → 2 → 1)',
            tree: [c('tile-grid', { cols: 4 },
                service('01', 'Teknisk ledelse', 'Strategi, roadmap og arkitektur på deltid.'),
                service('02', 'Din mand i rummet', 'Med til leverandørmøder og gennemgang af tilbud.'),
                service('03', 'Teknisk vurdering', 'Sundhedstjek af kodebase, projekt eller leverandør.'),
                service('04', 'Hands-on udvikling', "MVP'er, prototyper og kritiske systemer."),
            )],
        },
        {
            label: 'Three columns (the default)',
            tree: [c('tile-grid', {},
                c('tile-grid.tile', { num: '01' }, el('p', {}, 'Plan')),
                c('tile-grid.tile', { num: '02' }, el('p', {}, 'Build')),
                c('tile-grid.tile', { num: '03' }, el('p', {}, 'Run')),
            )],
        },
    ],
});
