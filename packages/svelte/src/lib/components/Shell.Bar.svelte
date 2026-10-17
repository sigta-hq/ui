<script lang="ts">
    /* A page's top bar in the shell: heading + mono sub on the left, the
       actions snippet on the right. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLElement> {
        heading?: string | null;
        sub?: string | null;
        actions?: Snippet;
    }

    let { heading = null, sub = null, actions, ...rest }: Props = $props();
</script>

<header data-sigta="shell.bar" {...rest}>
    <div data-scope="shell.bar" data-part="start">
        {#if heading}
            <h3 data-scope="shell.bar" data-part="heading">{heading}</h3>
        {/if}
        {#if sub}
            <span data-scope="shell.bar" data-part="sub">{sub}</span>
        {/if}
    </div>

    {#if actions}
        <div data-scope="shell.bar" data-part="actions">{@render actions()}</div>
    {/if}
</header>
