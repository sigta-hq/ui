/* ---------------------------------------------------------------------------
   Fold / unfold — a leaving row slides aside and the gap closes after it; an
   arriving row opens its gap, settles in and carries a brief highlighter
   wash so the eye finds it. Block rows only; a table row or a
   display:contents wrapper just fades.

   Exported as players (fold, unfold) plus their timings (foldDuration,
   unfoldDuration), so a runtime that owns element removal — Svelte's out:
   transitions — can start the same animation and wait exactly as long.
--------------------------------------------------------------------------- */
import { duration, elementOf, reduced, settle, settled, token, type Target } from './env.ts';

const edges = ['paddingTop', 'paddingBottom', 'marginTop', 'marginBottom', 'borderTopWidth', 'borderBottomWidth'] as const;
const foldable = (style: CSSStyleDeclaration) => !['table-row', 'contents', 'inline'].includes(style.display);

function shape(el: Element, style: CSSStyleDeclaration): [Record<string, string>, Record<string, string>] {
    const open: Record<string, string> = { height: `${el.getBoundingClientRect().height}px` };
    const shut: Record<string, string> = { height: '0px' };
    edges.forEach((edge) => { open[edge] = style[edge]; shut[edge] = '0px'; });
    return [open, shut];
}

/** How long fold(el) runs, in ms (0 under reduced motion). */
export function foldDuration(el: Element): number {
    if (reduced()) return 0;
    const time = duration('--sigta-dur-leave', 360, el);
    return foldable(getComputedStyle(el)) ? time * 1.6 : time;
}

/** How long unfold(el) runs, in ms (0 under reduced motion). */
export function unfoldDuration(el: Element): number {
    if (reduced()) return 0;
    const time = duration('--sigta-dur-draw', 420, el);
    return foldable(getComputedStyle(el)) ? time * 1.4 : time;
}

export function fold(el: HTMLElement | null): Promise<void> {
    if (!el?.isConnected || reduced()) return Promise.resolve();
    const style = getComputedStyle(el);
    const time = duration('--sigta-dur-leave', 360, el);
    el.style.pointerEvents = 'none';
    if (!foldable(style)) {
        return settled(el.animate([{ opacity: 1 }, { opacity: 0 }], { duration: time, fill: 'forwards' }));
    }
    const [open, shut] = shape(el, style);
    el.style.overflow = 'hidden';
    el.style.boxSizing = 'border-box';
    return settled(el.animate([
        { ...open, opacity: 1, translate: '0 0', offset: 0 },
        { ...open, opacity: 0, translate: '1.25rem 0', offset: 0.45 },
        { ...shut, opacity: 0, translate: '1.25rem 0', offset: 1 },
    ], { duration: time * 1.6, easing: 'cubic-bezier(.4, 0, .2, 1)', fill: 'forwards' }));
}

export function unfold(el: HTMLElement | null): Promise<void> {
    if (!el?.isConnected || reduced()) return Promise.resolve();
    const style = getComputedStyle(el);
    const time = duration('--sigta-dur-draw', 420, el);
    if (!foldable(style)) {
        return settled(el.animate([{ opacity: 0 }, { opacity: 1 }], { duration: time }));
    }
    const [open, shut] = shape(el, style);
    const accent = token('--sigta-accent', 'gold', el);
    el.style.overflow = 'hidden';
    el.animate(
        [{ boxShadow: `inset 0 0 0 100vmax color-mix(in srgb, ${accent} 45%, transparent)` }, { boxShadow: 'inset 0 0 0 100vmax transparent' }],
        { duration: 1600, delay: time * 0.6, easing: 'ease-out' },
    );
    return settled(el.animate([
        { ...shut, opacity: 0, translate: '0 -0.35rem', offset: 0 },
        { ...open, opacity: 0, translate: '0 -0.35rem', offset: 0.45 },
        { ...open, opacity: 1, translate: '0 0', offset: 1 },
    ], { duration: time * 1.4, easing: settle(el) })).then(() => { el.style.overflow = ''; });
}

// The text either side of a removed row becomes one node, as a fresh render
// has it: two in a row make the next morph move the row after them into
// line, and a moved row replays its animations (a strike drawn twice).
export function detach(el: Element): void {
    const before = el.previousSibling;
    const after = el.nextSibling;
    el.remove();
    if (before?.nodeType === Node.TEXT_NODE && after?.nodeType === Node.TEXT_NODE) {
        before.nodeValue += after.nodeValue ?? '';
        after.remove();
    }
}

export interface LeaveOptions {
    /** false when a framework owns the node (an Alpine x-for): fold, then let it drop the item. */
    remove?: boolean;
}

/* Leave — fold a row away and remove it. */
export async function leave(target: Target, options: LeaveOptions = {}): Promise<void> {
    const el = elementOf(target);
    if (!el?.isConnected) return;
    el.setAttribute('data-sigta-leaving', '');
    await fold(el);
    // Brought back mid-fold (an undo that beat the animation): it stays.
    if (!el.hasAttribute('data-sigta-leaving')) return;
    if (options.remove !== false) detach(el);
}

/** Undo a fold in progress and hand the row back to the page. */
export function revive(el: HTMLElement): void {
    el.removeAttribute('data-sigta-leaving');
    el.getAnimations().forEach((animation) => animation.cancel());
    (['pointerEvents', 'overflow', 'boxSizing'] as const).forEach((property) => { el.style[property] = ''; });
}

export function shake(el: HTMLElement | null): void {
    if (reduced() || !el?.isConnected) return;
    el.animate(
        [{ translate: '0 0' }, { translate: '-5px 0' }, { translate: '4px 0' }, { translate: '-2px 0' }, { translate: '0 0' }],
        { duration: 380, easing: 'ease-in-out' },
    );
}
