/* ---------------------------------------------------------------------------
   Sigta's own strings (aria labels, empty states) in the app's language —
   the Svelte twin of Blade's __(). Messages are keyed by the English text,
   exactly like a Laravel lang/<locale>.json, so one file can serve both
   runtimes:

     <script>  // the root layout
         import { setMessages } from '@sigta-hq/svelte';
         import da from '../lang/da.json';
         setMessages(da);
     </script>

   Held in context, never in a module-level store, so SSR requests in
   different languages never see each other's messages.
--------------------------------------------------------------------------- */
import { getContext, setContext } from 'svelte';

export type Messages = Record<string, string>;

const KEY = Symbol('sigta-messages');

/** Provide translations for every Sigta component below this one. */
export function setMessages(messages: Messages): void {
    setContext(KEY, messages);
}

/** A component's translator: the message for an English text, else the text. Call during component init. */
export function translator(): (text: string) => string {
    const messages = getContext<Messages | undefined>(KEY);
    return (text) => messages?.[text] ?? text;
}
