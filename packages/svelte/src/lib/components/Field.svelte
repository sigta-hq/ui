<script lang="ts">
    /* Field — a label, the control, then its description or error. Wraps any
       control; Input, Select, Textarea… render their chrome through it. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import Label from './Label.svelte';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        label?: string | null;
        description?: string | null;
        error?: string | null;
        for?: string | null;
        children?: Snippet;
    }

    let { label = null, description = null, error = null, for: forId = null, children, ...rest }: Props = $props();

    const uid = $props.id();
    const target = $derived(forId ?? `sigta-${uid}`);
    const chrome = $derived(!!(label || description || error || Object.keys(rest).length));
</script>

{#if chrome}
    <div data-sigta="field" {...rest}>
        {#if label}
            <Label for={target}>{label}</Label>
        {/if}

        {@render children?.()}

        {#if description}
            <p id="{target}-description" data-scope="field" data-part="description">{description}</p>
        {/if}

        {#if error}
            <p id="{target}-error" data-scope="field" data-part="error">{error}</p>
        {/if}
    </div>
{:else}
    {@render children?.()}
{/if}
