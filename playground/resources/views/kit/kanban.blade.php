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
</x-kit-shell>
