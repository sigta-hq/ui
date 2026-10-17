<script lang="ts">
    /* A breadcrumb trail: <nav> around an ordered list of Breadcrumbs.Items. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLElement> {
        children?: Snippet;
    }

    let { children, ...rest }: Props = $props();
</script>

<nav data-sigta="breadcrumbs" aria-label="Breadcrumb" {...rest}>
    <ol data-scope="breadcrumbs" data-part="list">
        {@render children?.()}
    </ol>
</nav>
