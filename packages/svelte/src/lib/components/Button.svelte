<script lang="ts">
    /* The action control. An <a> with an href, a <button> otherwise. Busy:
       pass the in-flight flag as `loading`; the compass is written in once
       it outlasts a beat (the runtime watches data-loading), and stands in
       for a leading icon. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { onMount } from 'svelte';
    import { markLeadIcon } from '@sigta-hq/core';
    import Loader from './Loader.svelte';
    import { ensureRuntime } from '../runtime/runtime.js';

    interface Props extends HTMLAttributes<HTMLElement> {
        variant?: 'primary' | 'secondary' | 'ghost' | 'cta' | 'danger' | 'inverse' | 'ghost-inverse';
        size?: 'sm' | 'base';
        loading?: boolean | null;
        href?: string | null;
        type?: 'button' | 'submit' | 'reset';
        target?: string;
        rel?: string;
        disabled?: boolean;
        children?: Snippet;
        [key: string]: unknown;
    }

    let { variant = 'secondary', size = 'base', loading = undefined, href = null, type = 'button', target, rel, children, ...rest }: Props = $props();

    let element: HTMLButtonElement | undefined = $state();
    // Busy at first render shows at once (a button you drive yourself);
    // turning busy later waits a beat, which the runtime times.
    let busyNow = $state(loading === true);
    const canBusy = $derived((loading !== undefined && loading !== null) || type === 'submit');

    $effect(() => {
        if (!loading) busyNow = false;
    });

    onMount(() => {
        ensureRuntime();
        if (element && canBusy) markLeadIcon(element);
    });
</script>

{#if href}
    <a data-sigta="button" data-variant={variant} data-size={size} {href} {target} rel={rel ?? (target === '_blank' ? 'noopener' : undefined)} {...rest}>{@render children?.()}</a>
{:else}
    <button
        bind:this={element}
        data-sigta="button"
        {type}
        data-variant={variant}
        data-size={size}
        data-loading={loading ? 'true' : undefined}
        data-sigta-busy={busyNow ? '' : undefined}
        aria-busy={loading ? 'true' : undefined}
        {...rest}
    >
        {#if canBusy}
            <span data-scope="button" data-part="busy" aria-hidden="true"><Loader size="xs" decorative /></span>
            <span data-scope="button" data-part="label">{@render children?.()}</span>
        {:else}
            {@render children?.()}
        {/if}
    </button>
{/if}
