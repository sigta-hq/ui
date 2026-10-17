import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'text',
    title: 'Text',
    tier: 'elements',
    page: 'typography',
    svelte: 'Text',
    description: 'Body copy in Newsreader: the default body, a lede, muted secondary text, a mono caption and a Caveat hand note.',
    aria: 'A <p>.',
    props: {
        variant: { type: 'enum', values: ['body', 'lede', 'muted', 'caption', 'hand'], default: 'body', description: 'body, lede (intro), muted, caption (mono), hand (Caveat).' },
    },
    slots: { default: { description: 'The text.', required: true } },
    usage: {
        livewire: '<sigta:text variant="muted">Secondary detail.</sigta:text>',
        svelte: '<Text variant="muted">Secondary detail.</Text>',
    },
    notes: { shared: ['Use it instead of a <p> with text utilities. lede = large intro; muted = graphite secondary; caption = small mono; hand = Caveat handwriting.'] },
    examples: [
        {
            label: 'Variants',
            tree: [
                c('text', { variant: 'lede' }, 'We design and build software the careful way — researched, drawn, typed, and tested.'),
                c('text', {}, 'Body text in Newsreader. Comfortable at long line lengths, with a paper-first reading rhythm and ', el('em', {}, 'true italics'), ' where emphasis calls for them.'),
                c('text', { variant: 'muted' }, 'Muted body text for secondary explanations.'),
                c('text', { variant: 'caption' }, 'SIGTA · est. 2017 · made on paper'),
                c('text', { variant: 'hand' }, 'nice work!'),
            ],
        },
    ],
});
