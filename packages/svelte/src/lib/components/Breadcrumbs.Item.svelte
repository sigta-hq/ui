<script lang="ts">
    /* One crumb: a link, or the current page (no href, or current) as ink
       text with aria-current. Attributes land on the link / current span. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLElement> {
        href?: string | null;
        current?: boolean;
        children?: Snippet;
    }

    let { href = null, current = false, children, ...rest }: Props = $props();

    const isCurrent = $derived(current || href === null || href === undefined);
</script>

<li data-sigta="breadcrumbs.item">
    {#if isCurrent}
        <span aria-current="page" data-scope="breadcrumbs.item" data-part="current" {...rest}>{@render children?.()}</span>
    {:else}
        <a {href} data-scope="breadcrumbs.item" data-part="link" {...rest}>{@render children?.()}</a>
    {/if}
</li>
