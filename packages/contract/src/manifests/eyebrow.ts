import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'eyebrow',
    title: 'Eyebrow',
    tier: 'elements',
    page: 'typography',
    svelte: 'Eyebrow',
    description: 'Mono uppercase kicker with a leading dash rule. Sits above section headings.',
    aria: 'A <p>; the dash is a pseudo-element.',
    props: {},
    slots: { default: { description: 'The kicker text.', required: true } },
    usage: {
        livewire: '<sigta:eyebrow>01 · Install</sigta:eyebrow>',
        svelte: '<Eyebrow>01 · Install</Eyebrow>',
    },
    examples: [
        { label: 'Kicker', tree: [c('eyebrow', {}, 'Design & engineering studio · est. 2017')] },
        { label: 'Numbered step', tree: [c('eyebrow', {}, '01 · Install')] },
    ],
});
