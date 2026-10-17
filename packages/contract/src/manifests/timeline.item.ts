import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'timeline.item',
    title: 'Timeline item',
    tier: 'components',
    page: 'timeline',
    svelte: 'Timeline.Item',
    description: 'One entry on the timeline: an ink dot on the rail, a mono meta line (a date), a Fraunces heading and graphite body copy. Each part is optional.',
    aria: 'A native <li>; the dot is decorative (aria-hidden). The heading is an <h3>.',
    props: {
        heading: { type: 'string', default: null, description: 'The entry heading.' },
        meta: { type: 'string', default: null, description: 'Mono uppercase line above the heading, usually a date.' },
        variant: { type: 'enum', values: ['default', 'accent'], default: 'default', description: 'default = ink dot; accent = accent-filled dot with an ink ring, for the milestone.' },
    },
    parts: {
        dot: { description: 'The dot on the rail.' },
        meta: { description: 'The meta line.' },
        heading: { description: 'The heading.' },
        body: { description: 'The slot content (only rendered when there is some).' },
    },
    states: {
        variant: { attribute: 'data-variant', values: ['default', 'accent'], description: 'On the entry.' },
    },
    slots: { default: { description: 'The body copy.' } },
    uses: ['timeline'],
    examples: [
        {
            label: 'Entries',
            tree: [c('timeline', {},
                c('timeline.item', { heading: 'Kickoff', meta: 'Jan' }, 'Scoped the work.'),
                c('timeline.item', { heading: 'Shipped', meta: 'Mar', variant: 'accent' }, 'Live for all.'),
                c('timeline.item', { meta: 'Apr' }, 'A quiet month: meta and body, no heading.'),
                c('timeline.item', { heading: 'Heading only' }),
            )],
        },
    ],
});
