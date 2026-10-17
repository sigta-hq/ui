/* ---------------------------------------------------------------------------
   @sigta-hq/svelte/inertia — the server boundary for any backend.

   A backend raises toasts and celebrations by putting a `sigta` prop on the
   Inertia page (shared or flash data — Laravel, Go, anything):

     { "sigta": {
         "toasts": [{ "text": "Saved", "variant": "success" }],
         "celebrate": { "level": "big", "text": "Inbox zero!", "target": "#inbox" }
     } }

   connectInertia() applies it on every visit. It can also drive a named
   <SaveState> from non-GET visits (saving → saved, or failed on validation
   errors), the way Livewire's save state follows wire:target.
--------------------------------------------------------------------------- */
import { router } from '@inertiajs/svelte';
import { celebrate, saveState, toast, type CelebrateLevel, type ToastDetail } from '@sigta-hq/core';

export interface SigtaPageProps {
    toasts?: ToastDetail[] | null;
    celebrate?: { level?: CelebrateLevel; text?: string | false; target?: string | null } | null;
}

export interface ConnectOptions {
    /** The page prop to read (default "sigta"). */
    prop?: string;
    /** Name of a <SaveState> to drive from non-GET visits; true drives the unnamed one. */
    saveState?: string | true;
}

/** Shows what a `sigta` page prop asks for. */
export function applySigtaProps(props: SigtaPageProps | null | undefined): void {
    if (!props) return;
    for (const detail of props.toasts ?? []) toast(detail);
    if (props.celebrate) {
        const { level = 'small', text, target = null } = props.celebrate;
        celebrate(target, level, text === undefined ? {} : { text });
    }
}

type Page = { props: Record<string, unknown> };

/** Connects the Inertia router; returns a function that disconnects it. */
export function connectInertia(options: ConnectOptions = {}): () => void {
    const key = options.prop ?? 'sigta';
    const seen = new WeakSet<object>();
    const apply = (page: Page | undefined) => {
        const props = page?.props?.[key] as SigtaPageProps | undefined;
        if (!props || typeof props !== 'object' || seen.has(props)) return;
        seen.add(props);
        applySigtaProps(props);
    };
    const stops: Array<() => void> = [
        router.on('navigate', (event) => apply((event as CustomEvent<{ page: Page }>).detail.page)),
        router.on('success', (event) => apply((event as CustomEvent<{ page: Page }>).detail.page)),
    ];
    if (options.saveState) {
        const name = options.saveState === true ? null : options.saveState;
        // Only writes save: a visit that changes something (not a GET).
        let saving = false;
        stops.push(
            router.on('start', (event) => {
                const visit = (event as CustomEvent<{ visit: { method: string } }>).detail.visit;
                saving = visit.method !== 'get';
                if (saving) saveState('saving', name);
            }),
            router.on('success', () => { if (saving) saveState('saved', name); saving = false; }),
            router.on('error', () => { if (saving) saveState('failed', name); saving = false; }),
        );
    }
    return () => stops.forEach((stop) => stop());
}
