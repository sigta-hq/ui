import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

// Drags a card or a column the way a pointer would: dragstart on what is
// dragged, dragover and drop where it lands, dragend. The drop's detail is
// written into the page, so both runtimes are compared on what they report.
const drag = (source: string, target: string, at: 'top' | 'bottom' | 'left', event: string) => `(async () => {
    const pause = () => new Promise((resolve) => setTimeout(resolve, 60));
    const fixture = document.getElementById('fixture');
    window.addEventListener('${event}', (e) => {
        const out = document.createElement('output');
        out.textContent = JSON.stringify(e.detail);
        fixture.append(out);
    }, { once: true });
    const source = document.querySelector('${source}');
    const target = document.querySelector('${target}');
    const dataTransfer = new DataTransfer();
    source.dispatchEvent(new DragEvent('dragstart', { bubbles: true, cancelable: true, dataTransfer }));
    await pause();
    const box = target.getBoundingClientRect();
    const point = ${at === 'top' ? '{ clientX: box.left + 8, clientY: box.top + 4 }' : at === 'bottom' ? '{ clientX: box.left + 8, clientY: box.bottom - 4 }' : '{ clientX: box.left - 4, clientY: box.top + 8 }'};
    target.dispatchEvent(new DragEvent('dragover', { bubbles: true, cancelable: true, dataTransfer, ...point }));
    await pause();
    target.dispatchEvent(new DragEvent('drop', { bubbles: true, cancelable: true, dataTransfer, ...point }));
    source.dispatchEvent(new DragEvent('dragend', { bubbles: true, cancelable: true, dataTransfer }));
})()`;

const roadmap = () => c('kanban', {},
    c('kanban.column', { id: 'todo', label: 'To do' },
        c('kanban.card', { id: 'marginalia' }, 'Marginalia v3 — margin search'),
        c('kanban.card', { id: 'plumb' }, 'Plumb — one-number widget'),
        c('kanban.card', { id: 'almanac' }, 'Almanac — changelog polish'),
    ),
    c('kanban.column', { id: 'doing', label: 'Doing' },
        c('kanban.card', { id: 'halyard' }, 'Halyard OS — routing console'),
        c('kanban.card', { id: 'ledger' }, 'Ledger rebuild — budget engine'),
    ),
    c('kanban.column', { id: 'done', label: 'Done' },
        c('kanban.card', { id: 'brand' }, 'Marginalia — brand refresh'),
    ),
);

