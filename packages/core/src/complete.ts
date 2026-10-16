/* ---------------------------------------------------------------------------
   Complete — the moment a task, a message, a to-do is done: its box ticks, a
   burst flicks out, each [data-sigta-strike] is crossed through, then the row
   folds away. `commit` does the real work (a server call, an array filter, a
   fetch); if it throws or rejects, the row is put back.

   Two orders, chosen by the runtime:
     atOnce  the commit goes out at once and the row leaves only when the
             runtime says the server dropped it (Livewire, where a morph does)
     else    the row folds first, then commit runs (plain pages, Alpine x-for
             lists, Svelte lists that drop the item in commit)

   Options: stamp ('Done' or true) presses a stamp on the row; leave: false
   keeps it in place, crossed out.
--------------------------------------------------------------------------- */
import { burst } from './celebrate.ts';
import { duration, elementOf, reduced, wait, type Target } from './env.ts';
import { fold, shake } from './fold.ts';

export const ROW_SELECTOR = '[data-sigta-row], [data-sigta-motion] > *';

export interface CompleteOptions {
    stamp?: string | boolean;
    leave?: boolean;
    /** Commit at once and let the runtime remove the row (see onCommitted). */
    atOnce?: boolean;
    /** Runs after an at-once commit settles, e.g. to fold a row the server dropped. */
    onCommitted?: (row: HTMLElement) => unknown;
    /** Extra selector for the row a target belongs to (Livewire adds [wire:key]). */
    rowSelector?: string;
}

export function rowOf(target: Target, rowSelector?: string): HTMLElement | null {
    const el = elementOf(target);
    const selector = rowSelector ? `${ROW_SELECTOR}, ${rowSelector}` : ROW_SELECTOR;
    return (el?.closest?.(selector) as HTMLElement | null) ?? el;
}

export type Commit = (() => unknown) | Promise<unknown> | null;

export async function complete(target: Target, commit: Commit = null, options: CompleteOptions = {}): Promise<boolean> {
    const row = rowOf(target, options.rowSelector);
    if (!row || row.hasAttribute('data-sigta-completing')) return false;
    const source = elementOf(target);
    const quiet = reduced();
    row.setAttribute('data-sigta-done', '');
    row.setAttribute('data-sigta-completing', '');
    let stamp: HTMLElement | null = null;
    if (!quiet) {
        const from = source?.matches?.('input, button') ? source : row.querySelector('input[type="checkbox"]') ?? source;
        burst(from, { reach: 10 });
        if (options.stamp) {
            stamp = document.createElement('span');
            stamp.className = 'sigta-stamp';
            stamp.setAttribute('aria-hidden', 'true');
            stamp.textContent = options.stamp === true ? 'Done' : options.stamp;
            if (getComputedStyle(row).position === 'static') row.style.position = 'relative';
            row.appendChild(stamp);
        }
    }
    const marked = wait(quiet ? 0 : duration('--sigta-dur-draw', 420, row) + 280);
    const run = () => Promise.resolve(typeof commit === 'function' ? commit() : commit);
    try {
        if (options.atOnce) {
            // The optimistic flags hold only while the commit is out; from
            // its answer on, what the server renders wins.
            const committed = run().finally(() => row.removeAttribute('data-sigta-completing'));
            await Promise.all([committed, marked]);
            await options.onCommitted?.(row);
        } else {
            await marked;
            if (options.leave !== false) {
                // It is leaving now: a runtime that removes it after the
                // commit (a Svelte out: transition) has nothing left to fold.
                row.setAttribute('data-sigta-leaving', '');
                await fold(row);
            }
            await run();
            row.removeAttribute('data-sigta-completing');
            if (options.leave !== false && !commit) row.remove();
        }
        return true;
    } catch (error) {
        row.getAnimations().forEach((animation) => animation.cancel());
        row.style.pointerEvents = '';
        row.style.overflow = '';
        row.removeAttribute('data-sigta-completing');
        row.removeAttribute('data-sigta-done');
        stamp?.remove();
        if (source instanceof HTMLInputElement && source.type === 'checkbox') source.checked = false;
        shake(row);
        console.error(error);
        return false;
    }
}
