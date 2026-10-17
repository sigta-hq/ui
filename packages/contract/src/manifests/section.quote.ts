import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'section.quote',
    title: 'Section · Quote',
    tier: 'sections',
    page: 'quote',
    svelte: 'Section.Quote',
    description: 'A centred pull-quote in big light Fraunces italic, curly-quoted, over a mono cite; generous rhythm above and below.',
    aria: 'A <section> with a <blockquote>.',
    props: {
        quote: { type: 'string', default: 'Du behøver ikke forstå koden for at træffe gode beslutninger. Du skal bare kende én, der gør.', description: 'The quote, without quotation marks (the section adds them).' },
        cite: { type: 'string', default: 'Sådan arbejder Sigta', description: 'The mono line under the quote; empty to leave it out.' },
    },
    parts: {
        wrap: { description: 'The centred page-width wrap.' },
        quote: { description: 'The <blockquote>.' },
        cite: { description: 'The cite line.' },
    },
    usage: {
        livewire: `<sigta:section.quote />
<sigta:section.quote quote="Godt håndværk taler for sig selv." cite="Sigtas arbejdsfilosofi" />`,
        svelte: `<Section.Quote />
<Section.Quote quote="Godt håndværk taler for sig selv." cite="Sigtas arbejdsfilosofi" />`,
    },
    notes: {
        shared: [
            'Brand-default Danish copy: it renders complete with no props, and every prop is optional and overrides one piece.',
        ],
    },
    examples: [
        {
            label: 'Pull-quote — zero-config',
            frame: 'wide',
            tree: [c('section.quote')],
        },
        {
            label: 'Custom quote + cite',
            frame: 'wide',
            tree: [c('section.quote', { quote: 'Godt håndværk taler for sig selv.', cite: 'Sigtas arbejdsfilosofi' })],
        },
    ],
});
