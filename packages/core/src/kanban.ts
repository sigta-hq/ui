/* ---------------------------------------------------------------------------
   Kanban: where a dragged card or column lands. Both runtimes' boards
   (Alpine's sigtaKanban, the Svelte Kanban) move the dragged element while
   it is dragged — the optimistic move, so it shows where it will land — and
   read its position back from the DOM on drop, with these. Cards are placed
   vertically within a column body, columns horizontally within the board.
   SSR-safe: nothing runs at import time.
--------------------------------------------------------------------------- */

/** An item's extent along the axis it is sorted on: top/height, or left/width. */
export interface Span {
    start: number;
    size: number;
}

/**
 * The item a pointer at `pointer` drops before: of the items whose middle
 * lies past the pointer, the nearest one. -1 drops after the last item.
 */
export function dropBefore(spans: readonly Span[], pointer: number): number {
    let best = -1;
    let closest = Number.NEGATIVE_INFINITY;
    spans.forEach((span, index) => {
        const offset = pointer - span.start - span.size / 2;
        if (offset < 0 && offset > closest) {
            closest = offset;
            best = index;
        }
    });
    return best;
}

const vertical = (el: Element): Span => {
    const box = el.getBoundingClientRect();
    return { start: box.top, size: box.height };
};

// An id inside a quoted attribute selector ('…').
const escape = (id: string): string =>
    typeof CSS !== 'undefined' && CSS.escape ? CSS.escape(id) : id.replace(/['\\\n]/g, (ch) => `\\${ch === '\n' ? 'a ' : ch}`);

const horizontal = (el: Element): Span => {
    const box = el.getBoundingClientRect();
    return { start: box.left, size: box.width };
};

/* -------- cards ------------------------------------------------------------- */

/** A card, by its id, inside the board (or null). */
export function cardElement(board: ParentNode, id: string | null): HTMLElement | null {
    if (!id) return null;
    return board.querySelector<HTMLElement>(`[data-card-id='${escape(id)}']`);
}

/**
 * Moves the dragged card within a column body to the pointer's height
 * (clientY): before the first card whose middle is below it, else last.
 */
export function placeCard(body: HTMLElement, card: HTMLElement, y: number): void {
    const cards = Array.from(body.querySelectorAll<HTMLElement>('[data-card-id]:not([data-dragging])'));
    const index = dropBefore(cards.map(vertical), y);
    if (index === -1) body.appendChild(card);
    else body.insertBefore(card, cards[index]);
}

/** The card's place among its column's cards: the position a move reports. */
export function cardPosition(body: ParentNode, card: Element): number {
    return Array.from(body.querySelectorAll('[data-card-id]')).indexOf(card);
}

/* -------- columns ----------------------------------------------------------- */

/** A column, by its id, inside the board (or null). */
export function columnElement(board: ParentNode, id: string | null): HTMLElement | null {
    if (!id) return null;
    return board.querySelector<HTMLElement>(`[data-sigta='kanban.column'][data-column='${escape(id)}']`);
}

/**
 * What moves when a column is reordered: the board's direct child that is,
 * or wraps, the column — so a consumer's wrapper (carrying a wire:key, say)
 * moves with it.
 */
export function columnUnit(board: Node, el: Node | null): HTMLElement | null {
    while (el && el.parentNode !== board) el = el.parentNode;
    return el as HTMLElement | null;
}

/** The board's reorder units, in order. */
export function columnUnits(board: HTMLElement): HTMLElement[] {
    return (Array.from(board.children) as HTMLElement[]).filter(
        (child) => child.matches(`[data-sigta='kanban.column']`) || child.querySelector(`[data-sigta='kanban.column']`) !== null,
    );
}

/**
 * Moves a column's unit along the board to the pointer (clientX): before the
 * first other column whose middle is right of it, else after the last.
 */
export function placeColumn(board: HTMLElement, unit: HTMLElement, x: number): void {
    const others = columnUnits(board).filter((other) => other !== unit);
    const index = dropBefore(others.map(horizontal), x);
    if (index !== -1) {
        board.insertBefore(unit, others[index]);
        return;
    }
    const units = columnUnits(board);
    const last = units[units.length - 1];
    if (last && last !== unit) board.insertBefore(unit, last.nextSibling);
}

/** The unit's place among the board's columns: the position a column move reports. */
export function columnPosition(board: HTMLElement, unit: HTMLElement | null): number {
    return unit ? columnUnits(board).indexOf(unit) : 0;
}

/* -------- the move events --------------------------------------------------- */

export interface KanbanMove {
    card: string;
    from: string | null;
    to: string;
    position: number;
}

export interface KanbanColumnMove {
    column: string;
    position: number;
}

export const KANBAN_MOVE_EVENT = 'sigta-kanban-move';
export const KANBAN_COLUMN_MOVE_EVENT = 'sigta-kanban-column-move';
