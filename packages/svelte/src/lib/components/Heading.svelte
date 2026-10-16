<script lang="ts">
    /* Heading — Fraunces at the brand scale; the level sets a default size. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLHeadingElement> {
        level?: number | string;
        size?: 'display' | 'xl' | 'lg' | 'base' | 'sm' | null;
        children?: Snippet;
    }

    let { level = 2, size = null, children, ...rest }: Props = $props();

    const n = $derived(Number(level) || 0);
    const tag = $derived(`h${Math.min(Math.max(n, 1), 6)}`);
    const resolved = $derived(size ?? (n === 1 ? 'xl' : n === 2 ? 'lg' : n === 3 ? 'base' : 'sm'));
</script>

<svelte:element this={tag} data-sigta="heading" data-size={resolved} {...rest}>{@render children?.()}</svelte:element>
