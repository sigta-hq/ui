<script lang="ts">
    /* Time picker — a text field with a clock button that opens a listbox of
       times every `step` minutes between min and max, in the field chrome.
       bind:value stays 24h HH:MM whatever the label format; typed times
       within min / max are taken too. Arrows move the active time (wrapping),
       Enter picks it, Escape or focus leaving closes the list. The list is
       the core's timeOptions(), the same the Livewire runtime renders. */
    import type { HTMLInputAttributes } from 'svelte/elements';
    import { tick, untrack } from 'svelte';
    import { activeFor, formatTime, isTimeOutOfRange, moveActive, normalizeTime, parseTime, timeOptions, type TimeOption } from '@sigta-hq/core';
    import Field from './Field.svelte';
    import Icon from './Icon.svelte';

    interface Props extends Omit<HTMLInputAttributes, 'value' | 'min' | 'max' | 'step' | 'placeholder' | 'onchange'> {
        label?: string | null;
        description?: string | null;
        error?: string | null;
        name?: string | null;
        value?: string | null;
        min?: string | null;
        max?: string | null;
        step?: number | string;
        format?: '24' | '12';
        placeholder?: string | null;
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
        step = 30,
        format = '24',
        placeholder = 'Select a time',
        disabled = false,
        onchange,
        id,
        class: klass,
        ...rest
    }: Props = $props();

    const uid = $props.id();
    const inputId = $derived(id ?? `sigta-${uid}`);
    const listId = $derived(`${inputId}-listbox`);
    const describedBy = $derived([description ? `${inputId}-description` : '', error ? `${inputId}-error` : ''].filter(Boolean).join(' ') || undefined);

    // "9:30" reads as 09:30, as the Livewire runtime normalizes it.
    const chosen = $derived(normalizeTime(value));
    const bounds = $derived({ min: normalizeTime(min), max: normalizeTime(max) });
    const options = $derived(timeOptions({ ...bounds, step, format }));

    let open = $state(false);
    let activeIndex = $state(-1);
    let display = $state(untrack(() => formatTime(chosen, format)));

    let input: HTMLInputElement | undefined = $state();
    let list: HTMLUListElement | undefined = $state();

    // A value set from outside (a reset, a loaded record) shows in the field.
    let shown = untrack(() => value);
    $effect.pre(() => {
        const next = value;
        if (next === shown) return;
        shown = next;
        display = formatTime(untrack(() => chosen), format);
    });

    function set(next: string | null) {
        shown = next;
        if (next === value) return;
        value = next;
        onchange?.(next);
    }

    function close(focus = false) {
        open = false;
        activeIndex = -1;
        if (focus) input?.focus();
    }

    async function setActive(index: number) {
        if (index < 0) return;
        activeIndex = index;
        await tick();
        list?.children[index]?.scrollIntoView?.({ block: 'nearest' });
    }

    async function openList() {
        open = true;
        await tick();
        setActive(activeFor(options, chosen));
    }

    const toggle = () => (open ? close() : openList());

    function move(delta: number) {
        if (!open) return openList();
        setActive(moveActive(activeIndex, delta, options.length));
    }

    function choose(option: TimeOption) {
        display = option.label;
        set(option.value);
        close(true);
    }

    // The field changed: a time in range is taken, an emptied field clears, anything else reverts.
    function typed() {
        const next = parseTime(display);
        if (next === null || (next && isTimeOutOfRange(next, bounds.min, bounds.max))) {
            display = formatTime(chosen, format);
            return;
        }
        display = formatTime(next || null, format);
        set(next || null);
    }

    const keydown = (event: KeyboardEvent) => {
        if (event.key !== 'Escape') return;
        event.preventDefault();
        event.stopPropagation();
        close(true);
    };
    const focusout = (event: FocusEvent) => {
        if (!(event.currentTarget as HTMLElement).contains(event.relatedTarget as Node | null)) close();
    };
    const inputKeydown = (event: KeyboardEvent) => {
        if (event.key === 'ArrowDown') { event.preventDefault(); move(1); }
        else if (event.key === 'ArrowUp') { event.preventDefault(); move(-1); }
        else if (event.key === 'Enter') {
            event.preventDefault();
            if (open) { const option = options[activeIndex]; if (option) choose(option); }
            else openList();
        }
    };
</script>

<Field {label} {description} {error} for={inputId}>
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div onkeydown={keydown} onfocusout={focusout} class={klass} data-sigta="time-picker">
        <div data-scope="time-picker" data-part="trigger">
            <input
                bind:this={input}
                type="text"
                id={inputId}
                role="combobox"
                aria-haspopup="listbox"
                aria-controls={listId}
                aria-expanded={open ? 'true' : 'false'}
                bind:value={display}
                onclick={openList}
                onchange={typed}
                onkeydown={inputKeydown}
                placeholder={placeholder ?? undefined}
                autocomplete="off"
                data-scope="time-picker"
                data-part="input"
                disabled={disabled || undefined}
                aria-invalid={error ? 'true' : undefined}
                aria-describedby={describedBy}
            />
            <button type="button" tabindex="-1" onclick={toggle} aria-label="Open time list" data-scope="time-picker" data-part="toggle" disabled={disabled || undefined}>
                <Icon name="clock" size="sm" />
            </button>
        </div>

        <input type="hidden" value={chosen ?? ''} name={name ?? undefined} disabled={disabled || undefined} {...rest} />

        <ul bind:this={list} hidden={!open} id={listId} role="listbox" aria-label={label ?? 'Times'} data-scope="time-picker" data-part="listbox">
            {#each options as option, index (option.value)}
                <li role="presentation">
                    <!-- svelte-ignore a11y_no_interactive_element_to_noninteractive_role -->
                    <button
                        type="button"
                        role="option"
                        data-value={option.value}
                        aria-selected={chosen === option.value ? 'true' : 'false'}
                        data-active={activeIndex === index ? '' : undefined}
                        tabindex="-1"
                        onclick={() => choose(option)}
                        onmousemove={() => (activeIndex = index)}
                        data-scope="time-picker"
                        data-part="option"
                    >{option.label}</button>
                </li>
            {/each}
        </ul>
    </div>
</Field>
