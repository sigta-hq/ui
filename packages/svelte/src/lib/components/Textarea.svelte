<script lang="ts">
    /* Textarea — a native <textarea> in the field chrome. bind:value for
       two-way binding; every other attribute lands on the textarea itself. */
    import type { HTMLTextareaAttributes } from 'svelte/elements';
    import Field from './Field.svelte';

    interface Props extends HTMLTextareaAttributes {
        label?: string | null;
        description?: string | null;
        error?: string | null;
        rows?: number;
        value?: string | null;
    }

    let { label = null, description = null, error = null, rows = 4, value = $bindable(), id, ...rest }: Props = $props();

    const uid = $props.id();
    const textareaId = $derived(id ?? `sigta-${uid}`);
    const describedBy = $derived([description ? `${textareaId}-description` : '', error ? `${textareaId}-error` : ''].filter(Boolean).join(' ') || undefined);
</script>

<Field {label} {description} {error} for={textareaId}>
    <textarea data-sigta="textarea" {rows} id={textareaId} aria-invalid={error ? 'true' : undefined} aria-describedby={describedBy} bind:value {...rest}></textarea>
</Field>
