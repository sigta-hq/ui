import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'tooltip',
    title: 'Tooltip',
    tier: 'components',
    page: 'tooltip',
    svelte: 'Tooltip',
    description: 'Ink bubble on hover/focus with a 150ms open delay; Escape dismisses it. The slot (children) is the trigger.',
    aria: 'The bubble is role="tooltip"; the trigger wrapper points at it with aria-describedby.',
    props: {
        text: { type: 'string', required: true, description: 'The bubble text.' },
        position: { type: 'enum', values: ['top', 'bottom', 'left', 'right'], default: 'top', description: 'Side of the trigger; unknown values sit on top.' },
    },
    parts: {
        trigger: { description: 'Wraps the slot; carries aria-describedby.' },
        bubble: { description: 'The ink bubble (role="tooltip"), hidden until shown.' },
    },
    states: {
        position: { attribute: 'data-position', values: ['top', 'bottom', 'left', 'right'], description: 'On the root: which side the bubble sits.' },
    },
    slots: { default: { description: 'The trigger — usually a button.', required: true } },
    usage: {
        livewire: '<sigta:tooltip text="Delete permanently"><sigta:button variant="ghost"><sigta:icon name="trash-2" /></sigta:button></sigta:tooltip>',
        svelte: '<Tooltip text="Delete permanently"><Button variant="ghost"><Icon name="trash-2" /></Button></Tooltip>',
    },
    notes: {
        shared: [
            'Use it instead of a title= attribute or a custom popover.',
            'A tooltip only adds a hint: an icon-only trigger still needs its own aria-label.',
        ],
    },
    examples: [
        {
            label: 'Hover or focus the trigger',
            tree: [el('div', { class: 'flex flex-wrap items-center gap-6 py-8' },
                c('tooltip', { text: 'Saves a draft, publishes nothing' }, c('button', {}, 'Save draft')),
                c('tooltip', { text: 'Copied to clipboard' }, c('button', { variant: 'ghost' }, c('icon', { name: 'copy', size: 'sm' }), ' Copy')),
            )],
            interactions: [{ hover: '[data-sigta="tooltip"] button', wait: 450 }],
        },
        {
            label: 'Positions',
            tree: [el('div', { class: 'flex flex-wrap items-center gap-6 py-10' },
                c('tooltip', { text: 'Above (default)' }, c('button', { size: 'sm' }, 'Top')),
                c('tooltip', { text: 'Below', position: 'bottom' }, c('button', { size: 'sm' }, 'Bottom')),
                c('tooltip', { text: 'Leading side', position: 'left' }, c('button', { size: 'sm' }, 'Left')),
                c('tooltip', { text: 'Trailing side', position: 'right' }, c('button', { size: 'sm' }, 'Right')),
            )],
        },
        ...(['bottom', 'left', 'right'] as const).map((position) => ({
            label: `Shown on the ${position}`,
            tree: [el('div', { class: 'flex justify-center px-24 py-10' },
                c('tooltip', { text: `Bubble on the ${position}`, position }, c('button', { size: 'sm' }, 'Hover me')),
            )],
            interactions: [{ focus: '[data-sigta="tooltip"] button', wait: 450 }],
        })),
        {
            label: 'Escape dismisses',
            tree: [el('div', { class: 'py-8' }, c('tooltip', { text: 'Gone on Escape' }, c('button', {}, 'Hover me')))],
            interactions: [{ hover: '[data-sigta="tooltip"] button', wait: 450 }, { press: 'Escape' }],
        },
    ],
});
