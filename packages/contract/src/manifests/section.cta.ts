import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'section.cta',
    title: 'Section · CTA',
    tier: 'sections',
    page: 'cta',
    svelte: 'Section.Cta',
    description: 'The closing call to action: a dark ink band (rounded frame) with a centred heading, a muted lede and the inverse / ghost-inverse button pair; an accent hand-note overhangs the top-right corner on wider screens.',
    aria: 'A <section>; the heading is an <h2>, the actions are links.',
    props: {
        heading: { type: 'string', default: 'Skal vi tage en snak, før du skriver under på noget?', description: 'The heading.' },
        lede: { type: 'string', default: 'En intro-samtale koster ingenting og forpligter ikke. Medbring gerne det tilbud, du er i tvivl om.', description: 'The muted line under the heading; empty to leave it out.' },
        primaryLabel: { type: 'string', default: 'Book en intro-samtale', description: 'The primary button (paper fill, with a trailing arrow).' },
        primaryHref: { type: 'string', default: 'https://calendly.com/sf-sigta/30min', description: 'Where the primary button goes.' },
        target: { type: 'string', default: null, description: 'The primary link\'s target ("_blank" opens a new tab and adds rel="noopener"). None by default.' },
        secondaryLabel: { type: 'string', default: 'Skriv en mail', description: 'The secondary button (hairline outline).' },
        secondaryHref: { type: 'string', default: 'mailto:sf@sigta.dk', description: 'Where the secondary button goes.' },
        secondaryTarget: { type: 'string', default: null, description: 'The secondary link\'s target.' },
        annotation: { type: 'string', default: 'lad os tegne det op ↘', description: 'The accent hand-note on the band\'s corner (hidden on a phone); empty to leave it out.' },
    },
    parts: {
        wrap: { description: 'The centred page-width wrap.' },
        band: { description: 'The dark ink band.' },
        note: { description: 'The accent hand-note (an annot).' },
        heading: { description: 'The <h2>.' },
        lede: { description: 'The muted line.' },
        actions: { description: 'The button row.' },
        arrow: { description: 'The primary button\'s arrow; it nudges forward on hover.' },
    },
    uses: ['button', 'annot'],
    usage: {
        livewire: `<sigta:section.cta />
<sigta:section.cta target="_blank" />
<sigta:section.cta heading="Klar til at komme videre?" primary-label="Book et møde" primary-href="/book" annotation="lige her ↘" />`,
        svelte: `<Section.Cta />
<Section.Cta target="_blank" />
<Section.Cta heading="Klar til at komme videre?" primaryLabel="Book et møde" primaryHref="/book" annotation="lige her ↘" />`,
    },
    notes: {
        shared: [
            'Brand-default Danish copy: it renders complete with no props, and every prop is optional and overrides one piece. Key props: heading, lede, primaryLabel, primaryHref, target, secondaryLabel, secondaryHref, secondaryTarget, annotation.',
            'The dark band uses the inverse surface and the inverse / ghost-inverse button variants — never override its colours.',
        ],
    },
    examples: [
        {
            label: 'Ink CTA band — zero-config (inverse + ghost-inverse buttons)',
            frame: 'wide',
            tree: [c('section.cta')],
        },
        {
            label: 'Custom copy + links',
            frame: 'wide',
            tree: [c('section.cta', {
                heading: 'Klar til at komme videre?',
                lede: 'Vi svarer inden for en arbejdsdag.',
                primaryLabel: 'Book et møde',
                primaryHref: 'https://calendly.com/sf-sigta/30min',
                target: '_blank',
                secondaryLabel: 'Skriv en mail',
                secondaryHref: 'mailto:sf@sigta.dk',
                annotation: 'lige her ↘',
            })],
        },
    ],
});
