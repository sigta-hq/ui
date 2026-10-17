{{--
    Kanban board — the x-data root that owns all drag state.

    Native HTML5 drag-and-drop, in two flavours that never touch Livewire's
    state tracking (so morphs stay clean and no request is ever rejected):

    1. Cards. Cards are draggable="true"; each column body is a drop target.
       On drop the board dispatches `sigta-kanban-move` with
       detail = { card, from, to, position }.

    2. Columns (opt-in). Give a column the `sortable` prop and it grows a drag
       handle in its header. Dragging the handle reorders the columns; on drop
       the board dispatches `sigta-kanban-column-move` with
       detail = { column, position }.

    Listen for either on the board element or window, e.g.

        <x-sigta::kanban
            x-on:sigta-kanban-move="$wire.moveCard($event.detail)"
            x-on:sigta-kanban-column-move="$wire.moveColumn($event.detail)"> … </x-sigta::kanban>

    Drag approach (documented tradeoff): we do an *optimistic* DOM move during
    dragover — the dragged node is inserted before the sibling whose midpoint the
    pointer has passed (classic reduce over getBoundingClientRect), vertical for
    cards and horizontal for columns. This gives real between-item placement
    without a library. It moves DOM nodes imperatively, which is safe here because
    the canonical order is owned server-side: after the dispatched event persists,
    Livewire's morph reconciles the board back to the authoritative order. In the
    playground (no backend) the optimistic move simply stands. We deliberately
    keep only transient drag state in Alpine (never the list order) so morphs stay
    clean — this is exactly why it does not hit the payload-corruption failures a
    Livewire-directive sorter (wire:sort) runs into on a keyed, model-bound board.

    Column reordering works whether or not the consumer wraps each column in an
    outer element (e.g. to carry a wire:key): the reorder unit is resolved as the
    board's direct child that is, or contains, the dragged column.

    The drag state lives in the runtime's sigtaKanban (resources/js/sigta.js);
    where a card or column lands comes from @sigta-hq/core (kanban.ts), shared
    with the Svelte board. The look is the contract's kanban.css.
--}}
<div
    data-sigta="kanban"
    x-data="sigtaKanban()"
    x-on:dragend="end(); columnEnd()"
    x-on:dragover="columnOver($event)"
    x-on:drop="columnDrop($event)"
    {{ $attributes }}
>
    {{ $slot }}
</div>
