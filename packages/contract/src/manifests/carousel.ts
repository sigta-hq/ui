import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const work = (title: string, text: string) => c('carousel.slide', {},
    el('div', { class: 'flex min-h-48 flex-col justify-between rounded-panel border border-line bg-surface-raised p-6' },
        el('span', { class: 'font-mono text-[0.62rem] tracking-[0.14em] uppercase text-graphite' }, 'Engagement'),
        el('div', {},
            el('h3', { class: 'font-head text-2xl text-ink' }, title),
            el('p', { class: 'mt-2 max-w-[48ch] text-graphite' }, text),
        ),
    ),
);
const oneOnly = () => c('carousel', { label: 'Selected work' },
    work('Marginalia', 'A reading-and-notes app for teams — annotate any doc, search every margin.'),
    work('Halyard OS', 'A dispatch and routing console for a freight client. 4,000 trucks, one calm screen.'),
    work('Plumb', 'Metrics without the dashboard fatigue. One honest number per question.'),
);
const snapshot = (word: string) => c('carousel.slide', { basis: 'basis-1/2 md:basis-1/3' },
    el('div', { class: 'mr-0 flex h-32 items-center justify-center rounded-panel border border-line bg-surface-raised' },
        el('span', { class: 'font-mono text-[0.72rem] tracking-[0.14em] uppercase text-graphite' }, word),
    ),
);

export default defineComponent({
    name: 'carousel',
    title: 'Carousel',
    tier: 'components',
    page: 'carousel',
    svelte: 'Carousel',
    description: 'A scroll-snap track with prev/next chevrons and clickable dots. The active slide is derived from the scroll position, so a swipe, the arrows and the dots stay in sync; the arrows disable at the ends.',
    aria: 'A region with aria-roledescription="carousel" (named by label); each slide is a group with aria-roledescription="slide"; the dots are a "Slides" group, the current one aria-current="true".',
    props: {
        label: { type: 'string', default: null, description: 'Names the carousel region ("Selected work").' },
    },
    parts: {
        track: { description: 'The scroll-snap track holding the slides (no visible scrollbar).' },
        controls: { description: 'The row of arrows and dots under the track.' },
        dots: { description: 'The dots group, one per slide.' },
        dot: { description: 'A dot; jumps to its slide.' },
    },
    states: {
        current: { attribute: 'aria-current', values: ['true', 'false'], description: 'On a dot: its slide is the active one.' },
        disabled: { attribute: 'disabled', description: 'On an arrow at its end of the track.' },
    },
    slots: { default: { description: 'The carousel.slides.', required: true } },
    uses: ['carousel.slide', 'button', 'icon'],
    usage: {
        livewire: `<sigta:carousel label="Selected work">
    <sigta:carousel.slide>…</sigta:carousel.slide>
    <sigta:carousel.slide>…</sigta:carousel.slide>
</sigta:carousel>`,
        svelte: `<Carousel label="Selected work">
    <Carousel.Slide>…</Carousel.Slide>
    <Carousel.Slide>…</Carousel.Slide>
</Carousel>`,
    },
    notes: {
        shared: [
            'The dots are generated from the slide count once the page runs; smooth scrolling is skipped under prefers-reduced-motion.',
            'For several slides per view, give each carousel.slide a basis (basis="basis-1/2 md:basis-1/3").',
        ],
    },
    examples: [
        { label: 'One per view', tree: [oneOnly()] },
        {
            label: 'Multi per view (basis)',
            tree: [c('carousel', { label: 'Snapshots' }, snapshot('Discovery'), snapshot('Design'), snapshot('Build'), snapshot('Launch'))],
        },
        {
            label: 'Next moves one slide on',
            tree: [oneOnly()],
            interactions: [{ click: '[aria-label="Next slide"]', wait: 600 }],
        },
        {
            label: 'A dot jumps to its slide; the scroll position sets the active dot',
            tree: [oneOnly()],
            interactions: [{ click: '[data-part="dot"]:last-child', wait: 600 }],
        },
        {
            label: 'Scrolled by hand to the end',
            tree: [c('carousel', { label: 'Snapshots' }, snapshot('Discovery'), snapshot('Design'), snapshot('Build'), snapshot('Launch'))],
            interactions: [{ script: 'document.querySelector(\'[data-part="track"]\').scrollTo({ left: 100000 })', wait: 600 }],
        },
    ],
});
