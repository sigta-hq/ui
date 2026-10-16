import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'loader',
    title: 'Loader',
    tier: 'elements',
    page: 'loader',
    svelte: 'Loader',
    description: 'The Sigta compass: the brand mark as a loader. While it works the legs swing round and the pencil draws a circle; with a value the circle is drawn as far round as the work has come; done stands the mark complete and stamps; failed shakes it red. Never a generic spinner.',
    aria: 'role="progressbar" with aria-label (and aria-value* with a value); aria-hidden when decorative.',
    props: {
        size: { type: 'enum', values: ['xs', 'sm', 'base', 'lg', 'xl'], default: 'base', description: 'xs 16px … xl 72px; small sizes draw a heavier pen.' },
        variant: { type: 'enum', values: ['compass', 'sketch'], default: 'compass', description: 'compass turns; sketch draws the whole mark on a loop, for longer waits.' },
        state: { type: 'enum', values: ['idle', 'queued', 'working', 'done', 'failed'], default: 'working', description: 'Unknown values fall back to working.' },
        value: { type: 'number', default: null, description: 'Progress 0–100 (clamped); done fills it.' },
        label: { type: 'string', default: null, description: 'Accessible name; defaults to the state (Working, Queued, Done, Failed, Ready).' },
        decorative: { type: 'boolean', default: false, description: 'Hide from assistive tech when a parent already announces the work.' },
    },
    parts: {
        svg: { description: 'The mark; its paths carry the class hooks sigta-loader-frame, -ring(-front/-back), -leg (-needle/-pencil), -arc, -pivot.' },
    },
    states: {
        state: { attribute: 'data-state', values: ['idle', 'queued', 'working', 'done', 'failed'], description: 'What the work is doing.' },
        progress: { attribute: 'data-progress', description: 'Present with a value; --sigta-progress carries it.' },
        landing: { attribute: 'data-landing', description: 'Set by the runtime while a finished compass closes its circle.' },
    },
    notes: {
        shared: [
            'A loader never moves and nothing moves around it: keep labels beside it rendered through every state.',
            'Drive the state from data (a poll, a prop); the runtime lands the compass when working turns done.',
        ],
    },
    examples: [
        {
            label: 'Sizes',
            tree: ['xs', 'sm', 'base', 'lg', 'xl'].map((size) => c('loader', { size })),
        },
        {
            label: 'States',
            tree: [
                c('loader', { state: 'idle' }),
                c('loader', { state: 'queued' }),
                c('loader', { state: 'working', value: 42 }),
                c('loader', { state: 'done' }),
                c('loader', { state: 'failed' }),
            ],
        },
        {
            label: 'Sketch and decorative',
            tree: [
                c('loader', { variant: 'sketch', size: 'lg', label: 'Importing contacts' }),
                c('loader', { decorative: true, size: 'sm' }),
            ],
        },
    ],
});
