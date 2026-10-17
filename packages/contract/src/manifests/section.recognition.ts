import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'section.recognition',
    title: 'Section · Recognition',
    tier: 'sections',
    page: 'recognition',
    svelte: 'Section.Recognition',
    description: 'The "Kan du genkende det?" block: a section header over a two-column tile grid of scene/answer cards (the scene a quote in Fraunces italic, the answer led by an arrow), then a closing line and a call to action. Ships the four brand situations.',
    aria: 'A <section>; each card is a tile (<article>).',
    props: {
        heading: { type: 'string', default: 'Kan du genkende det?', description: 'The section heading.' },
        lede: { type: 'string', default: 'Fire situationer, hvor det hjælper at have en teknisk stemme på sin egen side af bordet.', description: 'The lede beside the heading.' },
        items: { type: 'array', default: null, shape: '{ num?: string; scene?: string; answer?: string }[]', description: 'The cards, a list of { num, scene, answer }: a mono kicker, the scene (a quote) and the answer. Without items: the four brand situations.' },
        closingText: { type: 'string', default: 'Hvis bare ét af dem rammer, så lad os tage en kop kaffe.', description: 'The line under the grid; empty to leave it out.' },
        ctaLabel: { type: 'string', default: 'Book en intro-samtale', description: 'The call to action beside the closing line (with a trailing arrow); empty to leave it out.' },
        ctaHref: { type: 'string', default: 'https://calendly.com/sf-sigta/30min', description: 'Where the call to action goes.' },
        target: { type: 'string', default: '_blank', description: 'The call to action\'s target: a new tab by default (with rel="noopener").' },
    },
    parts: {
        header: { description: 'The section header.' },
        tile: { description: 'Each card.' },
        scene: { description: 'The scene, a quote in Fraunces italic.' },
        answer: { description: 'The answer, led by a mono arrow.' },
        outro: { description: 'The closing row under the grid.' },
        closing: { description: 'The closing line.' },
        arrow: { description: 'The call to action\'s arrow; it nudges forward on hover.' },
    },
    uses: ['section-header', 'tile-grid', 'tile-grid.tile', 'button'],
    usage: {
        livewire: `<sigta:section.recognition />
<sigta:section.recognition heading="Lyder det bekendt?" closing-text="Ring til os." cta-label="Skriv en mail" cta-href="mailto:hej@eksempel.dk" :items="[
    ['num' => '01 · Tilbuddet', 'scene' => '\"Er prisen rimelig?\"', 'answer' => 'Vi læser det med dig.'],
]" />`,
        svelte: `<Section.Recognition />
<Section.Recognition heading="Lyder det bekendt?" closingText="Ring til os." ctaLabel="Skriv en mail" ctaHref="mailto:hej@eksempel.dk" items={[
    { num: '01 · Tilbuddet', scene: '"Er prisen rimelig?"', answer: 'Vi læser det med dig.' },
]} />`,
    },
    notes: {
        shared: [
            'Brand-default Danish copy: it renders complete with no props, and every prop is optional and overrides one piece.',
        ],
    },
    examples: [
        {
            label: 'Zero-config (brand defaults)',
            frame: 'wide',
            tree: [c('section.recognition')],
        },
        {
            label: 'Own cards, closing line and call to action',
            frame: 'wide',
            tree: [c('section.recognition', {
                heading: 'Lyder det bekendt?',
                lede: 'To situationer, vi ofte hører om.',
                items: [
                    { num: '01 · Tilbuddet', scene: '"Er prisen rimelig?"', answer: 'Vi læser det med dig, linje for linje.' },
                    { num: '02 · Leverandøren', scene: '"Hvorfor tager det så lang tid?"', answer: 'Vi finder ud af, hvor det knirker.' },
                ],
                closingText: 'Ring til os.',
                ctaLabel: 'Skriv en mail',
                ctaHref: 'mailto:hej@eksempel.dk',
                target: '_self',
            })],
        },
    ],
});
