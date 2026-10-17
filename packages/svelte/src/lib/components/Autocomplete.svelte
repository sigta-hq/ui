<script lang="ts">
    /* Autocomplete — a filtering combobox over <Autocomplete.Option>s. The
       query box is for typing; the chosen value lives in bind:value (and a
       hidden input carrying name, for plain form posts). */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { serialize } from '@sigta-hq/core';
    import { Combobox } from '../runtime/combobox.svelte.js';

    interface Props extends Omit<HTMLAttributes<HTMLInputElement>, 'onchange'> {
        label?: string | null;
        description?: string | null;
        error?: string | null;
        placeholder?: string | null;
        name?: string | null;
        value?: string | null;
        onchange?: (value: string | null) => void;
        class?: string;
        children?: Snippet;
    }

    let { label = null, description = null, error = null, placeholder = null, name = null, value = $bindable(null), onchange, id, class: klass, children, ...rest }: Props = $props();

    const uid = $props.id();
    const inputId = $derived(id ?? `sigta-${uid}`);
    const listboxId = $derived(`${inputId}-listbox`);

    const box = new Combobox({
        value,
        onchange: (chosen) => {
            value = chosen as string | null;
            onchange?.(value);
        },
    });

    // A value set from outside (a reset, a loaded record) reaches the combobox.
    $effect.pre(() => {
        if (box.value !== value) box.value = value;
    });

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
<div bind:this={root} onkeydown={keydown} onfocusout={focusout} class={klass} data-sigta="autocomplete">
    {#if label}
        <label for={inputId} data-scope="autocomplete" data-part="label">{label}</label>
    {/if}

    <input
        type="text"
        id={inputId}
        bind:this={box.anchor}
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
        data-scope="autocomplete"
        data-part="input"
    />

    <input type="hidden" name={name ?? undefined} value={serialize(box.value)} {...rest} />

    <div bind:this={box.panel} hidden={!box.open} id={listboxId} role="listbox" data-scope="autocomplete" data-part="listbox">
        {@render children?.()}
        <div hidden={box.visible.length > 0} data-scope="autocomplete" data-part="empty">No matches.</div>
    </div>

    {#if description && !error}
        <p data-scope="autocomplete" data-part="description">{description}</p>
    {/if}
    {#if error}
        <p data-scope="autocomplete" data-part="error">{error}</p>
    {/if}
</div>
