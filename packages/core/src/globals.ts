/* ---------------------------------------------------------------------------
   The public JavaScript API — window.Sigta and the window events — is a
   compatibility contract every runtime keeps:

     Sigta.toast(text | options, options?)       → `sigta-toast`
     Sigta.celebrate(target, level, options)      ← `sigta-celebrate` { level, target, text }
     Sigta.complete(target, commit, options)
     Sigta.leave(target, options)
     Sigta.burst / ring / note / confetti
     Sigta.save(promise, name) / saveState(state, name)  → `sigta-save-state`

   A runtime builds the API (it may wrap complete with its own defaults) and
   installs it once, on the client.
--------------------------------------------------------------------------- */
import { burst, celebrate, confetti, note, ring, type CelebrateLevel } from './celebrate.ts';
import { complete } from './complete.ts';
import { isBrowser } from './env.ts';
import { leave } from './fold.ts';
import { save, saveState } from './save.ts';
import { toast } from './toast.ts';

export const CELEBRATE_EVENT = 'sigta-celebrate';

const baseApi = { toast, complete, leave, celebrate, burst, ring, note, confetti, save, saveState };

export type SigtaApi = typeof baseApi;

export function createApi(overrides: Partial<SigtaApi> = {}): SigtaApi {
    return { ...baseApi, ...overrides };
}

declare global {
    interface Window {
        Sigta?: Partial<SigtaApi> & Record<string, unknown>;
    }
}

/** Installs window.Sigta and the `sigta-celebrate` listener. Returns an uninstaller. */
export function installGlobals(api: SigtaApi): () => void {
    if (!isBrowser()) return () => {};
    window.Sigta = Object.assign(window.Sigta || {}, api);
    const onCelebrate = (event: Event) => {
        const { level = 'small', target = null, text } = ((event as CustomEvent).detail ?? {}) as { level?: CelebrateLevel; target?: string | null; text?: string | false };
        api.celebrate(target, level, text === undefined ? {} : { text });
    };
    window.addEventListener(CELEBRATE_EVENT, onCelebrate);
    return () => window.removeEventListener(CELEBRATE_EVENT, onCelebrate);
}
