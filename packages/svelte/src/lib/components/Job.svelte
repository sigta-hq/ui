<script lang="ts">
    /* Job — one piece of long-running work: the compass draws its circle as
       far round as value, the status line says what is happening now, and
       the moment state turns done the mark completes with a burst. Drive it
       from your data (a poll, a websocket, Inertia props): state and value
       are plain props. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { onMount } from 'svelte';
    import { observeJob, phpNumber, phpRound } from '@sigta-hq/core';
    import ElapsedClock from './ElapsedClock.svelte';
    import Loader from './Loader.svelte';
    import { ensureRuntime } from '../runtime/runtime.js';

    type State = 'queued' | 'working' | 'done' | 'failed';
    interface Props extends HTMLAttributes<HTMLDivElement> {
        heading?: string | null;
        description?: string | null;
        stage?: string | null;
        value?: number | string | null;
        state?: State;
        since?: string | null;
        actions?: Snippet;
        children?: Snippet;
    }

    let { heading = null, description = null, stage = null, value = null, state = 'working', since = null, actions, children, ...rest }: Props = $props();

    const states = ['queued', 'working', 'done', 'failed'];
    const statuses: Record<string, string> = { queued: 'Queued', done: 'Done', failed: 'Failed' };

    const current = $derived((states.includes(state) ? state : 'working') as State);
    const status = $derived(statuses[current] ?? 'Running');
    const percent = $derived.by(() => {
        if (value === null || value === undefined) return null;
        if (current === 'done') return 100;
        const number = Number(value);
        return Math.min(100, Math.max(0, Number.isNaN(number) ? 0 : number));
    });
    const ticking = $derived(!!since && (current === 'queued' || current === 'working'));

    // A plain binding: `state` is a prop here, so the $state rune is not available.
    let root: HTMLDivElement | undefined;

    onMount(() => {
        ensureRuntime();
        return root ? observeJob(root) : undefined;
    });
</script>

<div bind:this={root} data-sigta="job" data-state={current} {...rest}>
    <Loader size="lg" label={heading ?? status} state={current} value={percent} data-scope="job" data-part="loader" />

    <div data-scope="job" data-part="main">
        <div data-scope="job" data-part="header">
            {#if heading}
                <p data-scope="job" data-part="heading">{heading}</p>
            {/if}
            {#if percent !== null}
                <!-- A fixed width, so a growing percentage never nudges the heading. -->
                <span data-scope="job" data-part="percent">{phpNumber(phpRound(percent))}%</span>
            {/if}
        </div>

        <p data-scope="job" data-part="meta">
            <span data-scope="job" data-part="status" aria-live="polite">{status}</span>
            {#if stage}
                <span aria-hidden="true">·</span>
                <span>{stage}</span>
            {/if}
            {#if ticking}
                <span aria-hidden="true">·</span>
                <ElapsedClock {since} />
            {/if}
        </p>

        {#if description}
            <p data-scope="job" data-part="description">{description}</p>
        {/if}

        {@render children?.()}
    </div>

    {#if actions}
        <div data-scope="job" data-part="actions">{@render actions()}</div>
    {/if}
</div>
