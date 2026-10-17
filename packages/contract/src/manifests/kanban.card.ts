import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'kanban.card',
    title: 'Kanban card',
    tier: 'components',
    page: 'kanban',
    svelte: 'Kanban.Card',
    description: 'A draggable card on raised paper inside a kanban.column: lifts on hover and focus, fades while it is dragged.',
    aria: 'role="listitem", focusable (tabindex="0") and announced with aria-roledescription="draggable"; draggable="true" for native drag and drop.',
    props: {
        id: { type: 'string', default: null, description: 'Identifies the card in the move events (card); generated when not given.' },
    },
    states: {
        dragging: { attribute: 'data-dragging', description: 'While the card is dragged.', conformance: false },
    },
    slots: { default: { description: 'The card\'s content.', required: true } },
    usage: {
        livewire: '<sigta:kanban.card :id="$task->key" wire:key="card-{{ $task->key }}">{{ $task->title }}</sigta:kanban.card>',
        svelte: '<Kanban.Card id={task.id}>{task.title}</Kanban.Card>',
    },
    notes: {
        shared: [
            'The card carries the group class, so group-hover utilities in its content (a button that shows on hover) react to hovering the card.',
            'Your class lands on the card and wins over its own look.',
        ],
    },
    examples: [
        {
            label: 'Cards with your own content',
            tree: [c('kanban', {},
                c('kanban.column', { id: 'cards', label: 'Cards' },
                    c('kanban.card', { id: 'plain' }, 'Plain text'),
                    c('kanban.card', { id: 'rich', class: 'flex items-center justify-between gap-3' },
                        'Halyard OS',
                        c('badge', { variant: 'accent' }, 'Due'),
                    ),
                ),
            )],
        },
        {
            label: 'Hover lifts a card',
            tree: [c('kanban', {},
                c('kanban.column', { id: 'hover', label: 'Hover' },
                    c('kanban.card', { id: 'lifted' }, 'Hover me'),
                ),
            )],
            interactions: [{ hover: '[data-card-id="lifted"]' }],
        },
    ],
});
