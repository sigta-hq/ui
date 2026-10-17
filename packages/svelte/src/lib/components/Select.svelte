<script lang="ts">
    /* Select — a native <select> in the field chrome with a chevron. The
       options are children; bind:value for two-way binding; every other
       attribute lands on the select itself. */
    import type { Snippet } from 'svelte';
    import type { HTMLSelectAttributes } from 'svelte/elements';
    import Field from './Field.svelte';
    import Icon from './Icon.svelte';

    interface Props extends HTMLSelectAttributes {
        label?: string | null;
        description?: string | null;
        error?: string | null;
        value?: unknown;
        children?: Snippet;
    }

    let { label = null, description = null, error = null, value = $bindable(), id, children, ...rest }: Props = $props();

    const uid = $props.id();
    const selectId = $derived(id ?? `sigta-${uid}`);
    const describedBy = $derived([description ? `${selectId}-description` : '', error ? `${selectId}-error` : ''].filter(Boolean).join(' ') || undefined);
</script>

<Field {label} {description} {error} for={selectId}>
    <span data-scope="select" data-part="wrap">
        <select data-sigta="select" id={selectId} aria-invalid={error ? 'true' : undefined} aria-describedby={describedBy} bind:value {...rest}>{@render children?.()}</select>
        <Icon name="chevron-down" size="sm" data-scope="select" data-part="icon" />
    </span>
</Field>
