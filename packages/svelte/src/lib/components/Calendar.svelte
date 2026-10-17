<script lang="ts">
    /* Calendar — a Monday-first month grid: 42 day buttons (six weeks,
       spilling into the months around), month navigation, the WAI-ARIA
       date-grid keys and min / max bounds, on the core's date logic (the
       same the Livewire runtime uses). bind:value (YYYY-MM-DD) and
       onchange; a pick also dispatches the bubbling calendar-change DOM
       event, as the Livewire runtime does. Without a value it opens on
       today: the `today` prop, else the current date. */
    import type { HTMLInputAttributes } from 'svelte/elements';
    import { tick, untrack } from 'svelte';
    import {
        WEEKDAYS,
        canShowNext,
        canShowPrev,
        formatDate,
        initialFocus,
        isOutOfRange,
        keyTarget,
        monthGrid,
        monthOf,
        monthTitle,
        parseIso,
        shiftMonth,
        todayIso,
        type MonthView,
    } from '@sigta-hq/core';
    import Icon from './Icon.svelte';

    interface Props extends Omit<HTMLInputAttributes, 'value' | 'min' | 'max' | 'onchange'> {
        name?: string | null;
        value?: string | null;
        min?: string | null;
        max?: string | null;
        today?: string | null;
        onchange?: (value: string | null) => void;
        class?: string;
    }

    let { name = null, value = $bindable(null), min = null, max = null, today = null, onchange, id, class: klass, ...rest }: Props = $props();

    const uid = $props.id();
    const titleId = $derived(`${id ?? `sigta-${uid}`}-title`);
    const todayDate = $derived(today ?? todayIso());
    const bounds = $derived({ selected: value, min, max, today: todayDate });

    // The month shown and the day holding the tab stop start from the value
    // (else today), like the server render of the Livewire runtime.
    const opening = (): MonthView => monthOf(parseIso(value) ? value! : todayDate);
    let view: MonthView = $state(untrack(opening));
    let focused: string = $state(untrack(() => initialFocus(opening(), bounds)));

    const cells = $derived(monthGrid(view.year, view.month, bounds));
    const title = $derived(monthTitle(view.year, view.month));
    const canPrev = $derived(canShowPrev(view, min));
    const canNext = $derived(canShowNext(view, max));

    let root: HTMLDivElement | undefined = $state();
    let grid: HTMLDivElement | undefined = $state();

    // A value set from outside (a typed date in the picker, a reset) shows its month.
    let shown = untrack(() => value);
    $effect.pre(() => {
        const next = value;
        if (next === shown) return;
        shown = next;
        untrack(() => {
            if (parseIso(next)) view = monthOf(next!);
            focused = initialFocus(view, bounds);
        });
    });

    function showMonth(delta: number) {
        view = shiftMonth(view, delta);
        focused = initialFocus(view, bounds);
    }

    function select(iso: string) {
        if (isOutOfRange(iso, min, max)) return;
        shown = iso;
        value = iso;
        view = monthOf(iso);
        focused = iso;
        onchange?.(iso);
        root?.dispatchEvent(new CustomEvent('calendar-change', { bubbles: true, detail: { value: iso, label: formatDate(iso) } }));
    }

    async function focusDate(iso: string) {
        focused = iso;
        view = monthOf(iso);
        await tick();
        grid?.querySelector<HTMLElement>(`[data-date="${iso}"]`)?.focus();
    }

    // Arrows, Home / End and PageUp / PageDown move the focus; a day out of range is refused.
    function keydown(event: KeyboardEvent) {
        const target = keyTarget(focused, event.key);
        if (target === null) return;
        event.preventDefault();
        if (!isOutOfRange(target, min, max)) focusDate(target);
    }
</script>

<div bind:this={root} class={klass} data-sigta="calendar">
    <div data-scope="calendar" data-part="header">
        <button type="button" onclick={() => canPrev && showMonth(-1)} disabled={!canPrev} aria-label="Previous month" data-scope="calendar" data-part="prev">
            <Icon name="chevron-left" size="sm" />
        </button>
        <div id={titleId} aria-live="polite" data-scope="calendar" data-part="title">{title}</div>
        <button type="button" onclick={() => canNext && showMonth(1)} disabled={!canNext} aria-label="Next month" data-scope="calendar" data-part="next">
            <Icon name="chevron-right" size="sm" />
        </button>
    </div>

    <!-- svelte-ignore a11y_interactive_supports_focus -->
    <div bind:this={grid} role="grid" aria-labelledby={titleId} onkeydown={keydown} data-scope="calendar" data-part="grid">
        <div role="row" data-scope="calendar" data-part="weekdays">
            {#each WEEKDAYS as weekday (weekday.abbr)}
                <span role="columnheader" aria-label={weekday.name} data-scope="calendar" data-part="weekday">{weekday.abbr}</span>
            {/each}
        </div>

        {#each [0, 1, 2, 3, 4, 5] as row (row)}
            <div role="row" data-scope="calendar" data-part="week">
                {#each cells.slice(row * 7, row * 7 + 7) as cell}
                    <button
                        type="button"
                        role="gridcell"
                        data-date={cell.date}
                        aria-label={cell.label}
                        aria-selected={cell.selected ? 'true' : 'false'}
                        tabindex={cell.date === focused ? 0 : -1}
                        aria-current={cell.today ? 'date' : undefined}
                        disabled={cell.disabled}
                        onclick={() => select(cell.date)}
                        data-scope="calendar"
                        data-part="day"
                    >{cell.day}</button>
                {/each}
            </div>
        {/each}
    </div>

    <input type="hidden" value={value ?? ''} name={name ?? undefined} {...rest} />
</div>
