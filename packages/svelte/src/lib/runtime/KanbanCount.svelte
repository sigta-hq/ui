<script lang="ts">
    /* A column's cards, rendered once more inside an inert <template> where
       each card only counts itself (see runtime/kanban.svelte.ts). */
    import type { Snippet } from 'svelte';
    import { untrack } from 'svelte';
    import { setCardCounter, type CardCounter } from './kanban.svelte.js';

    let { counter, children }: { counter: CardCounter; children?: Snippet } = $props();

    // The column hands over one counter for its lifetime.
    setCardCounter(untrack(() => counter));
</script>

<template>{@render children?.()}</template>
