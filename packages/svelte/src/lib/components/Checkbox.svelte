<script lang="ts">
    /* Checkbox — the tick is drawn as two pen strokes. bind:checked for the
       state; with a label it renders a label row and every other attribute
       lands on the native <input>. */
    import type { HTMLInputAttributes } from 'svelte/elements';

    interface Props extends Omit<HTMLInputAttributes, 'type'> {
        label?: string | null;
        checked?: boolean;
        class?: string;
    }

    let { label = null, checked = $bindable(false), class: klass, ...rest }: Props = $props();
</script>

{#if label}
    <label data-sigta="checkbox" class={klass}>
        <input type="checkbox" data-sigta-check="" bind:checked {...rest} />
        <span>{label}</span>
    </label>
{:else}
    <input data-sigta="checkbox" type="checkbox" data-sigta-check="" bind:checked class={klass} {...rest} />
{/if}
