/* ---------------------------------------------------------------------------
   Page-wide watchers. Each one decorates state the markup already carries,
   whoever changes it (a morph, a Svelte update, an x-text, plain DOM), and
   returns a function that stops it.
--------------------------------------------------------------------------- */
import { duration, reduced, settle, settled, spring } from './env.ts';
import { unfold } from './fold.ts';
import { burst } from './celebrate.ts';

type Stop = () => void;

/* Loader landing — when a working compass is told it is done, it finishes
   the turn it is on and closes its circle, then the circle's front rises
   into the mark's arc, and only then does the CSS done state (the stamp)
   take over. data-landing keeps the working motion running underneath
   until the landing hands over. Without script, or with reduced motion,
   the loader simply lands. */
export function landLoader(loader: HTMLElement): void {
    if (reduced() || loader.dataset.variant !== 'compass' || loader.hasAttribute('data-landing')) return;
    loader.setAttribute('data-landing', '');
    const deg = parseFloat(getComputedStyle(loader).getPropertyValue('--sigta-loader-deg')) || 0;
    const end = Math.max(360, Math.ceil(deg / 360 - 1e-6) * 360);
    const turn = 380 + 520 * ((end - deg) / 360);
    const rise = 320;
    const ease = settle(loader);
    const landing = loader.animate([
        { '--sigta-loader-deg': String(deg), '--sigta-loader-fill': '0', '--sigta-loader-rise': '0', easing: ease },
        { '--sigta-loader-deg': String(end), '--sigta-loader-fill': '1', '--sigta-loader-rise': '0', offset: turn / (turn + rise), easing: ease },
        { '--sigta-loader-deg': String(end), '--sigta-loader-fill': '1', '--sigta-loader-rise': '1' },
    ] as Keyframe[], { duration: turn + rise, fill: 'forwards' });
    settled(landing).then(() => {
        loader.removeAttribute('data-landing');
        landing.cancel();
    });
}

export function observeLoaders(root: Node = document.documentElement): Stop {
    const observer = new MutationObserver((records) => records.forEach((record) => {
        const loader = record.target as HTMLElement;
        if (loader.dataset?.sigta === 'loader' && loader.dataset.state === 'done' && record.oldValue === 'working') landLoader(loader);
    }));
    observer.observe(root, { attributes: true, attributeFilter: ['data-state'], attributeOldValue: true, subtree: true });
    return () => observer.disconnect();
}

/* Busy buttons: the runtime sets data-loading on a button (on the form, for
   a submit) the moment its action goes out. The button says so only once
   the action outlasts a beat, so a quick one shows nothing: a timer, not a
   CSS delay, because reduced motion drops every animation. */
const busyTimers = new WeakMap<Element, ReturnType<typeof setTimeout>>();

function busyButtons(el: Element): HTMLElement[] {
    if (el.matches('form')) return Array.from(el.querySelectorAll('[data-sigta="button"][type="submit"]'));
    return el.matches('[data-sigta="button"]') ? [el as HTMLElement] : [];
}

/** A leading icon is one the label starts with, before any words. */
export function markLeadIcon(button: HTMLElement): void {
    const label = button.querySelector(':scope > [data-part="label"]');
    if (!label) return;
    const first = Array.from(label.childNodes).find((node) => !(node.nodeType === Node.TEXT_NODE && !node.textContent?.trim()) && node.nodeType !== Node.COMMENT_NODE);
    button.toggleAttribute('data-lead-icon', first instanceof SVGElement);
}

export function busyChanged(target: Element): void {
    const buttons = busyButtons(target);
    if (buttons.length === 0) return;
    clearTimeout(busyTimers.get(target));
    if (!target.hasAttribute('data-loading')) {
        buttons.forEach((button) => button.removeAttribute('data-sigta-busy'));
        return;
    }
    busyTimers.set(target, setTimeout(() => {
        if (target.hasAttribute('data-loading')) buttons.forEach((button) => button.setAttribute('data-sigta-busy', ''));
    }, duration('--sigta-dur-busy', 500, target)));
}

export function observeBusy(root: Node = document.documentElement): Stop {
    const observer = new MutationObserver((records) => records.forEach(({ target }) => busyChanged(target as Element)));
    observer.observe(root, { attributes: true, attributeFilter: ['data-loading'], subtree: true });
    return () => observer.disconnect();
}

/* Motion lists — rows added to a [data-sigta-motion] list after the page has
   settled unfold in, whoever adds them. A node that was also removed in the
   same batch was only moved. `ready` says when the page has settled. */
export function observeMotionLists(root: Node = document.documentElement, ready: () => boolean = () => true): Stop {
    const born = new WeakMap<Node, number>();
    const observer = new MutationObserver((records) => {
        if (!ready()) return;
        const now = performance.now();
        const moved = new Set<Node>();
        records.forEach((record) => record.removedNodes.forEach((node) => moved.add(node)));
        records.forEach((record) => {
            record.addedNodes.forEach((node) => {
                if (node.nodeType !== 1) return;
                const el = node as Element;
                if (el.matches('[data-sigta-motion]')) born.set(el, now);
                el.querySelectorAll('[data-sigta-motion]').forEach((list) => born.set(list, now));
            });
        });
        records.forEach((record) => {
            const list = record.target as Element;
            if (record.type !== 'childList' || list.nodeType !== 1 || !list.hasAttribute('data-sigta-motion')) return;
            if (now - (born.get(list) ?? -Infinity) < 400) return;
            record.addedNodes.forEach((node) => {
                if (node.nodeType === 1 && !moved.has(node) && !(node as Element).hasAttribute('data-sigta-leaving')) unfold(node as HTMLElement);
            });
        });
    });
    observer.observe(root, { childList: true, subtree: true });
    return () => observer.disconnect();
}

/* Count — a number that bumps when it changes, rising when it grows and
   dropping when it shrinks. */
export const numberIn = (el: Element): number => parseFloat((el.textContent ?? '').replace(/[^\d.-]/g, ''));

export function bumpCount(el: HTMLElement, rise: boolean): void {
    if (reduced()) return;
    el.animate([
        { translate: `0 ${rise ? '0.45em' : '-0.45em'}`, opacity: 0.2, scale: 1 },
        { translate: '0 0', opacity: 1, scale: 1.2, offset: 0.55 },
        { translate: '0 0', opacity: 1, scale: 1 },
    ], { duration: 440, easing: spring(el) });
}

export function observeCount(el: HTMLElement): Stop {
    let last = numberIn(el);
    const observer = new MutationObserver(() => {
        const value = numberIn(el);
        if (Number.isNaN(value) || value === last) return;
        const rise = value > last;
        last = value;
        bumpCount(el, rise);
    });
    observer.observe(el, { childList: true, characterData: true, subtree: true });
    return () => observer.disconnect();
}

/* Job — a queued or running piece of work whose markup carries its state; a
   burst from the compass when it turns done. */
export function observeJob(el: HTMLElement): Stop {
    let state = el.dataset.state;
    const observer = new MutationObserver(() => {
        const next = el.dataset.state;
        if (next === state) return;
        if (next === 'done') burst(el.querySelector('[data-sigta="loader"]') ?? el, { reach: 12 });
        state = next;
    });
    observer.observe(el, { attributes: true, attributeFilter: ['data-state'] });
    return () => observer.disconnect();
}
