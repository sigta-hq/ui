import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';
import { sections } from '../sections.ts';

export default defineComponent({
    name: 'section.footer',
    title: 'Section · Footer',
    tier: 'sections',
    page: 'footer',
    svelte: 'Section.Footer',
    description: 'The site footer: the brand and a tagline, columns of links under mono headings, and a dashed signoff line; on a hairline. Columns stack on a phone and sit in one row on a desk.',
    aria: 'A <footer> landmark (contentinfo); each column is a heading over a <ul> of links.',
    props: {
        tagline: { type: 'string', default: 'Sigta er tech-partneren for virksomheder uden egen teknisk ledelse: hjælp, rådgivning og udvikling.', description: 'The line under the brand; empty to leave it out.' },
        signoff: { type: 'string', default: '© 2026 Sigta · CVR 45530760', description: 'The mono line under the dashed rule; empty to leave it out.' },
        columns: { type: 'array', default: sections.footer.columns, shape: '{ heading: string; links: { label: string; href: string }[] }[]', description: 'The link columns, a list of { heading, links: [{ label, href }] }. Defaults to the brand Navigation and Kontakt columns.' },
    },
    parts: {
        wrap: { description: 'The centred page-width wrap.' },
        grid: { description: 'The brand column and the link columns.' },
        tagline: { description: 'The tagline.' },
        heading: { description: 'A column heading (an h4).' },
        links: { description: 'A column\'s list of links.' },
        link: { description: 'Each link; underlined on hover.' },
        signoff: { description: 'The signoff line.' },
    },
    uses: ['brand'],
    usage: {
        livewire: `<sigta:section.footer />
<sigta:section.footer tagline="Et lille studie, der bygger holdbar software." signoff="© 2026 Dit Firma" :columns="[
    ['heading' => 'Produkt', 'links' => [['label' => 'Priser', 'href' => '#priser']]],
]" />`,
        svelte: `<Section.Footer />
<Section.Footer tagline="Et lille studie, der bygger holdbar software." signoff="© 2026 Dit Firma" columns={[
    { heading: 'Produkt', links: [{ label: 'Priser', href: '#priser' }] },
]} />`,
    },
    notes: {
        shared: [
            'Brand-default Danish copy: it renders complete with no props, and every prop is optional and overrides one piece.',
            'The root is a <footer> landmark rather than a <section>; it carries the same data-sigta hook.',
        ],
    },
    examples: [
        {
            label: 'Site footer — zero-config (brand, nav columns, signoff)',
            frame: 'wide',
            tree: [c('section.footer')],
        },
        {
            label: 'Custom tagline + columns',
            frame: 'wide',
            tree: [c('section.footer', {
                tagline: 'Et lille studie, der bygger holdbar software.',
                signoff: '© 2026 Dit Firma · CVR 00000000',
                columns: [
                    { heading: 'Produkt', links: [{ label: 'Funktioner', href: '#funktioner' }, { label: 'Priser', href: '#priser' }] },
                    { heading: 'Kontakt', links: [{ label: 'Skriv til os', href: 'mailto:hej@eksempel.dk' }] },
                ],
            })],
        },
    ],
});
