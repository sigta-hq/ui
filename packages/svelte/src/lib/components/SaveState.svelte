<script lang="ts">
    /* Save state — the compass stands in for "Saving…": it draws while a save
       is in flight, lands with a handwritten "Saved" (which settles back to
       idle after a moment) and shakes red when a save fails. All labels share
       one cell, so it never moves.

       Drive it with `state` (bind:state), or by name from anywhere:
       sigta.save(promise, name), sigta.saveState('dirty', name), a
       `sigta-save-state` window event, or Inertia visits through
       connectInertia({ saveState: name }). */
    import type { HTMLAttributes } from 'svelte/elements';
    import { onMount } from 'svelte';
    import { isSaveState, saveMark, SAVE_EVENT, SAVED_HOLD, type SaveState } from '@sigta-hq/core';
    import Loader from './Loader.svelte';
    import { ensureRuntime } from '../runtime/runtime.js';

    interface Props extends HTMLAttributes<HTMLSpanElement> {
        name?: string | null;
        state?: SaveState;
        align?: 'start' | 'end';
        idle?: string | null;
        dirty?: string;
        saving?: string;
        saved?: string;
        failed?: string;
    }

    let {
        name = null,
        state = $bindable('idle'),
        align = 'start',
        idle = null,
        dirty = 'Unsaved changes',
        saving = 'Saving…',
        saved = 'Saved',
        failed = 'Not saved',
        ...rest
    }: Props = $props();

    const current = $derived<SaveState>(isSaveState(state) ? state : 'idle');
    const mark = $derived(saveMark(current));

    // "Saved" holds for a moment, then rests — whenever it turns saved after
    // the first render (a first render that is already saved stays, as in Blade).
    let timer: ReturnType<typeof setTimeout> | undefined;
    let first = true;
    $effect(() => {
        const now = current;
        if (first) {
            first = false;
            return;
        }
        clearTimeout(timer);
        if (now === 'saved') timer = setTimeout(() => { state = 'idle'; }, SAVED_HOLD);
    });

    onMount(() => {
        ensureRuntime();
        const listener = (event: Event) => {
            const { name: target = null, state: next } = ((event as CustomEvent).detail ?? {}) as { name?: string | null; state?: unknown };
            if (target === (name || null) && isSaveState(next)) state = next;
        };
        window.addEventListener(SAVE_EVENT, listener);
        return () => {
            window.removeEventListener(SAVE_EVENT, listener);
            clearTimeout(timer);
        };
    });
</script>

<span data-sigta="save-state" data-state={current} data-name={name || undefined} data-align={align} {...rest}>
    <Loader size="sm" decorative state={mark} data-scope="save-state" data-part="loader" />
    <!-- One cell, every label: only the current one is visible. The compass is
         the visible cue while saving; its words are for assistive tech, and
         for anyone who has asked for less motion. -->
    <span role="status" aria-live="polite" data-scope="save-state" data-part="labels">
        <span data-scope="save-state" data-part="label" data-when="dirty">{dirty}</span>
        <span data-scope="save-state" data-part="label" data-when="saving">{saving}</span>
        <span data-scope="save-state" data-part="label" data-when="saved">{saved}</span>
        <span data-scope="save-state" data-part="label" data-when="failed">{failed}</span>
        {#if idle}
            <span data-scope="save-state" data-part="label" data-when="idle">{idle}</span>
        {/if}
    </span>
</span>
