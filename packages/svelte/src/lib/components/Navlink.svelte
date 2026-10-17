<script lang="ts">
    /* A navigation link: mono, graphite to ink, with an underline that grows
       on hover; the current page keeps it drawn (aria-current="page"). */
    import type { Snippet } from 'svelte';
    import type { HTMLAnchorAttributes } from 'svelte/elements';

    interface Props extends HTMLAnchorAttributes {
        current?: boolean;
        children?: Snippet;
    }

    let { current = false, children, ...rest }: Props = $props();
</script>

<a data-sigta="navlink" aria-current={current ? 'page' : undefined} {...rest}>{@render children?.()}</a>
