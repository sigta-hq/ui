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
        size: { type: 'enum', values: ['xs', 'sm', 'base', 'lg', 'xl'], default: 'base', description: 'xs 16px, sm 20px, base 28px, lg 44px, xl 72px; small sizes draw a heavier pen.' },
        variant: { type: 'enum', values: ['compass', 'sketch'], default: 'compass', description: 'compass: the mark in use — the handle turns, the legs swing round each other and the pencil draws a circle on the paper, then lifts it; sketch: the whole mark draws itself on a loop, for longer waits.' },
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
    usage: {
        livewire: `<sigta:loader />
<sigta:loader size="lg" :value="$import->progress" :state="$import->done ? 'done' : 'working'" />
<sigta:loader variant="sketch" size="xl" label="Preparing your workspace" />`,
        svelte: `<Loader />
<Loader size="lg" value={job.progress} state={job.done ? 'done' : 'working'} />
<Loader variant="sketch" size="xl" label="Preparing your workspace" />`,
    },
    notes: {
        shared: [
            'Use it wherever you would write "Loading…" or reach for a spinner.',
            'States: queued (the compass standing, no arc yet, graphite), working, done (the mark complete with its arc and a small stamp), failed (shakes, danger), idle (the complete mark, graphite).',
            'When a working loader turns done, the runtime lets it finish its turn, close the circle and raise it into the mark\'s arc before the stamp (with reduced motion it lands at once). currentColor throughout.',
            'It never moves (sigta-ui.md, MUST 5). Keep it rendered and change state rather than mounting it per request; if it must be hidden at rest, keep its box with class="invisible". Put it on the side of its text that stays put (after right-aligned text at a right edge) and reserve the text\'s room so a new word never pushes it.',
            'Prefer {{tag:save-state}}, a busy {{tag:button}} or {{tag:job}}, which hold their space already.',
        ],
        livewire: ['Never wrap it in @if, x-if, x-show or a bare wire:loading; drive state with a morph or x-bind:data-state. Hidden at rest: class="invisible" wire:loading.class.remove="invisible".'],
        svelte: ['Never wrap it in {#if}; pass state (and value) from your data.'],
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
