import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'section.proof',
    title: 'Section · Proof',
    tier: 'sections',
    page: 'proof',
    svelte: 'Section.Proof',
    description: 'A centred proof strip between hairlines: an inline years figure and the client names as mono placeholder wordmarks (the real logo files live in the site, not the package).',
    aria: 'A <section> labelled "Erfaring og kunder".',
    props: {
        yearsValue: { type: 'string', default: '14 år', description: 'The figure.' },
        yearsLabel: { type: 'string', default: 'erfaring med software', description: 'The label under the figure.' },
        logos: { type: 'array', default: ['Park Styling', 'Jelling Musikfestival'], shape: 'string[]', description: 'The client names (a list of strings), set as mono wordmarks.' },
    },
    parts: {
        wrap: { description: 'The centred page-width wrap.' },
        strip: { description: 'The strip between hairlines.' },
        stat: { description: 'The years figure (an inline stat).' },
        logo: { description: 'Each client wordmark.' },
    },
    uses: ['stat'],
    usage: {
        livewire: `<sigta:section.proof />
<sigta:section.proof years-value="20 år" years-label="i branchen" :logos="['Acme A/S', 'Foobar ApS']" />`,
        svelte: `<Section.Proof />
<Section.Proof yearsValue="20 år" yearsLabel="i branchen" logos={['Acme A/S', 'Foobar ApS']} />`,
    },
    notes: {
        shared: [
            'Brand-default Danish copy: it renders complete with no props, and every prop is optional and overrides one piece.',
        ],
    },
    examples: [
        {
            label: 'Proof strip — zero-config (years stat + placeholder logos)',
            frame: 'wide',
            tree: [c('section.proof')],
        },
        {
            label: 'Custom years + logos',
            frame: 'wide',
            tree: [c('section.proof', { yearsValue: '20 år', yearsLabel: 'i branchen', logos: ['Acme A/S', 'Foobar ApS', 'Baz Group'] })],
        },
    ],
});
