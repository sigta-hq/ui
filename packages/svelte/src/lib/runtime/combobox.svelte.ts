/* ---------------------------------------------------------------------------
   The combobox behind Autocomplete, Select (rich), Command and Pillbox — the
   Svelte twin of the Livewire runtime's sigtaCombobox, on the same core
   logic: options register themselves, a query filters them by their text,
   the active option moves with the arrow keys and is announced through
   aria-activedescendant, choosing sets (or toggles) the value.
--------------------------------------------------------------------------- */
import { getContext, setContext, tick } from 'svelte';
import { choose, isChosen, matchesQuery, positionPanel, step } from '@sigta-hq/core';

export interface ComboboxOption {
    id: string;
    /** The option's value: its value prop, else its text. */
    value(): string;
    /** The text shown back in the query box when chosen. */
    label(): string;
    /** The text a query is matched against. */
    text(): string;
    element(): HTMLElement | undefined;
}

const KEY = Symbol('sigta-combobox');

export class Combobox {
    open = $state(false);
    query = $state('');
    /** The query as last typed: options filter by this, not by query, so a
        choice written back into the box does not hide the other options. */
    applied = $state('');
    activeId: string | null = $state(null);
    options: ComboboxOption[] = $state([]);
    value: string | string[] | null = $state(null);
    multiple: boolean;
    anchor: HTMLElement | undefined = $state();
    panel: HTMLElement | undefined = $state();
    onchange: (value: string | string[] | null) => void;

    constructor(options: { multiple?: boolean; value?: string | string[] | null; onchange?: (value: string | string[] | null) => void } = {}) {
        this.multiple = options.multiple ?? false;
        this.value = options.value ?? (this.multiple ? [] : null);
        this.onchange = options.onchange ?? (() => {});
        setContext(KEY, this);
    }

    register(option: ComboboxOption): () => void {
        this.options.push(option);
        return () => { this.options = this.options.filter((o) => o !== option); };
    }

    isVisible(option: ComboboxOption): boolean {
        return matchesQuery(option.text(), this.applied);
    }

    get visible(): ComboboxOption[] {
        return this.options.filter((o) => this.isVisible(o));
    }

    isSelected(value: string): boolean {
        return isChosen(this.value, value);
    }

    labelFor(value: string): string {
        return this.options.find((o) => o.value() === value)?.label() ?? value;
    }

    setActive(option: ComboboxOption | null): void {
        this.activeId = option?.id ?? null;
        option?.element()?.scrollIntoView?.({ block: 'nearest' });
    }

    async reposition(): Promise<void> {
        await tick();
        positionPanel(this.anchor, this.panel);
    }

    filter(): void {
        this.applied = this.query;
        this.setActive(null);
        this.open = true;
        this.reposition();
    }

    openList(): void {
        this.open = true;
        if (!this.activeId) this.setActive(this.visible[0] ?? null);
        this.reposition();
    }

    closeList(): void {
        this.open = false;
        this.setActive(null);
    }

    move(delta: number): void {
        const visible = this.visible;
        if (!visible.length) return;
        this.open = true;
        const index = visible.findIndex((o) => o.id === this.activeId);
        this.setActive(visible[step(index, delta, visible.length)]);
        this.reposition();
    }

    chooseActive(): void {
        const option = this.options.find((o) => o.id === this.activeId);
        if (option) this.choose(option);
    }

    choose(option: ComboboxOption): void {
        this.value = choose(this.value, option.value(), this.multiple);
        if (!this.multiple) {
            this.query = option.label();
            this.closeList();
        }
        this.onchange(this.value);
        // A chip added or removed can wrap the anchor onto another row.
        this.reposition();
    }

    deselect(value: string): void {
        if (!this.multiple || !Array.isArray(this.value)) return;
        this.value = this.value.filter((v) => v !== value);
        this.onchange(this.value);
        this.reposition();
    }
}

export const useCombobox = (): Combobox => getContext(KEY);
