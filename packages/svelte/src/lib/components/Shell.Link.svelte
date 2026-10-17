<script lang="ts">
    /* A shell nav link: mono, quiet graphite; `current` inverts it to ink and
       marks it aria-current="page". */
    import type { Snippet } from 'svelte';
    import type { HTMLAnchorAttributes } from 'svelte/elements';
    import Icon from './Icon.svelte';

    interface Props extends HTMLAnchorAttributes {
        icon?: string | null;
        current?: boolean;
        children?: Snippet;
    }

    let { icon = null, current = false, children, ...rest }: Props = $props();
</script>

<a data-sigta="shell.link" aria-current={current ? 'page' : undefined} {...rest}>
    {#if icon}<Icon name={icon} size="sm" data-scope="shell.link" data-part="icon" />{/if}
    {@render children?.()}
</a>
