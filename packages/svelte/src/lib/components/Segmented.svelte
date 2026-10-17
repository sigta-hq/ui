<script lang="ts">
    /* Segmented — a strip of radio options (value ⇒ label), zero JS beyond
       the binding. bind:checked={range} holds the chosen value; every
       attribute but class lands on each radio. */
    import type { HTMLInputAttributes } from 'svelte/elements';

    interface Props extends Omit<HTMLInputAttributes, 'type' | 'checked' | 'name'> {
        name: string;
        options?: Record<string, string> | string[];
        label?: string | null;
        checked?: string | number | null;
        class?: string;
    }

    let { name, options = {}, label = null, checked = $bindable(null), class: klass, onchange, ...rest }: Props = $props();

    // Values compare as strings, like the Livewire render.
    const entries = $derived(Object.entries(options ?? {}));
    const isChecked = (value: string) => checked !== null && checked !== undefined && String(checked) === value;

    function change(value: string, event: Event & { currentTarget: EventTarget & HTMLInputElement }) {
        if (event.currentTarget.checked) checked = value;
        onchange?.(event);
    }
</script>

<fieldset class={klass} data-sigta="segmented">
    {#if label}<legend data-scope="segmented" data-part="legend">{label}</legend>{/if}
    <div data-scope="segmented" data-part="group">
        {#each entries as [value, optionLabel] (value)}
            <label data-scope="segmented" data-part="option">
                <input type="radio" {name} {value} checked={isChecked(value)} {...rest} onchange={(event) => change(value, event)} data-scope="segmented" data-part="input" />
                <span data-scope="segmented" data-part="label">{optionLabel}</span>
            </label>
        {/each}
    </div>
</fieldset>
