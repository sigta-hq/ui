import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'label',
    title: 'Label',
    tier: 'elements',
    page: 'field',
    svelte: 'Label',
    description: 'Mono uppercase form label. variant="inverse" re-colours it for a dark surface.',
    aria: 'Native <label>; point it at its control with for.',
    props: {
        variant: { type: 'enum', values: ['default', 'inverse'], default: 'default', description: 'inverse for a dark surface (inside an inverse panel).' },
    },
    slots: { default: { description: 'The label text.', required: true } },
    notes: { shared: ['inverse re-colours it for a dark surface (inside <sigta:panel variant="inverse"> / <Panel variant="inverse">).'] },
    examples: [
        { label: 'Standalone label', tree: [c('label', { for: 'kit-label-target' }, 'Engagement type')] },
        { label: 'On a dark surface', frame: 'inverse', tree: [el('div', { class: 'bg-inverse p-6 rounded-panel' }, c('label', { variant: 'inverse' }, 'Engagement type'))] },
    ],
});
