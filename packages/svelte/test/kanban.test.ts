// The Svelte board against a page that keeps its own lists: a drop reports
// the move (callback and DOM event), the dropped element goes back where
// Svelte rendered it, and the page's lists re-render the board cleanly. A
// page that keeps nothing (a static board) keeps the optimistic move.
import { afterEach, describe, expect, it } from 'vitest';
import { flushSync, mount, tick, unmount } from 'svelte';
import KanbanData from './fixtures/KanbanData.svelte';

let app: Record<string, unknown> | null = null;
afterEach(() => {
    if (app) unmount(app);
    app = null;
    document.body.innerHTML = '';
});

function board(props: { keyed?: boolean; persist?: boolean } = {}) {
    const target = document.createElement('div');
    document.body.append(target);
    app = mount(KanbanData, { target, props });
    flushSync();
    return target;
}

// jsdom has no layout: every box is empty, so a pointer above 0 lands
// before the first card or column, anything else after the last.
function fire(el: Element, type: string, point: { clientX?: number; clientY?: number } = {}) {
    const event = new Event(type, { bubbles: true, cancelable: true });
    Object.defineProperty(event, 'clientX', { value: point.clientX ?? 0 });
    Object.defineProperty(event, 'clientY', { value: point.clientY ?? 0 });
    el.dispatchEvent(event);
}

async function dragCard(root: Element, card: string, column: string, clientY: number) {
    const source = root.querySelector(`[data-card-id="${card}"]`)!;
    const body = root.querySelector(`[data-column-id="${column}"]`)!;
    fire(source, 'dragstart');
    flushSync();
    fire(body, 'dragover', { clientY });
    flushSync();
    fire(body, 'drop', { clientY });
    fire(source, 'dragend');
    await tick();
    await tick();
}

const cardsIn = (root: Element, column: string) =>
    Array.from(root.querySelectorAll(`[data-column-id="${column}"] [data-card-id]`)).map((card) => `${card.getAttribute('data-card-id')}:${card.textContent?.trim()}`);
const count = (root: Element, column: string) =>
    root.querySelector(`[data-column="${column}"] [data-sigta="badge"]`)?.textContent?.trim();
const columnOrder = (root: Element) =>
    Array.from(root.querySelectorAll('[data-sigta="kanban"] > [data-sigta="kanban.column"]')).map((column) => column.getAttribute('data-column'));

describe('Kanban', () => {
    it('reports a card move and re-renders from the page\'s lists', async () => {
        const root = board();
        const moves: unknown[] = [];
        window.addEventListener('sigta-kanban-move', (e) => moves.push((e as CustomEvent).detail), { once: true });

        await dragCard(root, 'a', 'doing', -1);

        expect(moves).toEqual([{ card: 'a', from: 'todo', to: 'doing', position: 0 }]);
        expect(cardsIn(root, 'todo')).toEqual(['b:B']);
        expect(cardsIn(root, 'doing')).toEqual(['a:A', 'c:C']);
        expect(count(root, 'todo')).toBe('1');
        expect(count(root, 'doing')).toBe('2');
        expect(root.querySelector('[data-dragging]')).toBeNull();
        expect(root.querySelector('[data-over]')).toBeNull();
    });

    it('moves a card to the end of its own column', async () => {
        const root = board();
        await dragCard(root, 'a', 'todo', 10);
        expect(cardsIn(root, 'todo')).toEqual(['b:B', 'a:A']);
    });

    it('drops into an empty column', async () => {
        const root = board();
        await dragCard(root, 'c', 'done', 10);
        expect(cardsIn(root, 'doing')).toEqual([]);
        expect(cardsIn(root, 'done')).toEqual(['c:C']);
        expect(count(root, 'done')).toBe('1');
    });

    it('re-renders cleanly from unkeyed lists too', async () => {
        const root = board({ keyed: false });
        await dragCard(root, 'a', 'doing', -1);
        expect(cardsIn(root, 'todo')).toEqual(['b:B']);
        expect(cardsIn(root, 'doing')).toEqual(['a:A', 'c:C']);
        await dragCard(root, 'b', 'doing', 10);
        expect(cardsIn(root, 'todo')).toEqual([]);
        expect(cardsIn(root, 'doing')).toEqual(['a:A', 'c:C', 'b:B']);
    });

    it('keeps the optimistic move when the page keeps no order', async () => {
        const root = board({ persist: false });
        await dragCard(root, 'a', 'doing', -1);
        expect(cardsIn(root, 'todo')).toEqual(['b:B']);
        expect(cardsIn(root, 'doing')).toEqual(['a:A', 'c:C']);
    });

    it('reorders sortable columns from the handle', async () => {
        const root = board();
        const moves: unknown[] = [];
        window.addEventListener('sigta-kanban-column-move', (e) => moves.push((e as CustomEvent).detail), { once: true });
        const handle = root.querySelector('[data-column="done"] [data-part="handle"]')!;
        const board_ = root.querySelector('[data-sigta="kanban"]')!;
        fire(handle, 'dragstart');
        flushSync();
        expect(root.querySelector('[data-column="done"]')!.hasAttribute('data-col-dragging')).toBe(true);
        fire(board_, 'dragover', { clientX: -1 });
        fire(board_, 'drop', { clientX: -1 });
        fire(handle, 'dragend');
        await tick();
        await tick();
        expect(moves).toEqual([{ column: 'done', position: 0 }]);
        expect(columnOrder(root)).toEqual(['done', 'todo', 'doing']);
        expect(root.querySelector('[data-col-dragging]')).toBeNull();
    });

    it('counts the cards on its own, with no stray copies on the page', () => {
        const root = board();
        expect(count(root, 'todo')).toBe('2');
        expect(root.querySelectorAll('[data-card-id="a"]')).toHaveLength(1);
        expect(root.querySelector('[data-column="todo"] > template')!.innerHTML).not.toContain('>A<');
    });
});
