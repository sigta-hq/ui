/* ---------------------------------------------------------------------------
   A textarea that grows with its text up to a number of rows, then scrolls
   (the composer). Both runtimes call it on input, so the height they write
   is the same. Reached only when called; SSR-safe.
--------------------------------------------------------------------------- */

/** Fits the textarea's height to its text, up to maxRows lines; past that it scrolls. */
export function autosize(el: HTMLTextAreaElement, maxRows: number): void {
    el.style.height = 'auto';
    const line = parseFloat(getComputedStyle(el).lineHeight) || 20;
    const max = line * maxRows;
    el.style.height = Math.min(el.scrollHeight, max) + 'px';
    el.style.overflowY = el.scrollHeight > max ? 'auto' : 'hidden';
}
