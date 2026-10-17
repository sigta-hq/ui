import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const people = [{ id: 1, name: 'Ada Lovelace' }, { id: 2, name: 'Alan Turing' }, { id: 3, name: 'Grace Hopper' }];

export default defineComponent({
    name: 'assignee-picker',
    title: 'Assignee picker',
    tier: 'components',
    page: 'assignee-picker',
    svelte: 'AssigneePicker',
    description: 'The assignee\'s avatar (or a dashed "unassigned" affordance) that opens a client-side searchable people list: "Unassigned" and every member with their initials avatar. Choosing one hands its id (null for Unassigned) to your code, so one component drops into any context: a board card, a subtask, a task modal.',
    aria: 'The trigger is a <button> labelled by label (default "Assignee"); the panel holds a search box and one button per person. Escape and a click outside close it.',
    props: {
        user: { type: 'object', default: null, shape: '{ id: string | number; name: string } | null', description: 'The current assignee (id + name), or null.', blade: { accepts: 'an Eloquent model or array with id and name' } },
        members: { type: 'array', default: [], shape: '{ id: string | number; name: string }[]', description: 'The assignable people (id + name each).', blade: { accepts: 'any iterable (a Collection) of models or arrays with id and name' } },
        method: { type: 'string', default: null, svelte: false, description: 'Livewire method on the nearest component to call on select: method(...args, userId).' },
        args: { type: 'array', default: [], svelte: false, description: 'Leading arguments passed before the chosen user id.' },
        size: { type: 'enum', values: ['xs', 'sm', 'base', 'lg'], default: 'xs', description: 'Avatar size (the unassigned affordance is 32px at sm, 24px otherwise).' },
        showName: { type: 'boolean', default: false, description: 'Show the assignee\'s name (or "Assign") beside the avatar.' },
        label: { type: 'string', default: null, description: 'Accessible label of the trigger; defaults to "Assignee".' },
        inline: { type: 'boolean', default: false, description: 'Render the panel absolutely inside the picker instead of on <body>: use it inside a native <dialog> (the top layer).' },
    },
    parts: {
        trigger: { description: 'The button; your attributes and class land here.' },
        placeholder: { description: 'The dashed + circle shown while nobody is assigned.' },
        name: { description: 'The assignee\'s name beside the avatar (showName).' },
        panel: { description: 'The dropdown with the search box and the people.' },
        search: { description: 'The search box.' },
        list: { description: 'The scrolling list of people.' },
        unassign: { description: 'The "Unassigned" row.' },
        mark: { description: 'The dashed × circle of the Unassigned row.' },
        option: { description: 'A person\'s row: their avatar and name.' },
        label: { description: 'A person\'s name in their row.' },
        empty: { description: '"No people found." when the search matches nobody.' },
    },
    states: {
        size: { attribute: 'data-size', description: 'On the placeholder: 32px at sm, 24px otherwise.' },
        inline: { attribute: 'data-inline', description: 'On the panel: positioned inside the picker.' },
    },
    events: {
        select: { description: 'A person (or Unassigned) was chosen.', blade: false, svelte: 'onselect', detail: 'string | number | null' },
    },
    uses: ['avatar', 'icon'],
    usage: {
        livewire: '<sigta:assignee-picker :user="$task->assignee" :members="$members" method="assignTask" :args="[$task->id]" wire:key="assignee-{{ $task->assignee?->id }}" />',
        svelte: '<AssigneePicker user={task.assignee} {members} onselect={(id) => assignTask(task.id, id)} />',
    },
    notes: {
        shared: [
            'Re-check membership on the server in whatever the choice calls; the list is filtered on the client.',
            'By default the panel is placed on <body> with fixed positioning, so it escapes a draggable card and a board\'s overflow clipping; pass inline inside a native <dialog>, where content placed on <body> paints behind the top layer.',
            'The search box and the list are only built while the panel is open, so many closed pickers stay cheap.',
        ],
        livewire: [
            'On select it calls method(...args, userId) on the nearest Livewire component ($wire), so one component serves board cards, subtasks and the task modal: method="assignTask" :args="[$task->id]" calls assignTask($taskId, $userId).',
            'Pass a value-keyed wire:key (by assignee id) so a reassign replaces the picker instead of leaving stale Alpine state; wire:key lands on the root, every other attribute on the trigger.',
            'With inline, Livewire\'s morph keeps the panel: a panel placed on <body> would be stripped on re-render inside a dialog.',
        ],
        svelte: ['onselect receives the chosen id (null for Unassigned); every other attribute lands on the trigger.'],
    },
    examples: [
        {
            label: 'Unassigned & assigned',
            tree: [el('div', { class: 'flex items-start gap-10' },
                el('div', {},
                    c('label', { class: 'mb-2 block' }, 'Unassigned'),
                    c('assignee-picker', { members: people }),
                ),
                el('div', {},
                    c('label', { class: 'mb-2 block' }, 'Assigned, with name'),
                    c('assignee-picker', { user: people[0], members: people, showName: true, size: 'sm' }),
                ),
            )],
        },
        {
            label: 'Inline positioning (for a native dialog\'s top layer)',
            tree: [el('div', { class: 'max-w-sm' }, c('assignee-picker', { inline: true, showName: true, members: people.slice(0, 2) }))],
        },
        {
            label: 'Opened',
            tree: [el('div', { class: 'max-w-sm' }, c('assignee-picker', { user: people[1], members: people, label: 'Owner' }))],
            interactions: [{ click: '[data-part="trigger"]' }],
        },
        {
            label: 'Searched',
            tree: [el('div', { class: 'max-w-sm' }, c('assignee-picker', { inline: true, members: people }))],
            interactions: [{ click: '[data-part="trigger"]' }, { fill: ['[data-part="search"]', 'al'] }],
        },
        {
            label: 'Nobody found',
            tree: [el('div', { class: 'max-w-sm' }, c('assignee-picker', { inline: true, showName: true, members: people }))],
            interactions: [{ click: '[data-part="trigger"]' }, { fill: ['[data-part="search"]', 'zzz'] }],
        },
        {
            label: 'Closed with Escape',
            tree: [el('div', { class: 'max-w-sm' }, c('assignee-picker', { inline: true, members: people }))],
            interactions: [{ click: '[data-part="trigger"]' }, { press: 'Escape' }],
        },
    ],
});
