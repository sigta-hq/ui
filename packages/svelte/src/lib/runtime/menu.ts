/* ---------------------------------------------------------------------------
   Menus — the Svelte twin of the Livewire runtime's sigtaDropdown (and the
   context menu's inline Alpine): a menu publishes close() through context so
   a Dropdown.Item anywhere inside closes it, and the item list is read from
   the DOM, so items rendered by an {#each} count too. Roving focus follows
   the WAI-ARIA menu pattern: arrows wrap, Home and End jump to the ends.
--------------------------------------------------------------------------- */
import { getContext, setContext } from 'svelte';

export interface MenuContext {
    close(focusTrigger?: boolean): void;
}

const KEY = Symbol('sigta-menu');

export const setMenu = (menu: MenuContext): MenuContext => setContext(KEY, menu);
export const useMenu = (): MenuContext | undefined => getContext(KEY);

/** The enabled menu items inside an element, in document order. */
export const menuItems = (root: HTMLElement | null | undefined): HTMLElement[] =>
    root ? Array.from(root.querySelectorAll<HTMLElement>('[role="menuitem"]:not([aria-disabled="true"])')) : [];

/** Focuses the item at index, wrapping round (-1 is the last). */
export function focusItem(root: HTMLElement | null | undefined, index: number): void {
    const items = menuItems(root);
    if (!items.length) return;
    items[((index % items.length) + items.length) % items.length].focus();
}

/** Arrow keys, Home and End over a menu's items; true when the key was handled. */
export function menuKeydown(event: KeyboardEvent, root: HTMLElement | null | undefined): boolean {
    const move = (delta: number) => {
        const current = menuItems(root).indexOf(document.activeElement as HTMLElement);
        focusItem(root, current === -1 ? (delta > 0 ? 0 : -1) : current + delta);
    };
    if (event.key === 'ArrowDown') move(1);
    else if (event.key === 'ArrowUp') move(-1);
    else if (event.key === 'Home') focusItem(root, 0);
    else if (event.key === 'End') focusItem(root, -1);
    else return false;
    event.preventDefault();
    return true;
}

/**
 * Calls `close` on a click outside `inside()` while attached (use it in an
 * $effect that runs while the panel is open). A click that opened the panel
 * can still reach the document after the effect attached, so callers pass the
 * whole component (trigger included) as `inside`.
 */
export function onClickOutside(inside: () => HTMLElement | null | undefined, close: () => void): () => void {
    const listener = (event: MouseEvent) => {
        const target = event.target as Node | null;
        // A click that removed its own target (a re-rendered row) says nothing about where it was.
        if (target && !target.isConnected) return;
        if (!inside()?.contains(target)) close();
    };
    document.addEventListener('click', listener);
    return () => document.removeEventListener('click', listener);
}
