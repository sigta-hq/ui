import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const tile = (word: string) => el('div', { class: 'flex h-32 items-center justify-center rounded-panel border border-line bg-surface-raised' },
    el('span', { class: 'font-mono text-[0.72rem] tracking-[0.14em] uppercase text-graphite' }, word),
);

export default defineComponent({
    name: 'carousel.slide',
    title: 'Carousel slide',
    tier: 'components',
    page: 'carousel',
    svelte: 'Carousel.Slide',
    description: 'One slide of a carousel: snaps to the start of the track. Full width by default; pass a width utility as basis for multi-per-view layouts.',
    aria: 'A group with aria-roledescription="slide".',
    props: {
        basis: { type: 'string', default: 'w-full', description: 'The slide\'s width as a utility class: basis-4/5, w-1/2, basis-1/2 md:basis-1/3. Anything but the default replaces the full width.' },
    },
    states: {
        basis: { attribute: 'data-basis', values: ['custom'], description: 'A basis other than the default is given; the slide drops its own full width for it.' },
    },
    slots: { default: { description: 'The slide\'s content.', required: true } },
    usage: {
        livewire: '<sigta:carousel.slide basis="basis-1/2 md:basis-1/3">…</sigta:carousel.slide>',
        svelte: '<Carousel.Slide basis="basis-1/2 md:basis-1/3">…</Carousel.Slide>',
    },
    notes: {
        shared: ['basis is a class: it lands on the slide beside your own class.'],
    },
    examples: [
        {
            label: 'Four-fifths, so the next slide peeks in',
            tree: [c('carousel', { label: 'Phases' },
                c('carousel.slide', { basis: 'basis-4/5' }, tile('Discovery')),
                c('carousel.slide', { basis: 'basis-4/5' }, tile('Design')),
                c('carousel.slide', { basis: 'basis-4/5' }, tile('Build')),
            )],
        },
    ],
});
