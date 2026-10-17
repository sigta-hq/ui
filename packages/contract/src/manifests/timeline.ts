import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

const history = c('timeline', {},
    c('timeline.item', { heading: 'Kickoff', meta: 'Jan 2026' }, 'Scoped the build, drew the first flows on paper.'),
    c('timeline.item', { heading: 'First cut', meta: 'Feb 2026' }, 'MVP in front of five design partners.'),
    c('timeline.item', { heading: 'Shipped', meta: 'Mar 2026', variant: 'accent' }, 'Live for all workspaces — no migration needed.'),
    c('timeline.item', { heading: 'Now', meta: 'Ongoing' }, 'Steady ink: weekly releases, one honest changelog.'),
);

export default defineComponent({
    name: 'timeline',
    title: 'Timeline',
    tier: 'components',
    page: 'timeline',
    svelte: 'Timeline',
    description: 'Vertical timeline: an ordered list with a hairline rail down the left and an ink dot per entry.',
    aria: 'A native <ol> of <li> entries.',
    props: {},
    slots: { default: { description: 'The timeline.item entries, oldest first.', required: true } },
    uses: ['timeline.item'],
    notes: {
        shared: ['Accent is scarce: give one entry (the milestone) variant="accent", not several.'],
    },
    examples: [
        { label: 'Project history', tree: [history] },
    ],
});
