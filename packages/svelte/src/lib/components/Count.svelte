<script lang="ts">
    /* Count — a number that bumps when it changes: rising as it grows,
       dropping as it shrinks, whatever changes it. Pass value, or the number
       as children. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { onMount } from 'svelte';
    import { observeCount } from '@sigta-hq/core';

    interface Props extends HTMLAttributes<HTMLSpanElement> {
        value?: number | string | null;
        children?: Snippet;
    }

    let { value = null, children, ...rest }: Props = $props();

    let el: HTMLSpanElement | undefined = $state();

    onMount(() => (el ? observeCount(el) : undefined));
</script>

<span data-sigta="count" bind:this={el} {...rest}>{#if value !== null && value !== undefined}{value}{:else}{@render children?.()}{/if}</span>
