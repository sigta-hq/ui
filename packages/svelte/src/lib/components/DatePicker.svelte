<script lang="ts">
    /* Date picker — a text field with a calendar button that opens a
       Calendar in an anchored panel, in the field chrome. Picking a day
       fills the field ("9 Jul 2026") and bind:value (YYYY-MM-DD); a typed
       date within min / max is taken too and shows in the calendar. Escape,
       a click outside or focus leaving closes the panel. */
    import type { HTMLInputAttributes } from 'svelte/elements';
    import { untrack } from 'svelte';
    import { formatDate, isOutOfRange, parseDate } from '@sigta-hq/core';
    import Calendar from './Calendar.svelte';
    import Field from './Field.svelte';
    import Icon from './Icon.svelte';

    interface Props extends Omit<HTMLInputAttributes, 'value' | 'min' | 'max' | 'placeholder' | 'onchange'> {
        label?: string | null;
        description?: string | null;
        error?: string | null;
        name?: string | null;
        value?: string | null;
        min?: string | null;
        max?: string | null;
        placeholder?: string | null;
        today?: string | null;
        disabled?: boolean | null;
        onchange?: (value: string | null) => void;
        class?: string;
    }

    let {
        label = null,
        description = null,
        error = null,
        name = null,
        value = $bindable(null),
        min = null,
        max = null,
        placeholder = 'Select a date',
        today = null,
        disabled = false,
        onchange,
        id,
        class: klass,
        ...rest
    }: Props = $props();

    const uid = $props.id();
    const inputId = $derived(id ?? `sigta-${uid}`);
    const panelId = $derived(`${inputId}-panel`);
    const describedBy = $derived([description ? `${inputId}-description` : '', error ? `${inputId}-error` : ''].filter(Boolean).join(' ') || undefined);

    let open = $state(false);
    let display = $state(untrack(() => formatDate(value)));

    let anchor: HTMLDivElement | undefined = $state();
    let input: HTMLInputElement | undefined = $state();
    let toggleButton: HTMLButtonElement | undefined = $state();
    let panel: HTMLDivElement | undefined = $state();

    // A value set from outside (a reset, a loaded record) shows in the field.
    let shown = untrack(() => value);
    $effect.pre(() => {
        const next = value;
        if (next === shown) return;
        shown = next;
        display = formatDate(next);
    });

    function set(next: string | null) {
        shown = next;
        if (next === value) return;
        value = next;
        onchange?.(next);
    }

    function close(focusInput = false) {
        if (!open) return;
        open = false;
        if (focusInput) input?.focus();
    }

    const toggle = () => (open = !open);

    // A day picked in the calendar, which has already set the (bound) value.
    function pick(iso: string | null) {
        display = formatDate(iso);
        shown = iso;
        onchange?.(iso);
        close(true);
    }

    // The field changed: a date in range is taken (the calendar follows), anything else reverts.
    function typed() {
        const iso = parseDate(display);
        if (iso === null || (iso && isOutOfRange(iso, min, max))) {
            display = formatDate(value);
            return;
        }
        display = formatDate(iso || null);
        set(iso || null);
    }

    // A click outside the panel closes it (the button toggles it itself).
    $effect(() => {
        if (!open) return;
        const listener = (event: MouseEvent) => {
            const target = event.target as Node | null;
            if (!target?.isConnected || panel?.contains(target) || toggleButton?.contains(target)) return;
            close();
        };
        document.addEventListener('click', listener);
        return () => document.removeEventListener('click', listener);
    });

    const keydown = (event: KeyboardEvent) => {
        if (event.key !== 'Escape') return;
        event.preventDefault();
        event.stopPropagation();
        close(true);
    };
    const focusout = (event: FocusEvent) => {
        if (!anchor?.contains(event.relatedTarget as Node | null)) close();
    };
    const inputKeydown = (event: KeyboardEvent) => {
        if (event.key === 'Enter') { event.preventDefault(); input?.blur(); }
        else if (event.key === 'ArrowDown') { event.preventDefault(); if (!open) toggle(); }
    };
</script>

<Field {label} {description} {error} for={inputId}>
    <div class={klass} data-sigta="date-picker">
        <!-- svelte-ignore a11y_no_static_element_interactions -->
        <div bind:this={anchor} onkeydown={keydown} onfocusout={focusout} data-scope="date-picker" data-part="anchor">
            <div data-scope="date-picker" data-part="trigger">
                <input
                    bind:this={input}
                    type="text"
                    id={inputId}
                    role="combobox"
                    aria-haspopup="dialog"
                    aria-controls={panelId}
                    aria-expanded={open ? 'true' : 'false'}
                    bind:value={display}
                    onchange={typed}
                    onkeydown={inputKeydown}
                    placeholder={placeholder ?? undefined}
                    autocomplete="off"
                    data-scope="date-picker"
                    data-part="input"
                    disabled={disabled || undefined}
                    aria-invalid={error ? 'true' : undefined}
                    aria-describedby={describedBy}
                />
                <button bind:this={toggleButton} type="button" tabindex="-1" onclick={toggle} aria-label="Open calendar" data-scope="date-picker" data-part="toggle" disabled={disabled || undefined}>
                    <Icon name="calendar-days" size="sm" />
                </button>
            </div>

            <input type="hidden" value={value ?? ''} name={name ?? undefined} disabled={disabled || undefined} {...rest} />

            <div bind:this={panel} hidden={!open} id={panelId} role="dialog" aria-label="Choose date" data-scope="date-picker" data-part="panel">
                <Calendar bind:value {min} {max} {today} onchange={pick} />
            </div>
        </div>
    </div>
</Field>