export default defineComponent({
    name: 'kanban',
    title: 'Kanban',
    tier: 'components',
    page: 'kanban',
    svelte: 'Kanban',
    description: 'Native HTML5 drag-and-drop board of kanban.columns holding kanban.cards. Dragging a card between (or within) columns reports { card, from, to, position }; sortable columns report { column, position } when dragged along the board.',
    aria: 'Columns are labelled lists (role="list") of focusable cards (role="listitem", aria-roledescription="draggable"); a sortable column\'s grip handle is a labelled button. A keyboard move menu is out of scope.',
    props: {},
    slots: { default: { description: 'The kanban.columns (optionally each in a wrapper of your own).', required: true } },
    events: {
        move: {
            description: 'A card was dropped: its id, the column it came from, the column it landed in and its 0-based position among that column\'s cards. The DOM event bubbles to the board and window.',
            blade: 'sigta-kanban-move',
            svelte: 'onmove',
            detail: '{ card: string, from: string, to: string, position: number }',
        },
        columnMove: {
            description: 'A sortable column was dropped: its id and its 0-based position among the board\'s columns. The DOM event bubbles to the board and window.',
            blade: 'sigta-kanban-column-move',
            svelte: 'oncolumnmove',
            detail: '{ column: string, position: number }',
        },
    },
    uses: ['kanban.column', 'kanban.card'],
    usage: {
        livewire: `<sigta:kanban
    x-on:sigta-kanban-move="$wire.moveCard($event.detail)"
    x-on:sigta-kanban-column-move="$wire.moveColumn($event.detail)">
    @foreach ($columns as $column)
        <sigta:kanban.column :id="$column->key" :label="$column->name" sortable wire:key="column-{{ $column->key }}">
            @foreach ($column->cards as $card)
                <sigta:kanban.card :id="$card->key" wire:key="card-{{ $card->key }}">{{ $card->title }}</sigta:kanban.card>
            @endforeach
        </sigta:kanban.column>
    @endforeach
</sigta:kanban>`,
        svelte: `<script>
    import { Kanban } from '@sigta-hq/svelte';

    let columns = $state([
        { id: 'todo', label: 'To do', cards: [{ id: 'plumb', title: 'Plumb — one-number widget' }] },
        { id: 'doing', label: 'Doing', cards: [] },
    ]);

    function move({ card, from, to, position }) {
        const source = columns.find((column) => column.id === from).cards;
        const [item] = source.splice(source.findIndex((c) => c.id === card), 1);
        columns.find((column) => column.id === to).cards.splice(position, 0, item);
    }

    function moveColumn({ column, position }) {
        const [item] = columns.splice(columns.findIndex((c) => c.id === column), 1);
        columns.splice(position, 0, item);
    }
</script>

<Kanban onmove={move} oncolumnmove={moveColumn}>
    {#each columns as column (column.id)}
        <Kanban.Column id={column.id} label={column.label} sortable>
            {#each column.cards as card (card.id)}
                <Kanban.Card id={card.id}>{card.title}</Kanban.Card>
            {/each}
        </Kanban.Column>
    {/each}
</Kanban>`,
    },
    notes: {
        shared: [
            'Native drag and drop, no library. While dragging, the card moves in the page to where it will land (before the first card whose middle is below the pointer; columns by the pointer passing a column\'s middle), and the drop reports it.',
            'The board never keeps the order: persist the move and render the new order from your data.',
        ],
        livewire: [
            'Listen for sigta-kanban-move / sigta-kanban-column-move on the board (x-on:…) or on window, and persist in your component; its next morph reconciles the board to the order it renders. Without a backend the optimistic move stands.',
            'Only transient drag state lives in Alpine (the runtime\'s sigtaKanban, so @sigtaScripts is required), never the list order, so morphs stay clean on a keyed, model-bound board: give columns and cards wire:key (a column may sit in a wrapper of your own that carries it).',
        ],
        svelte: [
            'onmove / oncolumnmove carry the same detail as the DOM events, which the board dispatches too. Render columns and cards with keyed {#each} blocks from your lists and move the item in the callback: the dropped element is put back first, so your lists re-render from a clean DOM. If the callback leaves your lists as they are, the optimistic move stands.',
        ],
    },
    examples: [
        {
            label: 'Board',
            tree: [el('div', { class: 'max-w-5xl' }, roadmap())],
        },
        {
            label: 'Empty column',
            tree: [c('kanban', {},
                c('kanban.column', { id: 'backlog', label: 'Backlog' },
                    c('kanban.card', { id: 'idea' }, 'Draft the Q3 spotlight'),
                ),
                c('kanban.column', { id: 'review', label: 'In review' }),
            )],
        },
        {
            label: 'Drag a card to another column',
            tree: [el('div', { class: 'max-w-5xl' }, roadmap())],
            interactions: [{ script: drag('[data-card-id="plumb"]', '[data-column-id="doing"]', 'top', 'sigta-kanban-move'), wait: 500 }],
        },
        {
            label: 'Drag a card down its own column',
            tree: [el('div', { class: 'max-w-5xl' }, roadmap())],
            interactions: [{ script: drag('[data-card-id="marginalia"]', '[data-column-id="todo"]', 'bottom', 'sigta-kanban-move'), wait: 500 }],
        },
        {
            label: 'Sortable columns: drag one to the front',
            tree: [c('kanban', {},
                c('kanban.column', { id: 'now', label: 'Now', sortable: true },
                    c('kanban.card', { id: 'n1' }, 'Invoice run'),
                ),
                c('kanban.column', { id: 'next', label: 'Next', sortable: true },
                    c('kanban.card', { id: 'x1' }, 'Quarterly review'),
                ),
                c('kanban.column', { id: 'later', label: 'Later', sortable: true }),
            )],
            interactions: [{ script: drag('[data-column="later"] [data-part="handle"]', '[data-column="now"]', 'left', 'sigta-kanban-column-move'), wait: 500 }],
        },
    ],
});
