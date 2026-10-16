/* ---------------------------------------------------------------------------
   The core's fold as Svelte transitions, so a row Svelte removes (a keyed
   {#each} item that left the data) folds away exactly like a Livewire row
   the server dropped:

     {#each tasks as task (task.id)}
         <li out:leave>…</li>
     {/each}

   Rows added to a [data-sigta-motion] list unfold in by themselves (the
   runtime watches the list), so no in: transition is needed there.
--------------------------------------------------------------------------- */
import { fold, foldDuration, reduced, unfold, unfoldDuration } from '@sigta-hq/core';

export function leave(node: HTMLElement): { duration: number } {
    // Already folded (Sigta.complete folds before it commits): nothing left to animate.
    if (reduced() || node.hasAttribute('data-sigta-leaving')) return { duration: 0 };
    node.setAttribute('data-sigta-leaving', '');
    const duration = foldDuration(node);
    fold(node);
    return { duration };
}

export function arrive(node: HTMLElement): { duration: number } {
    if (reduced()) return { duration: 0 };
    const duration = unfoldDuration(node);
    unfold(node);
    return { duration };
}
