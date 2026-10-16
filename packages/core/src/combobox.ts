/* ---------------------------------------------------------------------------
   Combobox logic shared by Select (rich), Autocomplete, Command and Pillbox
   in both runtimes: which options a query keeps, how the active option moves,
   and what choosing one does to the value. Pure: no DOM, no framework.
--------------------------------------------------------------------------- */

export interface Choice {
    value: string;
    label: string;
}

/** An option stays when the query is empty or its text contains the query. */
export const matchesQuery = (text: string, query: string): boolean => {
    const q = query.trim().toLowerCase();
    return q === '' || text.toLowerCase().includes(q);
};

/** The index `delta` steps from `current` among `count` options, wrapping round. */
export const step = (current: number, delta: number, count: number): number =>
    count === 0 ? -1 : (current + delta + count) % count;

/** The value after choosing `value`: replaced, or toggled in a multiple value. */
export function choose(value: string | string[] | null, chosen: string, multiple: boolean): string | string[] {
    if (!multiple) return chosen;
    const list = Array.isArray(value) ? value : [];
    return list.includes(chosen) ? list.filter((v) => v !== chosen) : [...list, chosen];
}

export const isChosen = (value: string | string[] | null, candidate: string): boolean =>
    Array.isArray(value) ? value.includes(candidate) : value === candidate;

/** How a value is written into the hidden input a form or wire:model reads. */
export const serialize = (value: string | string[] | null): string =>
    Array.isArray(value) ? JSON.stringify(value) : (value ?? '');
