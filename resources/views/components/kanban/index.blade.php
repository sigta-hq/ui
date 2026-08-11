{{--
    Kanban board — the x-data root that owns all drag state.

    Native HTML5 drag-and-drop: cards are draggable="true"; each column body is a
    drop target (dragover/drop). On drop the board dispatches a bubbling
    `sigta-kanban-move` CustomEvent with:

        detail = { card, from, to, position }

    …so a Livewire component can persist the move. Listen for it either on the
    board element or on window, e.g.

        <x-sigta::kanban x-on:sigta-kanban-move="$wire.moveCard($event.detail)"> … </x-sigta::kanban>

    or in a Livewire class:  #[On('sigta-kanban-move')] public function move($card, $from, $to, $position) { … }

    Drag approach (documented tradeoff): we do an *optimistic* DOM move during
    dragover — the dragged node is inserted before the card whose vertical
    midpoint the pointer has passed (classic "insert between cards" reduce over
    getBoundingClientRect). This gives real between-card placement without a
    library. It moves DOM nodes imperatively, which is safe here because the
    canonical order is owned server-side: after the dispatched event persists,
    Livewire's morph reconciles the board back to the authoritative order. In the
    playground (no backend) the optimistic move simply stands. We deliberately
    keep only transient drag state in Alpine (draggedId / overColumn) and never
    store list order in JS, so morphs stay clean.
--}}
<div
    data-sigta="kanban"
    x-data="{
        draggedId: null,
        fromColumn: null,
        overColumn: null,

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
    }"
    x-on:dragend="end()"
    {{ $attributes->merge(['class' => 'flex items-start gap-4 overflow-x-auto pb-3']) }}
>
    {{ $slot }}
</div>
