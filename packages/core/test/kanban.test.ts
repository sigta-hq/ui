// @vitest-environment jsdom
// Where a dragged card or column lands: the math both boards share, and the
// DOM moves built on it (boxes stubbed, since jsdom does no layout).
import { describe, expect, it } from 'vitest';
import {
    cardElement,
    cardPosition,
    columnElement,
    columnPosition,
    columnUnit,
    columnUnits,
    dropBefore,
    placeCard,
    placeColumn,
} from '../src/kanban.ts';

describe('dropBefore', () => {
    const stack = [{ start: 0, size: 40 }, { start: 50, size: 40 }, { start: 100, size: 40 }];

    it('drops before the first item whose middle is past the pointer', () => {
        expect(dropBefore(stack, 5)).toBe(0);
        expect(dropBefore(stack, 19)).toBe(0);
        expect(dropBefore(stack, 21)).toBe(1);
        expect(dropBefore(stack, 69)).toBe(1);
        expect(dropBefore(stack, 71)).toBe(2);
    });

    it('drops at the end past the last middle, and into an empty list', () => {
        expect(dropBefore(stack, 120)).toBe(-1);
        expect(dropBefore(stack, 500)).toBe(-1);
        expect(dropBefore([], 10)).toBe(-1);
    });

    it('treats a pointer exactly on a middle as past it', () => {
        expect(dropBefore(stack, 20)).toBe(1);
    });

    it('picks the nearest middle past the pointer, whatever the order', () => {
        const shuffled = [stack[2], stack[0], stack[1]];
        expect(dropBefore(shuffled, 30)).toBe(2);
        expect(dropBefore(shuffled, 75)).toBe(0);
    });
});

/** Gives each element a box: cards stacked 50px apart, columns 300px apart. */
function layout(root: HTMLElement): void {
    root.querySelectorAll<HTMLElement>('[data-kanban-body]').forEach((body) => {
        Array.from(body.querySelectorAll<HTMLElement>('[data-card-id]')).forEach((card) => {
            card.getBoundingClientRect = () => {
                const index = Array.from(card.parentElement!.children).indexOf(card);
                return { top: index * 50, height: 40, left: 0, width: 200 } as DOMRect;
            };
        });
    });
    (Array.from(root.children) as HTMLElement[]).forEach((unit) => {
        unit.getBoundingClientRect = () => {
            const index = Array.from(root.children).indexOf(unit);
            return { left: index * 300, width: 280, top: 0, height: 400 } as DOMRect;
        };
    });
}

function board(): HTMLElement {
    document.body.innerHTML = `
        <div data-sigta="kanban">
            <div data-sigta="kanban.column" data-column="todo"><div data-kanban-body data-column-id="todo">
                <div data-card-id="a"></div><div data-card-id="b"></div><div data-card-id="c"></div>
            </div></div>
            <section wire:key="doing"><div data-sigta="kanban.column" data-column="doing"><div data-kanban-body data-column-id="doing">
                <div data-card-id="d"></div>
            </div></div></section>
            <div data-sigta="kanban.column" data-column="done"><div data-kanban-body data-column-id="done"></div></div>
            <p>Not a column</p>
        </div>`;
    const root = document.querySelector<HTMLElement>('[data-sigta="kanban"]')!;
    layout(root);
    return root;
}

const ids = (body: Element) => Array.from(body.querySelectorAll('[data-card-id]')).map((card) => card.getAttribute('data-card-id'));
const columns = (root: HTMLElement) => columnUnits(root).map((unit) => unit.querySelector('[data-column]')?.getAttribute('data-column') ?? unit.getAttribute('data-column'));

describe('cards', () => {
    it('finds a card by id, and nothing without one', () => {
        const root = board();
        expect(cardElement(root, 'b')?.getAttribute('data-card-id')).toBe('b');
        expect(cardElement(root, null)).toBeNull();
        expect(cardElement(root, 'zzz')).toBeNull();
    });

    it('moves a card up its own column', () => {
        const root = board();
        const todo = root.querySelector<HTMLElement>('[data-column-id="todo"]')!;
        const c = cardElement(root, 'c')!;
        c.setAttribute('data-dragging', '');
        placeCard(todo, c, 10);
        expect(ids(todo)).toEqual(['c', 'a', 'b']);
        expect(cardPosition(todo, c)).toBe(0);
    });

    it('moves a card into another column, between or after its cards', () => {
        const root = board();
        const doing = root.querySelector<HTMLElement>('[data-column-id="doing"]')!;
        const a = cardElement(root, 'a')!;
        a.setAttribute('data-dragging', '');
        placeCard(doing, a, 5);
        expect(ids(doing)).toEqual(['a', 'd']);
        layout(root);
        placeCard(doing, a, 200);
        expect(ids(doing)).toEqual(['d', 'a']);
        expect(cardPosition(doing, a)).toBe(1);
        expect(ids(root.querySelector('[data-column-id="todo"]')!)).toEqual(['b', 'c']);
    });

    it('drops into an empty column at position 0', () => {
        const root = board();
        const done = root.querySelector<HTMLElement>('[data-column-id="done"]')!;
        const b = cardElement(root, 'b')!;
        placeCard(done, b, 0);
        expect(ids(done)).toEqual(['b']);
        expect(cardPosition(done, b)).toBe(0);
    });
});

describe('columns', () => {
    it('lists the units that are or wrap a column, and resolves a column to its unit', () => {
        const root = board();
        expect(columns(root)).toEqual(['todo', 'doing', 'done']);
        const doing = columnElement(root, 'doing')!;
        expect(columnUnit(root, doing)?.localName).toBe('section');
        expect(columnElement(root, null)).toBeNull();
    });

    it('moves a column left, right and to the end', () => {
        const root = board();
        const done = columnUnit(root, columnElement(root, 'done'))!;
        placeColumn(root, done, 10);
        expect(columns(root)).toEqual(['done', 'todo', 'doing']);
        expect(columnPosition(root, done)).toBe(0);

        layout(root);
        const todo = columnUnit(root, columnElement(root, 'todo'))!;
        placeColumn(root, todo, 2000);
        expect(columns(root)).toEqual(['done', 'doing', 'todo']);
        expect(columnPosition(root, todo)).toBe(2);

        layout(root);
        const doing = columnUnit(root, columnElement(root, 'doing'))!;
        placeColumn(root, doing, 400);
        expect(columns(root)).toEqual(['done', 'doing', 'todo']);
    });

    it('keeps non-column children where they are', () => {
        const root = board();
        const todo = columnUnit(root, columnElement(root, 'todo'))!;
        placeColumn(root, todo, 5000);
        expect(Array.from(root.children).map((child) => child.localName)).toEqual(['section', 'div', 'div', 'p']);
        expect(columnPosition(root, null)).toBe(0);
    });
});
