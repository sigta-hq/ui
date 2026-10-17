import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'section-header',
    title: 'Section header',
    tier: 'layout',
    page: 'section-header',
    svelte: 'SectionHeader',
    description: 'A section\'s opening row: the heading on the left (with an optional eyebrow above it), a graphite lede on the right, baseline-aligned; the lede wraps under the heading when the row runs out of room.',
    aria: 'The heading is an <h2>; the lede a <p>.',
    props: {
        heading: { type: 'string', default: null, description: 'The heading (an h2 at the xl size).' },
        eyebrow: { type: 'string', default: null, description: 'Mono kicker above the heading.' },
        lede: { type: 'string', default: null, description: 'The graphite lede on the right (or pass it as the slot).' },
    },
    parts: {
        eyebrow: { description: 'The eyebrow.' },
        heading: { description: 'The heading.' },
        lede: { description: 'The lede.' },
    },
    slots: { default: { description: 'The lede, when the lede prop is not given.' } },
    uses: ['eyebrow', 'heading', 'text'],
    examples: [
        {
            label: 'Heading + lede prop',
            tree: [c('section-header', { heading: 'Kan du genkende det?', lede: 'Fire situationer, hvor det hjælper at have en teknisk stemme på sin egen side af bordet.' })],
        },
        {
            label: 'Heading + lede via default slot',
            tree: [c('section-header', { heading: 'To måder at samarbejde på.' }, 'Én filosofi bag begge: du skal altid vide, hvad du betaler for.')],
        },
        {
            label: 'With optional eyebrow',
            tree: [c('section-header', { eyebrow: 'Om Sigta · Manden bag', heading: 'Jeg hedder Søren, og jeg taler begge sprog.', lede: 'En teknisk stemme på din egen side af bordet.' })],
        },
    ],
});
