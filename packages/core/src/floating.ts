/* ---------------------------------------------------------------------------
   Floating panels — a listbox, menu or popover floats with fixed positioning
   so it escapes any ancestor overflow (a card, a form) instead of being
   clipped, and flips above its anchor when there isn't room below.
--------------------------------------------------------------------------- */

export interface FloatOptions {
    gap?: number;
    /** Match the anchor's width (a listbox under its input). */
    matchWidth?: boolean;
}

export function positionPanel(anchor: HTMLElement | null | undefined, panel: HTMLElement | null | undefined, options: FloatOptions = {}): void {
    if (!anchor || !panel) return;
    const gap = options.gap ?? 6;
    const rect = anchor.getBoundingClientRect();
    const spaceBelow = window.innerHeight - rect.bottom;
    const flip = spaceBelow < panel.offsetHeight + gap && rect.top > spaceBelow;

    panel.style.position = 'fixed';
    panel.style.left = `${Math.round(rect.left)}px`;
    if (options.matchWidth !== false) panel.style.width = `${Math.round(rect.width)}px`;
    if (flip) {
        panel.style.top = 'auto';
        panel.style.bottom = `${Math.round(window.innerHeight - rect.top + gap)}px`;
    } else {
        panel.style.bottom = 'auto';
        panel.style.top = `${Math.round(rect.bottom + gap)}px`;
    }
}
