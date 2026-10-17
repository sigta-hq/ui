/* ---------------------------------------------------------------------------
   Alpine's x-show with class transitions, for an element Svelte keeps in the
   DOM and shows or hides the way its Blade twin does (the shell's scrim and
   drawer). The same stages as Alpine, so the transition hook classes in the
   contract stylesheet run the same motion in both runtimes:

     showing: `enter` + `away` go on, the element shows a frame later, `away`
              comes off the frame after, `enter` once the transition has run;
     hiding:  `leave` goes on, `away` two frames later, and once the
              transition has run the element is hidden and both come off.

   A change while one is running finishes the running one at once, as Alpine
   does. Hidden is display: none inline, like x-show.
--------------------------------------------------------------------------- */
export interface ShowClasses {
    /** On while it shows. */
    enter: string;
    /** On while it hides. */
    leave: string;
    /** The far end of the move: where it comes from and goes to. */
    away: string;
}

const running = new WeakMap<HTMLElement, () => void>();

function display(el: HTMLElement, shown: boolean): void {
    if (!shown) el.style.setProperty('display', 'none');
    else if (el.style.length === 1 && el.style.display === 'none') el.removeAttribute('style');
    else el.style.removeProperty('display');
}

const seconds = (value: string) => Number(value.replace(/,.*/, '').replace('s', '')) * 1000 || 0;

/** Shows or hides the element with the transition. */
export function setShown(el: HTMLElement, shown: boolean, classes: ShowClasses): void {
    running.get(el)?.();
    const during = shown ? classes.enter : classes.leave;
    let frame = 0;
    let timer: ReturnType<typeof setTimeout> | undefined;
    let reachedBefore = false;
    let reachedEnd = false;

    const before = () => {
        reachedBefore = true;
        if (shown) display(el, true);
    };
    const end = () => {
        reachedEnd = true;
        el.classList.toggle(classes.away, !shown);
    };
    const finish = () => {
        cancelAnimationFrame(frame);
        clearTimeout(timer);
        running.delete(el);
        if (!reachedBefore) before();
        if (!reachedEnd) end();
        if (!shown) display(el, false);
        el.classList.remove(during, classes.away);
    };
    running.set(el, finish);

    el.classList.add(during);
    if (shown) el.classList.add(classes.away);
    frame = requestAnimationFrame(() => {
        const style = getComputedStyle(el);
        let duration = seconds(style.transitionDuration);
        const delay = seconds(style.transitionDelay);
        if (duration === 0) duration = seconds(style.animationDuration);
        before();
        frame = requestAnimationFrame(() => {
            end();
            timer = setTimeout(finish, duration + delay);
        });
    });
}
