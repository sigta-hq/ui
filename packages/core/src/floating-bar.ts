/* ---------------------------------------------------------------------------
   Floating bar — while it is up, the bar publishes its footprint as
   --sigta-floating-space on <html>, which the shell's main column and the
   bottom toast region add to their bottom edge, so nothing ends up stuck
   under it. Both runtimes call these from their floating bar.
--------------------------------------------------------------------------- */
import { isBrowser } from './env.ts';

export const FLOATING_SPACE = '--sigta-floating-space';

/** Publishes the bar's footprint (its height plus the root's padding above and below), or 0 while it is hidden. */
export function publishFloatingSpace(root: HTMLElement, bar: HTMLElement, visible: boolean): void {
    if (!isBrowser()) return;
    const bottom = parseFloat(getComputedStyle(root).paddingBottom) || 0;
    const space = visible ? bar.offsetHeight + bottom * 2 : 0;
    document.documentElement.style.setProperty(FLOATING_SPACE, `${space}px`);
}

/** Takes the footprint away again (the bar left the page). */
export function clearFloatingSpace(): void {
    if (!isBrowser()) return;
    document.documentElement.style.removeProperty(FLOATING_SPACE);
}
