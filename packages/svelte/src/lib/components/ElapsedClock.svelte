<script lang="ts">
    /* Elapsed clock — HH:MM:SS counting up from since (an ISO string; unset
       means now), ticking once a second on the client. Presentational: the
       timer's persistence stays in your app. */
    import type { HTMLAttributes } from 'svelte/elements';
    import { onMount } from 'svelte';
    import { elapsedStart, formatElapsed } from '@sigta-hq/core';

    interface Props extends HTMLAttributes<HTMLSpanElement> {
        since?: string | null;
    }

    let { since = null, ...rest }: Props = $props();

    // The server (and the first client render) shows 00:00:00, like the
    // Livewire render before Alpine starts; the clock runs once mounted.
    let now: number | null = $state(null);
    let mountedAt = $state(0);

    const started = $derived(elapsedStart(since, mountedAt));
    const label = $derived(now === null ? '00:00:00' : formatElapsed(now - started));

    onMount(() => {
        mountedAt = Date.now();
        now = mountedAt;
        const timer = setInterval(() => { now = Date.now(); }, 1000);
        return () => clearInterval(timer);
    });
</script>

<span data-sigta="elapsed-clock" {...rest}>{label}</span>
