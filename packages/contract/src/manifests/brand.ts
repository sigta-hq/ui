import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'brand',
    title: 'Brand',
    tier: 'elements',
    page: 'typography',
    svelte: 'Brand',
    description: 'The SIGTA wordmark + glyph lockup. currentColor throughout, so a text colour on it recolours the whole mark.',
    aria: 'The glyph is decorative (aria-hidden); the wordmark is text. A glyph-only brand in a link needs the link labelled.',
    props: {
        name: { type: 'string', default: 'SIGTA', description: 'The wordmark text.' },
        glyphOnly: { type: 'boolean', default: false, description: 'Only the glyph, no wordmark.' },
        size: { type: 'enum', values: ['sm', 'base', 'lg'], default: 'base', description: 'Glyph 22 / 30 / 38px with a matching wordmark.' },
    },
    parts: {
        glyph: { description: 'The square glyph (SVG).' },
        name: { description: 'The wordmark.' },
    },
    usage: {
        livewire: '<sigta:brand size="lg" />',
        svelte: '<Brand size="lg" />',
    },
    notes: {
        shared: ['Recolour it with a text utility on the brand itself (class="text-inverse-fg" on a dark band); never hard-code a colour.'],
    },
    examples: [
        {
            label: 'Brand lockup',
            tree: [el('div', { class: 'flex items-end gap-8' },
                c('brand', { size: 'sm' }),
                c('brand', {}),
                c('brand', { size: 'lg' }),
                c('brand', { glyphOnly: true }),
                el('span', { class: 'bg-inverse text-inverse-fg p-3 rounded-base inline-flex' }, c('brand', { class: 'text-inverse-fg' })),
            )],
        },
    ],
});
