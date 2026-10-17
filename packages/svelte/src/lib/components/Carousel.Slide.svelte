<script lang="ts">
    /* One carousel slide, snapping to the start of the track. Full width by
       default; a basis (a width utility: basis-4/5, w-1/2) replaces it. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        basis?: string | null;
        children?: Snippet;
    }

    let { basis = 'w-full', class: klass, children, ...rest }: Props = $props();

    const custom = $derived(basis !== 'w-full');
</script>

<div
    data-sigta="carousel.slide"
    role="group"
    aria-roledescription="slide"
    data-basis={custom ? 'custom' : undefined}
    class={[custom && basis, klass]}
    {...rest}
>
    {@render children?.()}
</div>
