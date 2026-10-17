import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'accordion.item',
    title: 'Accordion item',
    tier: 'components',
    page: 'accordion',
    svelte: 'Accordion.Item',
    description: 'One question: a native <details>/<summary> on a hairline, the heading in the head face and a mono "+" that rotates into a cross while it is open. No JS.',
    aria: 'Native <details>/<summary>.',
    props: {
        heading: { type: 'string', required: true, description: 'The summary line (the question).' },
        name: { type: 'string', default: null, description: 'The <details name>; defaults to the accordion\'s name. Items sharing a name open one at a time.' },
        open: { type: 'boolean', default: false, native: true, description: 'Open on load: the native open attribute on the <details>.', svelte: { note: 'bind:open follows it as the reader opens and closes it.' } },
    },
    parts: {
        summary: { description: 'The <summary> row with the heading and the "+".' },
        content: { description: 'The answer, graphite at a 60ch measure.' },
    },
    states: {
        open: { attribute: 'open', description: 'On the <details>: turns the "+" into a cross.' },
    },
    slots: { default: { description: 'The answer.', required: true } },
    uses: ['accordion'],
    notes: {
        livewire: ['Every other attribute (open, wire:key, data-*) lands on the <details>.'],
        svelte: ['Every other attribute lands on the <details>.'],
    },
    examples: [
        {
            label: 'Open by default',
            tree: [c('accordion', {},
                c('accordion.item', { heading: 'What does it cost?', open: true }, el('p', {}, 'One flat hourly rate, no minimum.')),
                c('accordion.item', { heading: 'Can we pause?' }, el('p', {}, 'Any month, with no notice.')),
            )],
        },
        {
            label: 'Toggled by a click',
            tree: [c('accordion', {}, c('accordion.item', { heading: 'Click to open' }, el('p', {}, 'The answer folds out.')))],
            interactions: [{ click: 'summary' }],
        },
    ],
});
