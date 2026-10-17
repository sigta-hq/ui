<script lang="ts">
    /* Stat — a figure: mono label, the value in Fraunces and tabular
       numerals, an optional delta coloured by its trend. panel boxes it
       (label above value); inline is the bare hero-meta figure (value above
       label). tone="inverse" re-inks it for a dark surface. The children
       (a Chart.Sparkline) sit under the figure. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        label?: string | null;
        value?: string | number | null;
        delta?: string | null;
        trend?: 'up' | 'down' | null;
        variant?: 'inline' | 'panel';
        tone?: 'default' | 'inverse';
        children?: Snippet;
    }

    let { label = null, value = null, delta = null, trend = null, variant = 'panel', tone = 'default', children, ...rest }: Props = $props();

    const inline = $derived(variant === 'inline');
    const hasValue = $derived(value !== null && value !== undefined);
    const prefix = $derived(trend === 'up' ? '▲' : trend === 'down' ? '▼' : null);
</script>

<div data-sigta="stat" data-tone={tone} data-variant={variant} {...rest}>
    {#if inline}
        {#if hasValue}
            <span data-scope="stat" data-part="value">{value}</span>
        {/if}
        {#if label}
            <span data-scope="stat" data-part="label">{label}</span>
        {/if}
    {:else}
        {#if label}
            <span data-scope="stat" data-part="label">{label}</span>
        {/if}
        {#if hasValue}
            <span data-scope="stat" data-part="value">{value}</span>
        {/if}
    {/if}
    {#if delta !== null && delta !== undefined}
        <span data-scope="stat" data-part="delta" data-trend={trend ?? undefined}>{#if prefix}<span aria-hidden="true">{prefix}</span>{/if}{prefix ? ` ${delta}` : delta}</span>
    {/if}
    {#if children}
        <div data-scope="stat" data-part="content">{@render children()}</div>
    {/if}
</div>
