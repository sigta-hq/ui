import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'kanban.column',
    title: 'Kanban column',
    tier: 'components',
    page: 'kanban',
    svelte: 'Kanban.Column',
    description: 'One column of a kanban: a mono header (label, optional actions, a count badge pinned right) over a dashed drop zone of kanban.cards that inks while a card is dragged over it.',
    aria: 'The body is a list (role="list") named by the string aria (or label), so slotted header markup never leaks into the accessible name; a sortable column\'s grip is a button labelled "Reorder <name>".',
    props: {
        label: { type: 'string', default: null, description: 'The header label (mono, uppercase); also the body\'s accessible name unless aria is given.' },
        id: { type: 'string', default: null, description: 'Identifies the column in the move events (from, to, column); generated when not given.' },
        count: { type: 'number', default: null, description: 'The number in the count badge; defaults to the number of cards inside.' },
        aria: { type: 'string', default: null, description: 'The body\'s accessible name, when it should differ from the visible label (defaults to label).' },
        sortable: { type: 'boolean', default: false, description: 'Grows a grip handle at the start of the header that drags the column along the board (only the handle starts a column drag).' },
    },
    parts: {
        header: { description: 'The header row.' },
        handle: { description: 'The grip handle of a sortable column (also data-sigta="kanban.column-handle").' },
        label: { description: 'The visible label (when no heading slot is given).' },
        heading: { description: 'Wraps the heading slot in place of the label.' },
        end: { description: 'The actions and the count badge, pinned to the right of the header.' },
        body: { description: 'The drop zone and list of cards (data-kanban-body, data-column-id).' },
    },
    states: {
        over: { attribute: 'data-over', description: 'On the body while a card is dragged over it.', conformance: false },
        dragging: { attribute: 'data-col-dragging', description: 'On a sortable column while it is dragged.', conformance: false },
    },
    slots: {
        default: { description: 'The kanban.cards.' },
        heading: { description: 'Replaces the visible label with your own control (e.g. an inline-rename input); the count and actions stay put.' },
        actions: { description: 'Header controls (a menu, a delete button) to the left of the count, which stays pinned to the right.' },
    },
    uses: ['badge', 'icon', 'kanban.card'],
    usage: {
        livewire: `<sigta:kanban.column id="todo" label="To do" sortable>
    <x-slot:actions>
        <sigta:button variant="ghost" size="sm" aria-label="Column menu"><sigta:icon name="ellipsis-vertical" size="sm" /></sigta:button>
    </x-slot:actions>
    <sigta:kanban.card id="plumb">Plumb — one-number widget</sigta:kanban.card>
</sigta:kanban.column>`,
        svelte: `<Kanban.Column id="todo" label="To do" sortable>
    {#snippet actions()}
        <Button variant="ghost" size="sm" aria-label="Column menu"><Icon name="ellipsis-vertical" size="sm" /></Button>
    {/snippet}
    <Kanban.Card id="plumb">Plumb — one-number widget</Kanban.Card>
</Kanban.Column>`,
    },
    notes: {
        shared: [
            'Use it inside a kanban. The body\'s aria-label comes from the string label / aria, never from the heading slot.',
        ],
        livewire: ['Without count, the badge counts the kanban.cards rendered in the slot.'],
        svelte: ['Without count, the badge counts the Kanban.Cards inside (the server render included). Passing count={column.cards.length} is just as good.'],
    },
    examples: [
        {
            label: 'Column header actions (menu beside the count)',
            tree: [c('kanban', {},
                c('kanban.column', { id: 'sprint', label: 'Sprint' },
                    {
                        actions: [c('dropdown', { position: 'bottom-end' },
                            { trigger: [c('button', { variant: 'ghost', size: 'sm', 'aria-label': 'Column menu' }, c('icon', { name: 'ellipsis-vertical', size: 'sm' }))] },
                            c('dropdown.item', { icon: 'pencil' }, 'Rename column'),
                            c('dropdown.item', { icon: 'trash-2', variant: 'danger' }, 'Delete column'),
                        )],
                    },
                    c('kanban.card', { id: 's1' }, 'Ship the actions slot'),
                ),
            )],
        },
        {
            label: 'Editable column name (heading slot)',
            conformance: 'browser',
            blade: `<x-sigta::kanban>
    <x-sigta::kanban.column id="rename" label="Backlog">
        <x-slot:heading>
            <div x-data="{ editing: false }" class="w-full">
                <button
                    type="button"
                    x-show="! editing"
                    x-on:click="editing = true; $nextTick(() => $refs.rename.focus())"
                    class="w-full truncate text-left font-mono text-[0.7rem] uppercase tracking-[0.14em] text-graphite hover:text-ink"
                >Backlog</button>
                <input
                    x-ref="rename"
                    x-show="editing"
                    x-cloak
                    x-on:blur="editing = false"
                    x-on:keydown.enter="editing = false"
                    x-on:keydown.escape="editing = false"
                    type="text"
                    value="Backlog"
                    aria-label="Rename column"
                    class="w-full min-w-0 rounded-base border border-ink bg-surface-raised px-2 py-1 font-mono text-[0.7rem] uppercase tracking-[0.14em] text-ink outline-none"
                />
            </div>
        </x-slot:heading>
        <x-sigta::kanban.card id="rename-card">Click the name to rename it</x-sigta::kanban.card>
    </x-sigta::kanban.column>
</x-sigta::kanban>`,
            svelte: `<script>
    import { tick } from 'svelte';
    import { Kanban } from '@sigta-hq/svelte';

    let editing = $state(false);
    let input;
</script>

<Kanban>
    <Kanban.Column id="rename" label="Backlog">
        {#snippet heading()}
            <div class="w-full">
                <button
                    type="button"
                    hidden={editing}
                    onclick={async () => { editing = true; await tick(); input.focus(); }}
                    class="w-full truncate text-left font-mono text-[0.7rem] uppercase tracking-[0.14em] text-graphite hover:text-ink"
                >Backlog</button>
                <input
                    bind:this={input}
                    hidden={!editing}
                    onblur={() => (editing = false)}
                    onkeydown={(event) => { if (event.key === 'Enter' || event.key === 'Escape') editing = false; }}
                    type="text"
                    value="Backlog"
                    aria-label="Rename column"
                    class="w-full min-w-0 rounded-base border border-ink bg-surface-raised px-2 py-1 font-mono text-[0.7rem] uppercase tracking-[0.14em] text-ink outline-none"
                />
            </div>
        {/snippet}
        <Kanban.Card id="rename-card">Click the name to rename it</Kanban.Card>
    </Kanban.Column>
</Kanban>`,
            interactions: [{ click: '[data-part="heading"] button' }],
        },
        {
            label: 'Body name from aria, explicit count',
            tree: [c('kanban', {},
                c('kanban.column', { id: 'wip', label: 'WIP', aria: 'Work in progress', count: 12 },
                    c('kanban.card', { id: 'w1' }, 'Twelve in flight; one shown'),
                ),
            )],
        },
    ],
});
