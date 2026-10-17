/* ---------------------------------------------------------------------------
   Kanban — the Svelte twin of the Livewire runtime's sigtaKanban. The board
   owns the transient drag state and shares it through context; columns and
   cards read it for their dragging and drop-target states. Where a card or
   column lands comes from the core (kanban.ts), as in the Livewire runtime.

   While dragging, the dragged element moves in the DOM (the optimistic move,
   so it shows where it will land). On drop it is first put back where Svelte
   rendered it, then the move is reported (onmove / oncolumnmove and the
   sigta-kanban-move / sigta-kanban-column-move DOM events): a page that
   moves the item in its own lists re-renders it from a clean DOM. When
   nothing re-renders it (a static board), the optimistic move stands, as it
   does in the Livewire runtime without a server.
--------------------------------------------------------------------------- */
import { getContext, setContext, tick } from 'svelte';
import {
    cardElement,
    cardPosition,
    columnElement,
    columnPosition,
    columnUnit,
    KANBAN_COLUMN_MOVE_EVENT,
    KANBAN_MOVE_EVENT,
    placeCard,
    placeColumn,
    type KanbanColumnMove,
    type KanbanMove,
} from '@sigta-hq/core';

export interface KanbanCallbacks {
    onmove?: (move: KanbanMove) => void;
    oncolumnmove?: (move: KanbanColumnMove) => void;
}

/** Where an element sat before the drag, by its neighbours. */
interface Spot {
    parent: Node;
    previous: Node | null;
    next: Node | null;
}

const spotOf = (el: Node): Spot | null => (el.parentNode ? { parent: el.parentNode, previous: el.previousSibling, next: el.nextSibling } : null);
const isAt = (el: Node, spot: Spot) => el.parentNode === spot.parent && el.previousSibling === spot.previous && el.nextSibling === spot.next;

const KEY = Symbol('sigta-kanban');

export class KanbanBoard {
    draggedId: string | null = $state(null);
    fromColumn: string | null = $state(null);
    overColumn: string | null = $state(null);
    draggedColumn: string | null = $state(null);
    /** The board's root element (not state: only read inside handlers). */
    root: HTMLElement | null = null;
    #callbacks: () => KanbanCallbacks;
    #origin: Spot | null = null;

    constructor(callbacks: () => KanbanCallbacks) {
        this.#callbacks = callbacks;
        setContext(KEY, this);
    }

    #emit(from: EventTarget, name: string, detail: KanbanMove | KanbanColumnMove): void {
        from.dispatchEvent(new CustomEvent(name, { detail, bubbles: true, composed: true, cancelable: true }));
    }

    /**
     * Puts a dropped element back where Svelte rendered it, reports the move,
     * and once the page has re-rendered, restores the optimistic placement if
     * nothing touched it.
     */
    async #settle(el: HTMLElement, origin: Spot | null, report: () => void): Promise<void> {
        const placed = spotOf(el);
        const key = el.dataset.cardId ?? el.dataset.column;
        if (origin && placed) origin.parent.insertBefore(el, origin.next);
        report();
        await tick();
        const untouched = origin && placed && el.isConnected && isAt(el, origin) && (el.dataset.cardId ?? el.dataset.column) === key;
        if (untouched) placed.parent.insertBefore(el, placed.next && placed.next.parentNode === placed.parent ? placed.next : null);
    }

    /* -------- cards ---------------------------------------------------------- */

    draggedEl(): HTMLElement | null {
        return this.root ? cardElement(this.root, this.draggedId) : null;
    }

    start(id: string, event: DragEvent): void {
        this.draggedId = id;
        const target = event.target as Element | null;
        const body = target?.closest<HTMLElement>('[data-kanban-body]');
        this.fromColumn = body ? (body.dataset.columnId ?? null) : null;
        const card = this.draggedEl();
        this.#origin = card ? spotOf(card) : null;
        if (event.dataTransfer) {
            event.dataTransfer.effectAllowed = 'move';
            try { event.dataTransfer.setData('text/plain', id); } catch { /* some browsers refuse during dragstart */ }
        }
    }

    end(): void {
        this.draggedId = null;
        this.fromColumn = null;
        this.overColumn = null;
    }

    over(event: DragEvent, columnId: string): void {
        if (!this.draggedId) return;
        if (event.dataTransfer) event.dataTransfer.dropEffect = 'move';
        this.overColumn = columnId;
        const dragged = this.draggedEl();
        if (dragged) placeCard(event.currentTarget as HTMLElement, dragged, event.clientY);
    }

    drop(event: DragEvent, columnId: string): void {
        const body = event.currentTarget as HTMLElement;
        const dragged = this.draggedEl();
        this.overColumn = null;
        if (!dragged || !this.draggedId) return;
        const detail: KanbanMove = { card: this.draggedId, from: this.fromColumn, to: columnId, position: cardPosition(body, dragged) };
        const origin = this.#origin;
        this.#origin = null;
        this.end();
        void this.#settle(dragged, origin, () => {
            this.#callbacks().onmove?.(detail);
            this.#emit(body, KANBAN_MOVE_EVENT, detail);
        });
    }

    /* -------- columns -------------------------------------------------------- */

    columnEl(): HTMLElement | null {
        return this.root ? columnElement(this.root, this.draggedColumn) : null;
    }

    columnStart(id: string, event: DragEvent): void {
        this.draggedColumn = id;
        const col = this.columnEl();
        const unit = col && this.root ? columnUnit(this.root, col) : null;
        this.#origin = unit ? spotOf(unit) : null;
        if (event.dataTransfer) {
            event.dataTransfer.effectAllowed = 'move';
            try { event.dataTransfer.setData('text/plain', id); } catch { /* some browsers refuse during dragstart */ }
            if (col) { try { event.dataTransfer.setDragImage(col, 24, 24); } catch { /* no drag image then */ } }
        }
        event.stopPropagation();
    }

    columnOver(event: DragEvent): void {
        if (!this.draggedColumn || !this.root) return;
        event.preventDefault();
        if (event.dataTransfer) event.dataTransfer.dropEffect = 'move';
        const col = this.columnEl();
        const unit = col ? columnUnit(this.root, col) : null;
        if (unit) placeColumn(this.root, unit, event.clientX);
    }

    columnDrop(event: DragEvent): void {
        if (!this.draggedColumn || !this.root) return;
        event.preventDefault();
        const root = this.root;
        const id = this.draggedColumn;
        const col = this.columnEl();
        const unit = col ? columnUnit(root, col) : null;
        const detail: KanbanColumnMove = { column: id, position: columnPosition(root, unit) };
        const origin = this.#origin;
        this.#origin = null;
        this.columnEnd();
        const report = () => {
            this.#callbacks().oncolumnmove?.(detail);
            this.#emit(root, KANBAN_COLUMN_MOVE_EVENT, detail);
        };
        if (unit) void this.#settle(unit, origin, report);
        else report();
    }

    columnEnd(): void {
        this.draggedColumn = null;
    }
}

export const useKanban = (): KanbanBoard | undefined => getContext(KEY);

/* -------- counting a column's cards ------------------------------------------ */
// A column without a count shows how many cards it holds. The cards are its
// children, which render after the header, so the column renders them once
// more beforehand, inside an inert <template>, where each card only counts
// itself: the number is right in the server render too.

export interface CardCounter {
    n: number;
}

const COUNT = Symbol('sigta-kanban-count');

export const setCardCounter = (counter: CardCounter): CardCounter => setContext(COUNT, counter);
export const useCardCounter = (): CardCounter | undefined => getContext(COUNT);
