<script lang="ts">
    /* App shell — the sidebar beside the main column, on raised paper. An
       embeddable rounded frame by default; `full` fills the viewport. An
       optional header snippet (a Shell.Header) spans both columns above them
       and takes over the sidebar's own brand row and burger. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        full?: boolean;
        header?: Snippet;
        sidebar?: Snippet;
        children?: Snippet;
    }

    let { full = false, header, sidebar, children, ...rest }: Props = $props();
</script>

<div data-sigta="shell" data-header={header ? '' : undefined} data-full={full ? '' : undefined} {...rest}>
    {@render header?.()}
    {@render sidebar?.()}
    {@render children?.()}
</div>
