<x-kit-shell title="Kanban">
    <x-kit-example label="Board">
    «p class="mb-5 max-w-[60ch] text-graphite">
        Drag a card between columns. On drop the board dispatches
        «code class="font-mono text-[0.85em]">sigta-kanban-move«/code> with
        «code class="font-mono text-[0.85em]">{ card, from, to, position }«/code> so Livewire can persist the move.
    «/p>

    «sigta:kanban>
        «sigta:kanban.column id="todo" label="To do">
            «sigta:kanban.card id="marginalia">Marginalia v3 — margin search«/sigta:kanban.card>
            «sigta:kanban.card id="plumb">Plumb — one-number widget«/sigta:kanban.card>
            «sigta:kanban.card id="almanac">Almanac — changelog polish«/sigta:kanban.card>
        «/sigta:kanban.column>

        «sigta:kanban.column id="doing" label="Doing">
            «sigta:kanban.card id="halyard">Halyard OS — routing console«/sigta:kanban.card>
            «sigta:kanban.card id="ledger">Ledger rebuild — budget engine«/sigta:kanban.card>
        «/sigta:kanban.column>

        «sigta:kanban.column id="done" label="Done">
            «sigta:kanban.card id="brand">Marginalia — brand refresh«/sigta:kanban.card>
        «/sigta:kanban.column>
    «/sigta:kanban>
    </x-kit-example>

    <x-kit-example label="Empty column">
    «sigta:kanban>
        «sigta:kanban.column id="backlog" label="Backlog">
            «sigta:kanban.card id="idea">Draft the Q3 spotlight«/sigta:kanban.card>
        «/sigta:kanban.column>
        «sigta:kanban.column id="review" label="In review" />
    «/sigta:kanban>
    </x-kit-example>

    <x-kit-example label="Column header actions (menu beside the count)">
    «p class="mb-5 max-w-[60ch] text-graphite">
        Pass an «code class="font-mono text-[0.85em]">actions«/code> slot for a column menu next to the count.
        The body's accessible name comes from the string «code class="font-mono text-[0.85em]">label«/code>
        (or «code class="font-mono text-[0.85em]">aria«/code>), so slotted markup never leaks into it.
    «/p>

    «sigta:kanban>
        «sigta:kanban.column id="sprint" label="Sprint">
            «x-slot:actions>
                «sigta:dropdown position="bottom-end">
                    «x-slot:trigger>
                        «sigta:button variant="ghost" size="sm" aria-label="Column menu">
                            «sigta:icon name="ellipsis-vertical" size="sm" />
                        «/sigta:button>
                    «/x-slot:trigger>
                    «sigta:dropdown.item icon="pencil">Rename column«/sigta:dropdown.item>
                    «sigta:dropdown.item icon="trash-2" variant="danger">Delete column«/sigta:dropdown.item>
                «/sigta:dropdown>
            «/x-slot:actions>
            «sigta:kanban.card id="s1">Ship the actions slot«/sigta:kanban.card>
        «/sigta:kanban.column>
    «/sigta:kanban>
    </x-kit-example>

    <x-kit-example label="Editable column name (heading slot)">
    «p class="mb-5 max-w-[60ch] text-graphite">
        Pass a «code class="font-mono text-[0.85em]">heading«/code> slot to replace the visible label with your
        own control. Here the name is plain text at rest and only turns into an input on click; the count and
        «code class="font-mono text-[0.85em]">actions«/code> stay put, and the body's accessible name still
        comes from «code class="font-mono text-[0.85em]">label«/code> (or «code class="font-mono text-[0.85em]">aria«/code>).
    «/p>

    «sigta:kanban>
        «sigta:kanban.column id="rename" label="Backlog">
            «x-slot:heading>
                «div x-data="{ editing: false }" class="w-full">
                    «button
                        type="button"
                        x-show="! editing"
                        x-on:click="editing = true; $nextTick(() => $refs.rename.focus())"
                        class="w-full truncate text-left font-mono text-[0.7rem] uppercase tracking-[0.14em] text-graphite hover:text-ink"
                    >Backlog«/button>
                    «input
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
                «/div>
            «/x-slot:heading>
            «sigta:kanban.card id="rename-card">Click the name to rename it«/sigta:kanban.card>
        «/sigta:kanban.column>
    «/sigta:kanban>
    </x-kit-example>
</x-kit-shell>
