<script lang="ts">
    /* Input — a native <input> in the field chrome. bind:value for two-way
       binding; every other attribute lands on the input itself. */
    import type { HTMLInputAttributes } from 'svelte/elements';
    import Field from './Field.svelte';
    import Icon from './Icon.svelte';

    interface Props extends Omit<HTMLInputAttributes, 'type'> {
        label?: string | null;
        description?: string | null;
        error?: string | null;
        icon?: string | null;
        type?: HTMLInputAttributes['type'];
        value?: unknown;
    }

    let { label = null, description = null, error = null, icon = null, type = 'text', value = $bindable(), id, ...rest }: Props = $props();

    const uid = $props.id();
    const inputId = $derived(id ?? `sigta-${uid}`);
    const describedBy = $derived([description ? `${inputId}-description` : '', error ? `${inputId}-error` : ''].filter(Boolean).join(' ') || undefined);
</script>

{#snippet control()}
    <input data-sigta="input" {type} id={inputId} aria-invalid={error ? 'true' : undefined} aria-describedby={describedBy} bind:value {...rest} />
{/snippet}

<Field {label} {description} {error} for={inputId}>
    {#if icon}
        <span data-scope="input" data-part="wrap">
            <Icon name={icon} size="sm" data-scope="input" data-part="icon" />
            {@render control()}
        </span>
    {:else}
        {@render control()}
    {/if}
</Field>
