/* ---------------------------------------------------------------------------
   Reading a consumer's class list. Some surfaces (card, panel) let a class
   the consumer passes replace a default instead of competing with it: a
   padding class replaces the default padding, a display class replaces the
   default stacking layout. A consumer class already outranks the component
   layer property by property, but "replace" means dropping the default as a
   whole (px-6 alone leaves no vertical default either, a flex row loses the
   column direction), so both runtimes read the class the same way and set a
   contract attribute (data-padding="custom", data-layout="custom") the
   stylesheet keys on. The Blade views carry the same two patterns in PHP.
   Pure and SSR-safe.
--------------------------------------------------------------------------- */

/** A padding utility (p-, px-, py-, pt-, pr-, pb-, pl-, ps-, pe-) under any variants, important or not. */
const PADDING = /(^|\s)([^\s:]+:)*!?p[xytrblse]?-/;

/** An unprefixed display utility. A responsive one (sm:flex) builds on the default instead. */
const DISPLAY = /(^|\s)!?(block|inline-block|flex|inline-flex|grid|inline-grid|flow-root)!?(?=\s|$)/;

export type ClassValue = string | number | bigint | boolean | null | undefined | ClassValue[] | { [name: string]: unknown };

/** A class value (string, array, or { name: condition } map, as Svelte accepts) as one class string. */
export function classString(value: ClassValue): string {
    if (typeof value === 'string') return value;
    if (typeof value === 'number' || typeof value === 'bigint') return String(value);
    if (Array.isArray(value)) return value.map(classString).filter(Boolean).join(' ');
    if (value && typeof value === 'object') return Object.keys(value).filter((name) => value[name]).join(' ');
    return '';
}

/** Whether the class list sets its own padding. */
export const ownsPadding = (value: ClassValue): boolean => PADDING.test(classString(value));

/** Whether the class list sets its own display (unprefixed). */
export const ownsDisplay = (value: ClassValue): boolean => DISPLAY.test(classString(value));
