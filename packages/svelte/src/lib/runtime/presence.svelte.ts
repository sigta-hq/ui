/* ---------------------------------------------------------------------------
   A panel a component shows and hides with `hidden`, faded like the Livewire
   runtime's x-transition.opacity: it fades in as it opens and stays shown
   while it fades out. With reduced motion it shows and hides at once.

     const fade = new Presence(() => open);
     <div bind:this={fade.node} hidden={!fade.shown}>…</div>
--------------------------------------------------------------------------- */
import { untrack } from 'svelte';
import { reduced } from '@sigta-hq/core';

// Alpine's x-transition easing.
const EASE = 'cubic-bezier(0.4, 0, 0.2, 1)';

export class Presence {
    /** Drives `hidden`: true while open and while fading out. */
    shown = $state(false);
    node: HTMLElement | undefined = $state();
    #duration: number;
    #fade: Animation | null = null;

    constructor(open: () => boolean, duration = 100) {
        this.#duration = duration;
        this.shown = untrack(open);
        $effect(() => {
            const show = open();
            untrack(() => this.#toggle(show));
        });
    }

    #toggle(show: boolean): void {
        this.#fade?.cancel();
        this.#fade = null;
        const node = this.node;
        if (!node || reduced() || typeof node.animate !== 'function') {
            this.shown = show;
            return;
        }
        if (show) {
            if (this.shown && !node.hidden) return;
            this.shown = true;
            this.#fade = node.animate([{ opacity: 0 }, { opacity: 1 }], { duration: this.#duration, easing: EASE });
        } else if (this.shown) {
            const fade = node.animate([{ opacity: 1 }, { opacity: 0 }], { duration: this.#duration, easing: EASE, fill: 'forwards' });
            this.#fade = fade;
            fade.onfinish = () => {
                if (this.#fade !== fade) return;
                this.shown = false;
                fade.cancel();
                this.#fade = null;
            };
        }
    }
}
