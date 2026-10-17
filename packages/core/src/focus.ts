/* ---------------------------------------------------------------------------
   Focus trap and scroll lock: what a surface that takes over the page (the
   shell's nav drawer) needs while it is open, when it is not a native
   <dialog> that brings them for free. The Livewire runtime gets the same
   behavior from Alpine's x-trap.noscroll; these follow its rules, so both
   runtimes' drawers behave alike:

   - on activation focus moves to an [autofocus] element inside, else the
     first tabbable one, unless focus is already inside;
   - Tab and Shift+Tab wrap around inside; focus that lands outside (a click
     on the page behind) is brought back to where it was inside;
   - on release focus returns to what had it before;
   - the scroll lock hides the root's overflow and pads it by the scrollbar's
     width, so the page does not shift; overlapping locks release together.

   Nothing runs at import time; every browser API is reached when called.
--------------------------------------------------------------------------- */
import { isBrowser } from './env.ts';

const FOCUSABLE = [
    'a[href]',
    'area[href]',
    'button:not([disabled])',
    'input:not([disabled]):not([type="hidden"])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    'iframe',
    'object',
    'embed',
    'audio[controls]',
    'video[controls]',
    'summary',
    '[contenteditable]:not([contenteditable="false"])',
    '[tabindex]',
].join(',');

/** Rendered: neither it nor an ancestor is display:none, and it is not visibility:hidden. */
function rendered(el: HTMLElement): boolean {
    const view = el.ownerDocument.defaultView;
    if (!view) return false;
    if (view.getComputedStyle(el).visibility === 'hidden') return false;
    for (let node: HTMLElement | null = el; node; node = node.parentElement) {
        if (node.hidden || view.getComputedStyle(node).display === 'none') return false;
    }
    return true;
}

/** The elements Tab reaches inside a container, in tab order (positive tabindex first). */
export function tabbables(container: HTMLElement): HTMLElement[] {
    const nodes = Array.from(container.querySelectorAll<HTMLElement>(FOCUSABLE))
        .filter((el) => el.tabIndex >= 0 && !el.closest('[inert]') && rendered(el));
    const ordered = nodes.filter((el) => el.tabIndex > 0).sort((a, b) => a.tabIndex - b.tabIndex);
    return [...ordered, ...nodes.filter((el) => el.tabIndex === 0)];
}

export interface TrapOptions {
    /** Where focus goes on activation; false leaves it where it is. Default: [autofocus], else the first tabbable. */
    initialFocus?: HTMLElement | null | false;
    /** Give focus back to what had it before on release (default true). */
    returnFocus?: boolean;
}

/**
 * Keeps keyboard focus inside the container until the returned function is
 * called. A container that is still being revealed (display:none for a
 * frame or two while it transitions in) gets its initial focus once it shows.
 */
export function trapFocus(container: HTMLElement, options: TrapOptions = {}): () => void {
    if (!isBrowser()) return () => {};
    const doc = container.ownerDocument;
    const view = doc.defaultView ?? window;
    const before = doc.activeElement instanceof view.HTMLElement ? doc.activeElement : null;
    let recent: HTMLElement | null = null;
    let active = true;
    let frame = 0;

    const initial = (): HTMLElement | null => {
        if (options.initialFocus === false) return null;
        return options.initialFocus ?? container.querySelector<HTMLElement>('[autofocus]') ?? tabbables(container)[0] ?? container;
    };
    const inside = () => container.contains(doc.activeElement);

    const settle = (tries: number) => {
        frame = 0;
        if (!active || inside()) return;
        initial()?.focus();
        if (!inside() && tries > 0) frame = view.requestAnimationFrame(() => settle(tries - 1));
    };

    const keydown = (event: KeyboardEvent) => {
        if (event.key !== 'Tab' || event.altKey || event.ctrlKey || event.metaKey) return;
        const nodes = tabbables(container);
        if (!nodes.length) {
            event.preventDefault();
            return;
        }
        const index = nodes.indexOf(doc.activeElement as HTMLElement);
        if (event.shiftKey && index <= 0) {
            event.preventDefault();
            nodes[nodes.length - 1].focus();
        } else if (!event.shiftKey && (index === -1 || index === nodes.length - 1)) {
            event.preventDefault();
            nodes[0].focus();
        }
    };

    const focusin = (event: FocusEvent) => {
        const target = event.target as Node | null;
        if (target && container.contains(target)) {
            recent = target as HTMLElement;
            return;
        }
        event.stopImmediatePropagation();
        const back = recent?.isConnected && container.contains(recent) ? recent : initial();
        back?.focus();
    };

    doc.addEventListener('keydown', keydown, true);
    doc.addEventListener('focusin', focusin, true);
    settle(10);

    return () => {
        if (!active) return;
        active = false;
        if (frame) view.cancelAnimationFrame(frame);
        doc.removeEventListener('keydown', keydown, true);
        doc.removeEventListener('focusin', focusin, true);
        if (options.returnFocus !== false && before && before.isConnected && before !== doc.body) before.focus();
    };
}

interface Lock {
    count: number;
    overflow: string;
    paddingRight: string;
}

// Keyed by document, so nothing is shared outside the page that locked.
const locks = new WeakMap<Document, Lock>();

/**
 * Stops the page behind an overlay from scrolling: the root's overflow is
 * hidden and padded by the scrollbar it loses, so nothing shifts sideways.
 * Returns the release; the styles come back once every lock is released.
 */
export function lockScroll(doc?: Document): () => void {
    if (!isBrowser()) return () => {};
    const page = doc ?? document;
    const root = page.documentElement;
    let lock = locks.get(page);
    if (!lock) {
        const scrollbar = (page.defaultView?.innerWidth ?? root.clientWidth) - root.clientWidth;
        lock = { count: 0, overflow: root.style.overflow, paddingRight: root.style.paddingRight };
        locks.set(page, lock);
        root.style.overflow = 'hidden';
        root.style.paddingRight = `${scrollbar}px`;
    }
    lock.count++;
    const held = lock;
    let released = false;
    return () => {
        if (released) return;
        released = true;
        if (--held.count > 0) return;
        locks.delete(page);
        root.style.overflow = held.overflow;
        root.style.paddingRight = held.paddingRight;
    };
}
