/* ---------------------------------------------------------------------------
   Environment and theme timing.

   Nothing in the core touches `window` or `document` when it is imported:
   every browser API is reached inside a function, so the package is safe to
   import while a Svelte page renders on a server.
--------------------------------------------------------------------------- */

export const isBrowser = (): boolean => typeof window !== 'undefined' && typeof document !== 'undefined';

let reducedQuery: MediaQueryList | null = null;

/** The visitor asked for reduced motion: skip the flourish, never the work. */
export function reduced(): boolean {
    if (!isBrowser() || typeof window.matchMedia !== 'function') return true;
    reducedQuery ??= window.matchMedia('(prefers-reduced-motion: reduce)');
    return reducedQuery.matches;
}

export const wait = (ms: number): Promise<void> => new Promise((resolve) => setTimeout(resolve, ms));

/** Resolves when an animation ends, finished or cancelled. */
export const settled = (animation: Animation): Promise<void> => animation.finished.then(() => {}, () => {});

/** A theme token, read at call time so a re-themed subtree wins. */
export function token(name: string, fallback: string, el?: Element | null): string {
    if (!isBrowser()) return fallback;
    return getComputedStyle(el ?? document.documentElement).getPropertyValue(name).trim() || fallback;
}

/** A duration token in milliseconds (`420ms`, `.42s` or a bare number). */
export function duration(name: string, fallback: number, el?: Element | null): number {
    const value = token(name, '', el);
    const number = parseFloat(value);
    if (Number.isNaN(number)) return fallback;
    return value.endsWith('ms') ? number : value.endsWith('s') ? number * 1000 : number;
}

export const curves = {
    out: 'cubic-bezier(.2, .8, .2, 1)',
    pen: 'cubic-bezier(.65, 0, .35, 1)',
    spring: 'cubic-bezier(.34, 1.56, .64, 1)',
} as const;

export const pen = (el?: Element | null): string => token('--sigta-curve-pen', curves.pen, el);
export const spring = (el?: Element | null): string => token('--sigta-curve-spring', curves.spring, el);
export const settle = (el?: Element | null): string => token('--sigta-curve-out', curves.out, el);

export type Target = Element | string | null | undefined;

/** An element, or the first match of a selector. */
export function elementOf(target: Target): HTMLElement | null {
    if (!isBrowser() || !target) return null;
    return (typeof target === 'string' ? document.querySelector(target) : target) as HTMLElement | null;
}
