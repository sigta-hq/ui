<script lang="ts">
    /* Pillbox — a multi-select combobox over <Autocomplete.Option>s: the
       chosen values sit as removable chips beside the query box. bind:value
       holds the array; a hidden input carries it as JSON (and name) for
       plain form posts. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { serialize } from '@sigta-hq/core';
    import { Combobox } from '../runtime/combobox.svelte.js';
    import Icon from './Icon.svelte';

    interface Props extends Omit<HTMLAttributes<HTMLInputElement>, 'onchange'> {
        label?: string | null;
        description?: string | null;
        error?: string | null;
        placeholder?: string | null;
        name?: string | null;
        value?: string[] | null;
        onchange?: (value: string[]) => void;
        class?: string;
        children?: Snippet;
    }

    let { label = null, description = null, error = null, placeholder = 'Add…', name = null, value = $bindable([]), onchange, id, class: klass, children, ...rest }: Props = $props();

    const uid = $props.id();
    const inputId = $derived(id ?? `sigta-${uid}`);
    const listboxId = $derived(`${inputId}-listbox`);

    const box = new Combobox({
        multiple: true,
        value,
        onchange: (chosen) => {
            value = chosen as string[];
            onchange?.(value);
        },
    });

    // A value set from outside (a reset, a loaded record) reaches the combobox.
    // Compared by content: state re-wraps an array, so identity never settles.
    const same = (a: unknown, b: unknown) => JSON.stringify(a ?? []) === JSON.stringify(b ?? []);
    $effect.pre(() => {
        if (!same(box.value, value)) box.value = [...(value ?? [])];
    });

    const chips = $derived(Array.isArray(box.value) ? box.value : []);

    let root: HTMLDivElement | undefined = $state();
    const focusout = (event: FocusEvent) => {
        if (!root?.contains(event.relatedTarget as Node | null)) box.closeList();
    };
    const keydown = (event: KeyboardEvent) => {
        if (event.key === 'Escape') { event.preventDefault(); box.closeList(); }
    };
    const inputKeydown = (event: KeyboardEvent) => {
        if (event.key === 'ArrowDown') { event.preventDefault(); box.move(1); }
        else if (event.key === 'ArrowUp') { event.preventDefault(); box.move(-1); }
        else if (event.key === 'Enter') { event.preventDefault(); box.chooseActive(); }
    };
</script>

<!-- svelte-ignore a11y_no_static_element_interactions -->
<div bind:this={root} onkeydown={keydown} onfocusout={focusout} class={klass} data-sigta="pillbox">
    {#if label}
        <label for={inputId} data-scope="pillbox" data-part="label">{label}</label>
    {/if}

    <div bind:this={box.anchor} data-scope="pillbox" data-part="control">
        {#each chips as chip (chip)}
            <span data-scope="pillbox" data-part="chip">
                <span>{box.labelFor(chip)}</span>
                <button type="button" onclick={() => box.deselect(chip)} aria-label={`Remove ${box.labelFor(chip)}`} data-scope="pillbox" data-part="remove">
                    <Icon name="x" size="xs" />
                </button>
            </span>
        {/each}

        <input
            type="text"
            id={inputId}
            role="combobox"
            aria-autocomplete="list"
            aria-controls={listboxId}
            aria-expanded={box.open ? 'true' : 'false'}
            aria-activedescendant={box.activeId ?? undefined}
            bind:value={box.query}
            oninput={() => box.filter()}
            onclick={() => box.openList()}
            onkeydown={inputKeydown}
            placeholder={placeholder ?? undefined}
            aria-invalid={error ? 'true' : undefined}
            autocomplete="off"
            data-scope="pillbox"
            data-part="input"
        />
    </div>

    <!-- Empty until something is chosen, like the Livewire render. -->
    <input type="hidden" name={name ?? undefined} value={chips.length ? serialize(chips) : ''} {...rest} />

    <div bind:this={box.panel} hidden={!box.open} id={listboxId} role="listbox" aria-multiselectable="true" data-scope="pillbox" data-part="listbox">
        {@render children?.()}
        <div hidden={box.visible.length > 0} data-scope="pillbox" data-part="empty">No matches.</div>
    </div>

    {#if description && !error}
        <p data-scope="pillbox" data-part="description">{description}</p>
    {/if}
    {#if error}
        <p data-scope="pillbox" data-part="error">{error}</p>
    {/if}
</div>
