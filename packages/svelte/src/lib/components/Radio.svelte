<script lang="ts">
    /* Radio — a round box that fills with ink. bind:group={choice} with a
       value per radio (or a plain checked); with a label it renders a label
       row and every other attribute lands on the native <input>. */
    import type { HTMLInputAttributes } from 'svelte/elements';

    interface Props extends Omit<HTMLInputAttributes, 'type'> {
        label?: string | null;
        checked?: boolean;
        group?: unknown;
        class?: string;
    }

    let { label = null, checked = false, group = $bindable(), value, class: klass, onchange, ...rest }: Props = $props();

    // Bound to a group, the group decides; otherwise the checked prop does.
    const on = $derived(group !== undefined && group !== null ? group === value : checked);

    function change(event: Event & { currentTarget: EventTarget & HTMLInputElement }) {
        if (event.currentTarget.checked) group = value;
        onchange?.(event);
    }
</script>

{#if label}
    <label data-sigta="radio" class={klass}>
        <input type="radio" data-scope="radio" data-part="control" {...rest} {value} checked={on} onchange={change} />
        <span>{label}</span>
    </label>
{:else}
    <input data-sigta="radio" type="radio" class={klass} {...rest} {value} checked={on} onchange={change} />
{/if}
