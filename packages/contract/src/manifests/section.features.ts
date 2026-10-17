import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'section.features',
    title: 'Section · Features',
    tier: 'sections',
    page: 'features',
    svelte: 'Section.Features',
    description: 'What you offer: a section header over a tile grid of services, each with a mono number, an ink icon, a title and graphite text. Ships the four "Sådan hjælper vi" services.',
    aria: 'A <section>; each service is a tile (<article>) with an <h3>. Icons are decorative.',
    props: {
        eyebrow: { type: 'string', default: null, description: 'Mono kicker above the heading.' },
        heading: { type: 'string', default: 'Sådan hjælper vi.', description: 'The section heading.' },
        lede: { type: 'string', default: 'Fire måder at bruge en tech-partner på. De fleste samarbejder starter med én og vokser derfra.', description: 'The lede beside the heading.' },
        cols: { type: 'number', default: 4, description: 'Columns on a wide screen: 2, 3 or 4 (collapsing 4 → 2 → 1, 3 → 1, 2 → 1).' },
        items: { type: 'array', default: null, shape: '{ num?: string; icon?: string; title?: string; text?: string }[]', description: 'The services, a list of { num, icon, title, text } (each optional); icon is inline <svg…> markup or an icon name. Without items: the four brand services.' },
    },
    parts: {
        header: { description: 'The section header.' },
        tile: { description: 'Each service tile.' },
        icon: { description: 'The icon holder (ink, pushed to the top of the tile).' },
        glyph: { description: 'A named icon, drawn at 34px like the inline SVGs.' },
        title: { description: 'The service title (an h3).' },
        text: { description: 'The service text.' },
    },
    uses: ['section-header', 'tile-grid', 'tile-grid.tile', 'heading', 'icon'],
    usage: {
        livewire: `<sigta:section.features />
<sigta:section.features heading="Vores ydelser" :cols="3" :items="[
    ['num' => '01', 'icon' => 'compass', 'title' => 'Retning', 'text' => 'Strategi og roadmap.'],
    ['num' => '02', 'icon' => 'code', 'title' => 'Udvikling', 'text' => 'Vi skriver koden.'],
    ['num' => '03', 'icon' => 'shield-check', 'title' => 'Drift', 'text' => 'Den bliver ved med at virke.'],
]" />`,
        svelte: `<Section.Features />
<Section.Features heading="Vores ydelser" cols={3} items={[
    { num: '01', icon: 'compass', title: 'Retning', text: 'Strategi og roadmap.' },
    { num: '02', icon: 'code', title: 'Udvikling', text: 'Vi skriver koden.' },
    { num: '03', icon: 'shield-check', title: 'Drift', text: 'Den bliver ved med at virke.' },
]} />`,
    },
    notes: {
        shared: [
            'Brand-default Danish copy: it renders complete with no props, and every prop is optional and overrides one piece.',
            'An item\'s icon is either inline <svg…> markup (currentColor strokes) or the name of an icon from the set.',
        ],
    },
    examples: [
        {
            label: 'Zero-config (brand defaults)',
            frame: 'wide',
            tree: [c('section.features')],
        },
        {
            label: 'Own services in three columns, with named icons',
            frame: 'wide',
            tree: [c('section.features', {
                eyebrow: 'Ydelser',
                heading: 'Vores ydelser',
                lede: 'Tre ting, vi gør godt.',
                cols: 3,
                items: [
                    { num: '01', icon: 'compass', title: 'Retning', text: 'Strategi, roadmap og arkitektur.' },
                    { num: '02', icon: 'code', title: 'Udvikling', text: 'Vi skriver koden. Omhyggeligt.' },
                    { num: '03', icon: 'shield-check', title: 'Drift', text: 'Softwaren bliver ved med at virke.' },
                ],
            })],
        },
    ],
});
