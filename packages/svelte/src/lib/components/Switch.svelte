<script lang="ts">
    /* Switch — a real checkbox (role="switch") behind a drawn track; the knob
       squashes toward its travel while pressed. bind:checked for the state;
       every attribute but class lands on the native <input>. */
    import type { HTMLInputAttributes } from 'svelte/elements';

    interface Props extends Omit<HTMLInputAttributes, 'type'> {
        label?: string | null;
        checked?: boolean;
        class?: string;
    }

    let { label = null, checked = $bindable(false), class: klass, ...rest }: Props = $props();
</script>

<label data-sigta="switch" class={klass}>
    <input type="checkbox" role="switch" data-scope="switch" data-part="input" bind:checked {...rest} />
    <span aria-hidden="true" data-scope="switch" data-part="track"></span>
    {#if label}
        <span>{label}</span>
    {/if}
</label>
