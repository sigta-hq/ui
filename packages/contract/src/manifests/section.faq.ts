import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';
import { sections } from '../sections.ts';

export default defineComponent({
    name: 'section.faq',
    title: 'Section · FAQ',
    tier: 'sections',
    page: 'faq',
    svelte: 'Section.Faq',
    description: 'Questions and answers: a section header over a single-open accordion at a reading measure. Ships the seven brand questions.',
    aria: 'A <section>; the entries are native <details>/<summary> (the accordion), grouped by name so one is open at a time.',
    props: {
        heading: { type: 'string', default: 'Spørgsmål, vi ofte får.', description: 'The section heading.' },
        lede: { type: 'string', default: 'Og de ærlige svar. Mangler dit, så spørg løs på intro-samtalen.', description: 'The lede beside the heading.' },
        name: { type: 'string', default: 'faq', description: 'The accordion group name: the browser keeps one entry open at a time. Give each FAQ on a page its own.' },
        items: { type: 'array', default: sections.faq.items, shape: '{ q: string; a: string }[]', description: 'The entries: question (q) and answer (a). Defaults to the seven brand questions.' },
    },
    parts: {
        wrap: { description: 'The centred page-width wrap.' },
        header: { description: 'The section header.' },
        accordion: { description: 'The accordion, centred at 760px.' },
    },
    uses: ['section-header', 'accordion', 'accordion.item'],
    usage: {
        livewire: `<sigta:section.faq />
<sigta:section.faq :items="$faqs" />   {{-- [['q' => …, 'a' => …], …] --}}`,
        svelte: `<Section.Faq />
<Section.Faq items={faqs} />   <!-- [{ q, a }, …] -->`,
    },
    notes: {
        shared: [
            'Brand-default Danish copy: it renders complete with no props, and every prop is optional and overrides one piece. Key props: heading, lede, name, items.',
        ],
    },
    examples: [
        {
            label: 'Spørgsmål — zero-config (7 default entries, single-open)',
            frame: 'wide',
            tree: [c('section.faq')],
        },
        {
            label: 'Custom heading + your own entries',
            frame: 'wide',
            tree: [c('section.faq', {
                heading: 'Ofte stillede spørgsmål',
                lede: 'Kan du ikke finde dit svar? Skriv til os.',
                items: [
                    { q: 'Hvordan kommer vi i gang?', a: 'Book en intro-samtale, så tager vi den derfra.' },
                    { q: 'Hvad koster det?', a: 'Én fast, lav timepris. Ingen overraskelser.' },
                ],
            })],
        },
        {
            label: 'Opening an entry',
            frame: 'wide',
            tree: [c('section.faq', { name: 'faq-open' })],
            interactions: [{ click: '[data-sigta="accordion.item"]:nth-of-type(3) > summary' }],
        },
    ],
});
