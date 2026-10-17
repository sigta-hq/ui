<script lang="ts">
    /* Listbox — a rich single select: a button trigger in the field chrome
       opens a floating listbox of <Autocomplete.Option>s. bind:value holds
       the chosen value; a hidden input carries it (and name) for plain form
       posts. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { onMount } from 'svelte';
    import { serialize } from '@sigta-hq/core';
    import { Combobox } from '../runtime/combobox.svelte.js';
    import Field from './Field.svelte';
    import Icon from './Icon.svelte';

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

    let { label = null, description = null, error = null, placeholder = 'Select…', name = null, value = $bindable(null), onchange, id, class: klass, children, ...rest }: Props = $props();

    const uid = $props.id();
    const triggerId = $derived(id ?? `sigta-${uid}`);
    const listboxId = $derived(`${triggerId}-listbox`);
    const describedBy = $derived([description ? `${triggerId}-description` : '', error ? `${triggerId}-error` : ''].filter(Boolean).join(' ') || undefined);

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

    // The placeholder until the runtime starts (the options register after
    // the trigger renders), then the chosen option's text — as Livewire does.
    let mounted = $state(false);
    onMount(() => { mounted = true; });
    const chosen = $derived(mounted && box.value ? box.labelFor(box.value as string) : null);

    let root: HTMLDivElement | undefined = $state();
    const focusout = (event: FocusEvent) => {
        if (!root?.contains(event.relatedTarget as Node | null)) box.closeList();
    };
    const keydown = (event: KeyboardEvent) => {
        if (event.key === 'Escape') { event.preventDefault(); box.closeList(); }
        else if (event.key === 'ArrowDown') { event.preventDefault(); box.move(1); }
        else if (event.key === 'ArrowUp') { event.preventDefault(); box.move(-1); }
        else if (event.key === 'Enter') { event.preventDefault(); if (box.open) box.chooseActive(); else box.openList(); }
    };
    // A click outside the picker closes the list.
    const outside = (event: MouseEvent) => {
        if (box.open && !root?.contains(event.target as Node | null)) box.closeList();
    };
</script>

<svelte:document onclick={outside} />

<Field {label} {description} {error} for={triggerId}>
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div bind:this={root} onkeydown={keydown} onfocusout={focusout} class={klass} data-sigta="listbox">
        <button
            type="button"
            bind:this={box.anchor}
            role="combobox"
            aria-haspopup="listbox"
            aria-controls={listboxId}
            aria-expanded={box.open ? 'true' : 'false'}
            aria-activedescendant={box.activeId ?? undefined}
            onclick={() => (box.open ? box.closeList() : box.openList())}
            data-scope="listbox"
            data-part="trigger"
            id={triggerId}
            aria-invalid={error ? 'true' : undefined}
            aria-describedby={describedBy}
        >
            <span data-empty={chosen === null ? '' : undefined} data-scope="listbox" data-part="value">{chosen ?? placeholder}</span>
            <span aria-hidden="true" data-scope="listbox" data-part="chevron">
                <Icon name="chevron-down" size="sm" />
            </span>
        </button>

        <input type="hidden" name={name ?? undefined} value={serialize(box.value)} {...rest} />

        <div bind:this={box.panel} hidden={!box.open} id={listboxId} role="listbox" data-scope="listbox" data-part="listbox">
            {@render children?.()}
        </div>
    </div>
</Field>
