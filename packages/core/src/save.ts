/* ---------------------------------------------------------------------------
   Save state — the compass stands in for "Saving…": it draws while a save is
   in flight, lands with a handwritten "Saved", and shakes red when a save
   fails. Named save states hear `sigta-save-state` window events, so any
   promise can drive one: save(fetch(…), 'profile').
--------------------------------------------------------------------------- */
import { isBrowser } from './env.ts';

export const SAVE_STATES = ['idle', 'dirty', 'saving', 'saved', 'failed'] as const;
export type SaveState = (typeof SAVE_STATES)[number];
export const SAVE_EVENT = 'sigta-save-state';
/** How long "Saved" stays before the indicator rests again. */
export const SAVED_HOLD = 2600;

export const isSaveState = (state: unknown): state is SaveState => SAVE_STATES.includes(state as SaveState);

/** The loader state that draws each save state. */
export function saveMark(state: SaveState): 'idle' | 'queued' | 'working' | 'done' | 'failed' {
    return ({ dirty: 'queued', saving: 'working', saved: 'done', failed: 'failed' } as const)[state as Exclude<SaveState, 'idle'>] ?? 'idle';
}

export function saveState(state: SaveState, name: string | null = null): void {
    if (!isBrowser()) return;
    window.dispatchEvent(new CustomEvent(SAVE_EVENT, { detail: { name, state } }));
}

/** Drive named (or unnamed) save states from any promise or function. */
export function save<T>(work: Promise<T> | (() => T | Promise<T>), name: string | null = null): Promise<T> {
    saveState('saving', name);
    return Promise.resolve(typeof work === 'function' ? (work as () => T | Promise<T>)() : work).then(
        (value) => { saveState('saved', name); return value; },
        (error) => { saveState('failed', name); throw error; },
    );
}
