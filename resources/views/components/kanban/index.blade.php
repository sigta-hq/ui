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
--}}
<div
    data-sigta="kanban"
    x-data="{
        draggedId: null,
        fromColumn: null,
        overColumn: null,
        draggedColumn: null,

        draggedEl() {
            if (! this.draggedId) return null;
            return this.$root.querySelector(`[data-card-id='${CSS.escape(this.draggedId)}']`);
        },

        start(id, event) {
            this.draggedId = id;
            const body = event.target.closest('[data-kanban-body]');
            this.fromColumn = body ? body.dataset.columnId : null;
            if (event.dataTransfer) {
                event.dataTransfer.effectAllowed = 'move';
                try { event.dataTransfer.setData('text/plain', id); } catch (e) {}
            }
        },

        end() {
            this.draggedId = null;
            this.fromColumn = null;
            this.overColumn = null;
        },

        insertBefore(body, y) {
            const cards = Array.from(body.querySelectorAll('[data-card-id]:not([data-dragging])'));
            return cards.reduce((closest, card) => {
                const box = card.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                return (offset < 0 && offset > closest.offset) ? { offset, el: card } : closest;
            }, { offset: Number.NEGATIVE_INFINITY, el: null }).el;
        },

        over(event, columnId) {
            if (! this.draggedId) return;
            if (event.dataTransfer) event.dataTransfer.dropEffect = 'move';
            this.overColumn = columnId;
            const body = event.currentTarget;
            const dragged = this.draggedEl();
            if (! dragged) return;
            const ref = this.insertBefore(body, event.clientY);
            ref ? body.insertBefore(dragged, ref) : body.appendChild(dragged);
        },

        drop(event, columnId) {
            const body = event.currentTarget;
            const dragged = this.draggedEl();
            this.overColumn = null;
            if (! dragged) return;
            const position = Array.from(body.querySelectorAll('[data-card-id]')).indexOf(dragged);
            this.$dispatch('sigta-kanban-move', {
                card: this.draggedId,
                from: this.fromColumn,
                to: columnId,
                position,
            });
            this.end();
        },

        columnEl() {
            if (! this.draggedColumn) return null;
            return this.$root.querySelector(`[data-sigta='kanban.column'][data-column='${CSS.escape(this.draggedColumn)}']`);
        },

        columnUnit(el) {
            while (el && el.parentNode !== this.$root) el = el.parentNode;
            return el;
        },

        columnUnits() {
            return Array.from(this.$root.children).filter(
                (c) => c.matches(`[data-sigta='kanban.column']`) || c.querySelector(`[data-sigta='kanban.column']`)
            );
        },

        columnStart(id, event) {
            this.draggedColumn = id;
            if (event.dataTransfer) {
                event.dataTransfer.effectAllowed = 'move';
                try { event.dataTransfer.setData('text/plain', id); } catch (e) {}
                const col = this.columnEl();
                if (col) { try { event.dataTransfer.setDragImage(col, 24, 24); } catch (e) {} }
            }
            event.stopPropagation();
        },

        columnInsertBefore(x, dragged) {
            const units = this.columnUnits().filter((u) => u !== dragged);
            return units.reduce((closest, unit) => {
                const box = unit.getBoundingClientRect();
                const offset = x - box.left - box.width / 2;
                return (offset < 0 && offset > closest.offset) ? { offset, el: unit } : closest;
            }, { offset: Number.NEGATIVE_INFINITY, el: null }).el;
        },

        columnOver(event) {
            if (! this.draggedColumn) return;
            event.preventDefault();
            if (event.dataTransfer) event.dataTransfer.dropEffect = 'move';
            const col = this.columnEl();
            if (! col) return;
            const dragged = this.columnUnit(col);
            const ref = this.columnInsertBefore(event.clientX, dragged);
            if (ref) {
                if (ref !== dragged) this.$root.insertBefore(dragged, ref);
            } else {
                const units = this.columnUnits();
                const last = units[units.length - 1];
                if (last && last !== dragged) this.$root.insertBefore(dragged, last.nextSibling);
            }
        },

        columnDrop(event) {
            if (! this.draggedColumn) return;
            event.preventDefault();
            const id = this.draggedColumn;
            const col = this.columnEl();
            const dragged = col ? this.columnUnit(col) : null;
            const position = dragged ? this.columnUnits().indexOf(dragged) : 0;
            this.columnEnd();
            this.$dispatch('sigta-kanban-column-move', { column: id, position });
        },

        columnEnd() {
            this.draggedColumn = null;
        },
    }"
    x-on:dragend="end(); columnEnd()"
    x-on:dragover="columnOver($event)"
    x-on:drop="columnDrop($event)"
    {{ $attributes->merge(['class' => 'flex items-start gap-4 overflow-x-auto pb-3']) }}
>
    {{ $slot }}
</div>
