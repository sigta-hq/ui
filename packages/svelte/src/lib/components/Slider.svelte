<script lang="ts">
    /* Slider — a native range input in the field chrome. bind:value for
       two-way binding; min, max, step … land on the input. showValue prints
       the live value beside it. */
    import type { HTMLInputAttributes } from 'svelte/elements';
    import Field from './Field.svelte';

    interface Props extends Omit<HTMLInputAttributes, 'type'> {
        label?: string | null;
        description?: string | null;
        error?: string | null;
        showValue?: boolean;
        value?: number | string | null;
    }

    let { label = null, description = null, error = null, showValue = false, value = $bindable(), id, ...rest }: Props = $props();

    const uid = $props.id();
    const inputId = $derived(id ?? `sigta-${uid}`);
    const describedBy = $derived([description ? `${inputId}-description` : '', error ? `${inputId}-error` : ''].filter(Boolean).join(' ') || undefined);
</script>

{#snippet control()}
    <input data-sigta="slider" type="range" id={inputId} aria-invalid={error ? 'true' : undefined} aria-describedby={describedBy} bind:value {...rest} />
{/snippet}

<Field {label} {description} {error} for={inputId}>
    {#if showValue}
        <span data-scope="slider" data-part="wrap">
            {@render control()}
            <output for={inputId} data-scope="slider" data-part="value">{value ?? ''}</output>
        </span>
    {:else}
        {@render control()}
    {/if}
</Field>
