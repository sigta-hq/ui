<script lang="ts">
    /* Panel — a bordered surface that does not clip, so overhanging notes
       show; inverse is a dark ink band. Children stack as a padded flex
       column: a padding class replaces the padding, an unprefixed display
       class (flex, grid, block) replaces the stack, a direction class alone
       (flex-row) turns it. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { ownsDisplay, ownsPadding } from '@sigta-hq/core';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        variant?: 'default' | 'inverse';
        children?: Snippet;
    }

    let { variant = 'default', children, class: klass, ...rest }: Props = $props();
</script>

<div
    data-sigta="panel"
    data-variant={variant}
    data-padding={ownsPadding(klass) ? 'custom' : undefined}
    data-layout={ownsDisplay(klass) ? 'custom' : undefined}
    class={klass}
    {...rest}
>{@render children?.()}</div>
