/* ---------------------------------------------------------------------------
   Toasts — a stack driven by `sigta-toast` window events, so anything on the
   page can raise one: a server (Livewire dispatch, Inertia page props), a
   script (Sigta.toast()), an Alpine or Svelte handler ($sigta.toast()).

   An `action` ({ text, event, detail } or { text, onClick }) adds a button —
   "Undo" after a completion — that fires the event and closes the toast. A
   `key` makes a toast replace the one before it with the same key, so ticking
   off five items in a row leaves one toast, not a stack of five.

   The list itself lives in each runtime's own reactive state (Alpine data, a
   Svelte $state); these are the pure operations both apply to it.
--------------------------------------------------------------------------- */
import { isBrowser } from './env.ts';

export interface ToastLink {
    text?: string;
    href: string;
    target?: string;
    rel?: string;
    download?: boolean | string;
    navigate?: boolean;
}

export interface ToastAction {
    text: string;
    event?: string;
    detail?: Record<string, unknown>;
    onClick?: () => void;
}

export interface ToastDetail {
    text?: string;
    heading?: string | null;
    variant?: string | null;
    duration?: number | null;
    link?: ToastLink | null;
    action?: ToastAction | null;
    key?: string | null;
}

export interface Toast {
    id: number;
    key: string | null;
    heading: string | null;
    text: string;
    variant: string;
    link: ToastLink | null;
    action: ToastAction | null;
}

export const TOAST_EVENT = 'sigta-toast';
export const DEFAULT_TOAST_DURATION = 5000;

/** `toast('Saved')`, `toast('Saved', { variant: 'success' })` or `toast({ heading, text, … })`. */
export function toastDetail(messageOrOptions: string | ToastDetail, options?: ToastDetail): ToastDetail {
    if (typeof messageOrOptions === 'string') {
        return options && typeof options === 'object' ? { text: messageOrOptions, ...options } : { text: messageOrOptions };
    }
    return messageOrOptions && typeof messageOrOptions === 'object' ? messageOrOptions : {};
}

/** Raise a toast anywhere on the page. */
export function toast(messageOrOptions: string | ToastDetail, options?: ToastDetail): void {
    if (!isBrowser()) return;
    window.dispatchEvent(new CustomEvent(TOAST_EVENT, { detail: toastDetail(messageOrOptions, options) }));
}

/** The list with `detail` added (replacing a toast with the same key). */
export function pushToast(list: Toast[], detail: ToastDetail, id: number): Toast[] {
    const kept = detail.key ? list.filter((t) => t.key !== detail.key) : list.slice();
    kept.push({
        key: detail.key ?? null,
        id,
        heading: detail.heading ?? null,
        text: detail.text ?? '',
        variant: detail.variant ?? 'info',
        link: detail.link ?? null,
        action: detail.action ?? null,
    });
    return kept;
}

export const dropToast = (list: Toast[], id: number): Toast[] => list.filter((t) => t.id !== id);

/** How long a toast stays, in ms; 0 or less keeps it until dismissed. */
export const toastDuration = (detail: ToastDetail): number => detail.duration ?? DEFAULT_TOAST_DURATION;

/**
 * Run a toast's action. `dispatch` sends a named event the runtime's way
 * (Livewire.dispatch, so #[On] listeners hear it); the default is a window event.
 */
export function runToastAction(
    action: ToastAction | null,
    dispatch: (event: string, detail: Record<string, unknown>) => void = (event, detail) =>
        window.dispatchEvent(new CustomEvent(event, { detail })),
): void {
    if (!action) return;
    if (typeof action.onClick === 'function') action.onClick();
    if (action.event) dispatch(action.event, action.detail ?? {});
}
