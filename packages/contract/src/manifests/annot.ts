import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'annot',
    title: 'Annotation',
    tier: 'elements',
    page: 'typography',
    svelte: 'Annot',
    description: 'Handwritten margin annotation (Caveat), slightly rotated. Decorative by default: decorative=true → aria-hidden.',
    aria: 'aria-hidden while decorative (the default); pass decorative={false} when the note carries meaning.',
    props: {
        rotate: { type: 'number', default: -6, description: 'Rotation in degrees.' },
        tone: {
            type: 'enum',
            values: ['ink', 'muted', 'accent'],
            default: null,
            description: 'ink, muted (graphite) or accent (for annotations on dark bands). Unset: muted when muted is set, else ink.',
        },
        muted: { type: 'boolean', default: false, description: 'Back-compat for tone="muted"; tone wins when both are given.' },
        decorative: { type: 'boolean', default: true, description: 'Hides it from assistive technology (aria-hidden).' },
    },
    states: {
        tone: { attribute: 'data-tone', values: ['ink', 'muted', 'accent'], description: 'The resolved tone (from tone, or muted).' },
    },
    slots: { default: { description: 'The note.', required: true } },
    usage: {
        livewire: '<sigta:annot tone="accent">lad os tegne det op ↘</sigta:annot>',
        svelte: '<Annot tone="accent">lad os tegne det op ↘</Annot>',
    },
    notes: {
        shared: [
            'A hand-drawn flourish: use it beside content as a margin note, sparingly. Position it with classes (absolute -top-3 right-[8%]) on a panel, which does not clip it.',
        ],
    },
    examples: [
        {
            label: 'Annotations',
            tree: [el('div', { class: 'flex gap-8 items-center' },
                c('annot', {}, 'drawn, not generated ↓'),
                c('annot', { rotate: 4, muted: true }, 'a real chart, on real paper'),
            )],
        },
        {
            label: 'Accent on a dark band',
            frame: 'inverse',
            tree: [el('div', { class: 'rounded-frame bg-inverse p-8' }, c('annot', { tone: 'accent' }, 'lad os tegne det op ↘'))],
        },
    ],
});
