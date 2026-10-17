/* ---------------------------------------------------------------------------
   Tabs — the Svelte twin of the Livewire runtime's sigtaTabs: the group owns
   the active tab name (bind:value) and shares it through context; the list
   moves it with the arrow keys (wrapping), Home and End, focusing the tab it
   lands on, per the WAI-ARIA tabs pattern with automatic activation.
--------------------------------------------------------------------------- */
import { getContext, setContext } from 'svelte';

export interface TabsContext {
    readonly active: string | null;
    isActive(name: string): boolean;
    select(name: string): void;
    /** Steps `delta` tabs from the active one, wrapping round. */
    move(delta: number): void;
    /** The tab at index (-1 is the last). */
    moveTo(index: number): void;
}

const KEY = Symbol('sigta-tabs');

export const setTabs = (tabs: TabsContext): TabsContext => setContext(KEY, tabs);
export const useTabs = (): TabsContext => getContext(KEY);

/** The tabs of a group, in document order (as the Livewire runtime reads them). */
export const tabsIn = (root: HTMLElement | null | undefined): HTMLElement[] =>
    root ? Array.from(root.querySelectorAll<HTMLElement>('[role="tab"]')) : [];
