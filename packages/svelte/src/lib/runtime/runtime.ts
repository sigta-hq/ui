/* ---------------------------------------------------------------------------
   The Svelte runtime: the same page-wide behavior the Livewire runtime
   installs — window.Sigta, the sigta-celebrate listener, loaders that land,
   busy buttons that wait a beat, motion lists that unfold new rows — set up
   once, on the client, by the first Sigta component that mounts (or by
   calling ensureRuntime() in a root layout).
--------------------------------------------------------------------------- */
import {
    createApi,
    installGlobals,
    isBrowser,
    observeBusy,
    observeLoaders,
    observeMotionLists,
    type SigtaApi,
} from '@sigta-hq/core';

/** The same API as window.Sigta / Alpine's $sigta: toast, complete, celebrate, save, … */
export const sigta: SigtaApi = createApi();

// A flag, not a store: it is only ever set inside a browser.
let installed = false;

export function ensureRuntime(): void {
    if (installed || !isBrowser()) return;
    installed = true;
    installGlobals(sigta);
    observeLoaders();
    observeBusy();
    let ready = false;
    requestAnimationFrame(() => requestAnimationFrame(() => { ready = true; }));
    observeMotionLists(document.documentElement, () => ready);
}
