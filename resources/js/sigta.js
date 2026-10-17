/* ---------------------------------------------------------------------------
   Sigta UI — Livewire runtime

   The Livewire adapter's behavior: Alpine components for the interactive
   Blade components, plus the Livewire glue (morph hooks, message
   interception, data-loading, Livewire.dispatch). The behavior both runtimes
   share — motion, toasts, celebrate, busy timing, positioning — lives in
   @sigta-hq/core (packages/core/src) and is bundled in.

   Alpine ships with Livewire, so this file waits for `alpine:init`; it is a
   no-op on pages without Livewire/Alpine. Built into dist/sigta.js by
   scripts/build-livewire.js — never edit dist/sigta.js.
--------------------------------------------------------------------------- */
import {
    choose,
    clearFloatingSpace,
    createApi,
    dropToast,
    leaveToast,
    TOAST_LEAVE_MS,
    installGlobals,
    isChosen,
    isSaveState,
    leave,
    matchesQuery,
    observeBusy,
    observeCount,
    observeJob,
    observeLoaders,
    observeMotionLists,
    positionPanel,
    publishFloatingSpace,
    pushToast,
    revive,
    complete as completeRow,
    rowOf,
    runToastAction,
    saveMark,
    SAVED_HOLD,
    SAVE_EVENT,
    serialize,
    step,
    toastDuration,
} from '../../packages/core/src/index.ts';
// The calendar, date picker and time picker's date and time logic.
import {
    activeFor,
    canShowNext,
    canShowPrev,
    formatDate,
    formatTime,
    initialFocus,
    isOutOfRange,
    isTimeOutOfRange,
    keyTarget,
    monthGrid,
    monthOf,
    monthTitle,
    moveActive,
    parseDate,
    parseTime,
    shiftMonth,
    timeOptions,
    todayIso,
} from '../../packages/core/src/index.ts';

// Rows the server dropped in a morph but that were kept on screen to leave.
const dropped = new WeakSet();

/* Complete — inside Livewire the commit goes out at once and the row leaves
   only if the server drops it; elsewhere the row folds first, then commit
   runs. defer: true folds first even inside Livewire (an Alpine x-for list
   within a component).

     <sigta:checkbox x-on:change="$sigta.complete($el, () => $wire.complete({{ $task->id }}))" /> */
const complete = (target, commit = null, options = {}) => {
    const row = rowOf(target, '[wire\\:key]');
    const atOnce = options.defer === undefined ? !!row?.closest('[wire\\:id]') : !options.defer;
    return completeRow(target, commit, {
        ...options,
        atOnce,
        rowSelector: '[wire\\:key]',
        onCommitted: (committed) => (dropped.has(committed) ? leave(committed) : undefined),
    });
};

const api = createApi({ complete });

document.addEventListener('alpine:init', () => {
    const Alpine = window.Alpine;
    if (!Alpine) return;

    /* Every component declares each field it writes: Alpine stores an
       undeclared `this.x = …` on the nearest ancestor scope that has an `x`,
       so a save state inside a modal once renamed the modal and it never opened. */

    /* Dropdown / popover — open state + roving focus over menu items. */
    Alpine.data('sigtaDropdown', () => ({
        open: false,
        toggle() {
            this.open = !this.open;
            if (this.open) this.$nextTick(() => this.focusItem(0));
        },
        // Arrow keys on the trigger open the menu on its first (0) or last (-1)
        // item, per the WAI-ARIA menu button pattern.
        openAt(index) {
            this.open = true;
            this.$nextTick(() => this.focusItem(index));
        },
        close(focusTrigger = false) {
            if (!this.open) return;
            this.open = false;
            if (focusTrigger) this.$refs.trigger?.querySelector('button, [href], input')?.focus?.() ?? this.$refs.trigger?.focus?.();
        },
        items() {
            // $root, not $el — items() is called from child handlers (item clicks,
            // panel keydowns) where $el is the firing element, not the dropdown root.
            return Array.from(this.$root.querySelectorAll('[role="menuitem"]:not([aria-disabled="true"])'));
        },
        focusItem(index) {
            const items = this.items();
            if (!items.length) return;
            items[((index % items.length) + items.length) % items.length].focus();
        },
        move(delta) {
            const items = this.items();
            const current = items.indexOf(document.activeElement);
            this.focusItem(current === -1 ? (delta > 0 ? 0 : -1) : current + delta);
        },
    }));

    /* Shell sidebar — a rail on desktop, a slide-in drawer below the tablet
       breakpoint. `drawer` says which one it currently is, so the overlay
       behaviour (focus trap, scroll lock, dialog semantics) applies only while
       it is actually a drawer. */
    Alpine.data('sigtaShellSidebar', () => ({
        open: false,
        drawer: false,
        init() {
            const narrow = window.matchMedia('(width < 48rem)');
            this.drawer = narrow.matches;
            // Growing past the breakpoint turns the drawer back into the rail, so a
            // drawer left open has to be released rather than stranding its trap.
            narrow.addEventListener('change', (event) => {
                this.drawer = event.matches;
                if (!event.matches) this.open = false;
            });
            // A shell header's burger lives outside this component, so it is told
            // the state rather than reading it.
            this.$watch('open', (open) => {
                window.dispatchEvent(new CustomEvent('sigta-shell-nav', { detail: { open } }));
            });
        },
        toggle() { this.open = !this.open; },
        close() { this.open = false; },
    }));

    /* Floating bar — follows its data-show attribute (a Livewire morph updates it
       in place) and publishes its footprint so the page and toasts keep clear. */
    Alpine.data('sigtaFloatingBar', () => ({
        visible: false,
        showObserver: null,
        sizeObserver: null,
        init() {
            this.visible = this.$root.dataset.show === 'true';
            this.showObserver = new MutationObserver(() => {
                this.visible = this.$root.dataset.show === 'true';
            });
            this.showObserver.observe(this.$root, { attributes: true, attributeFilter: ['data-show'] });
            this.sizeObserver = new ResizeObserver(() => this.publish());
            this.sizeObserver.observe(this.$refs.bar);
            this.$watch('visible', () => this.$nextTick(() => this.publish()));
            this.publish();
        },
        destroy() {
            this.showObserver?.disconnect();
            this.sizeObserver?.disconnect();
            clearFloatingSpace();
        },
        publish() {
            publishFloatingSpace(this.$root, this.$refs.bar, this.visible);
        },
    }));

    /* Modal — thin wrapper over native <dialog> (focus trap + Esc for free). */
    Alpine.data('sigtaModal', (name = null) => ({
        name,
        show() { this.$refs.dialog?.showModal(); },
        hide() { this.$refs.dialog?.close(); },
        matches(detail) {
            return !detail || detail.name === undefined || detail.name === null || detail.name === this.name;
        },
        onShow(detail) { if (this.matches(detail)) this.show(); },
        onHide(detail) { if (this.matches(detail)) this.hide(); },
        backdropClick(event) { if (event.target === this.$refs.dialog) this.hide(); },
    }));

    /* Tabs — active panel + arrow-key roving tabindex per APG. */
    Alpine.data('sigtaTabs', (active = null) => ({
        active,
        init() {
            if (!this.active) this.active = this.tabs()[0]?.dataset.name ?? null;
        },
        tabs() {
            return Array.from(this.$root.querySelectorAll('[role="tab"]'));
        },
        select(name) { this.active = name; },
        isActive(name) { return this.active === name; },
        move(delta) {
            const tabs = this.tabs();
            const i = tabs.findIndex((t) => t.dataset.name === this.active);
            const next = tabs[step(i, delta, tabs.length)];
            if (next) {
                this.active = next.dataset.name;
                next.focus();
            }
        },
        // Home / End: the first (0) or last (-1) tab.
        moveTo(index) {
            const tabs = this.tabs();
            const next = tabs[index < 0 ? tabs.length + index : index];
            if (next) {
                this.active = next.dataset.name;
                next.focus();
            }
        },
    }));

    /* Combobox — shared by Select (rich), Autocomplete, Command, Pillbox.
       Options are DOM elements with [role="option"] and data-value; filtering
       toggles `hidden`; the active option is tracked via aria-activedescendant
       so screen readers follow without moving focus off the input. */
    Alpine.data('sigtaCombobox', (config = {}) => ({
        open: false,
        query: '',
        value: config.multiple ? (config.initial ?? []) : (config.initial ?? null),
        multiple: config.multiple ?? false,
        activeId: null,
        // How many options the last query kept (null until one is typed): the
        // "no matches" line reads it, since Alpine cannot watch the options' DOM.
        matches: null,

        init() {
            // Reposition the panel whenever it opens (see positionPanel).
            this.$watch('open', (open) => {
                if (open) this.$nextTick(() => this.positionPanel());
            });
        },

        // $root, not $el: methods here are invoked from child elements (an
        // option's mousemove, the input's keydown), and Alpine's $el is the
        // element the handler fired on — $root is always the combobox root, so
        // querying/clearing spans every option, not just the one that fired.
        options() {
            return Array.from(this.$root.querySelectorAll('[role="option"]'));
        },

        // Float the listbox so it escapes any ancestor overflow. Needs
        // x-ref="anchor" (the input/trigger) and x-ref="panel" (the listbox); a
        // no-op without them (e.g. the command palette, which lives in a dialog).
        positionPanel() {
            positionPanel(this.$refs.anchor, this.$refs.panel);
        },
        visible() {
            return this.options().filter((o) => !o.hidden);
        },
        filter() {
            this.options().forEach((o) => {
                o.hidden = !matchesQuery(o.textContent, this.query);
            });
            this.matches = this.visible().length;
            this.setActive(null);
            this.open = true;
            // Filtering changes the panel height — re-anchor it.
            this.$nextTick(() => this.positionPanel());
        },
        openList() {
            this.open = true;
            if (!this.activeId) this.setActive(this.visible()[0] ?? null);
        },
        closeList() {
            this.open = false;
            this.setActive(null);
        },
        setActive(el) {
            this.options().forEach((o) => delete o.dataset.active);
            if (el) {
                el.dataset.active = 'true';
                this.activeId = el.id;
                el.scrollIntoView({ block: 'nearest' });
            } else {
                this.activeId = null;
            }
        },
        activeEl() {
            return this.options().find((o) => o.id === this.activeId) ?? null;
        },
        move(delta) {
            const opts = this.visible();
            if (!opts.length) return;
            this.open = true;
            this.setActive(opts[step(opts.indexOf(this.activeEl()), delta, opts.length)]);
        },
        chooseActive() {
            const el = this.activeEl();
            if (el) this.choose(el);
        },
        choose(el) {
            const value = el.dataset.value ?? el.textContent.trim();
            const label = (el.dataset.label ?? el.textContent).trim();
            this.value = choose(this.value, value, this.multiple);
            if (!this.multiple) {
                this.query = label;
                this.closeList();
            }
            this.syncInput();
        },
        deselect(value) {
            if (!this.multiple) return;
            this.value = this.value.filter((v) => v !== value);
            this.syncInput();
        },
        isSelected(value) {
            return isChosen(this.value, value);
        },
        labelFor(value) {
            const el = this.options().find((o) => (o.dataset.value ?? o.textContent.trim()) === value);
            return el ? (el.dataset.label ?? el.textContent).trim() : value;
        },
        syncInput() {
            const input = this.$refs.value;
            if (!input) return;
            input.value = serialize(this.value);
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        },
    }));

    /* Toast stack — driven by `sigta-toast` window events. From Livewire:
       Sigta::toast('…') or $this->dispatch('sigta-toast', heading: '…', variant: 'success').
       An action's event goes through Livewire when it is on the page, so
       #[On] listeners hear it. */
    Alpine.data('sigtaToasts', () => ({
        toasts: [],
        counter: 0,
        add(detail = {}) {
            const id = ++this.counter;
            this.toasts = pushToast(this.toasts, detail, id);
            const duration = toastDuration(detail);
            if (duration > 0) setTimeout(() => this.remove(id), duration);
        },
        remove(id) {
            // It slides off (data-leaving) before it leaves the list.
            this.toasts = leaveToast(this.toasts, id);
            setTimeout(() => { this.toasts = dropToast(this.toasts, id); }, TOAST_LEAVE_MS);
        },
        act(toast) {
            runToastAction(toast.action, (event, detail) => {
                if (window.Livewire?.dispatch) window.Livewire.dispatch(event, detail);
                else window.dispatchEvent(new CustomEvent(event, { detail }));
            });
            this.remove(toast.id);
        },
    }));

    /* Count — a number that bumps when it changes (a morph, an x-text, a
       plain write), rising when it grows and dropping when it shrinks. */
    Alpine.data('sigtaCount', () => ({
        stop: null,
        init() {
            this.stop = observeCount(this.$root);
        },
        destroy() {
            this.stop?.();
        },
    }));

    /* Save state — the compass stands in for "Saving…". It follows Livewire by
       itself — data-target names the actions and/or properties to watch on the
       closest component — or listens for sigta-save-state events: $sigta.save(promise). */
    Alpine.data('sigtaSaveState', () => ({
        state: 'idle',
        name: null,
        timer: null,
        component: null,
        onEvent: null,
        onInput: null,
        stopFollowing: null,
        init() {
            this.state = this.$root.dataset.state || 'idle';
            this.name = this.$root.dataset.name || null;
            this.onEvent = (event) => {
                const { name = null, state } = event.detail ?? {};
                if (name === this.name) this.set(state);
            };
            window.addEventListener(SAVE_EVENT, this.onEvent);
            this.followLivewire();
        },
        destroy() {
            window.removeEventListener(SAVE_EVENT, this.onEvent);
            this.stopFollowing?.();
            this.component?.removeEventListener('input', this.onInput);
            clearTimeout(this.timer);
        },
        get mark() {
            return saveMark(this.state);
        },
        set(state) {
            if (!isSaveState(state)) return;
            clearTimeout(this.timer);
            this.state = state;
            if (state === 'saved') this.timer = setTimeout(() => { this.state = 'idle'; }, SAVED_HOLD);
        },
        followLivewire() {
            const targets = (this.$root.dataset.target || '').split(',').map((t) => t.trim()).filter(Boolean);
            this.component = this.$root.closest('[wire\\:id]');
            const id = this.component?.getAttribute('wire:id');
            if (!targets.length || !id || !window.Livewire?.interceptMessage) return;
            const watched = (path) => targets.some((t) => path === t || path.startsWith(`${t}.`));
            const touches = (message) =>
                (message.calls ?? []).some((call) => targets.includes(call.method)) ||
                Object.keys(message.updates ?? {}).some(watched);
            // Typing into a watched wire:model marks the work unsaved until it syncs.
            this.onInput = (event) => {
                const model = event.target.getAttributeNames?.().find((attribute) => attribute.startsWith('wire:model'));
                if (model && watched(event.target.getAttribute(model)) && this.state !== 'saving') this.set('dirty');
            };
            this.component.addEventListener('input', this.onInput);
            this.stopFollowing = window.Livewire.interceptMessage(({ message, onSuccess, onError, onFailure, onCancel }) => {
                if (message.component?.id !== id || !touches(message)) return;
                this.set('saving');
                onSuccess(({ payload } = {}) => {
                    let snapshot = payload?.snapshot ?? {};
                    try { if (typeof snapshot === 'string') snapshot = JSON.parse(snapshot); } catch (e) { snapshot = {}; }
                    const errors = snapshot?.memo?.errors ?? {};
                    this.set(Object.keys(errors).length ? 'failed' : 'saved');
                });
                onError(() => this.set('failed'));
                onFailure(() => this.set('failed'));
                onCancel(() => this.set('idle'));
            });
        },
    }));

    /* Job — a queued or running piece of work whose markup carries its state
       (a poll or a morph updates data-state); a burst when it turns done. */
    Alpine.data('sigtaJob', () => ({
        stop: null,
        init() {
            this.stop = observeJob(this.$root);
        },
        destroy() {
            this.stop?.();
        },
    }));

    /* Calendar — the Monday-first month grid over 42 server-rendered day
       buttons (the count never changes, so a morph never adds or drops one).
       The view, the tab stop and the selection start from the server render
       (config); the date math is the core's, shared with the Svelte twin.
       `today` is the pinned today prop, else the browser's own date. The
       grid is a pure function of its inputs, so one is built per change
       (memoized) rather than once per day button binding. */
    const calendarGrids = new Map();
    const calendarGrid = (year, month, bounds) => {
        const key = [year, month, bounds.selected, bounds.min, bounds.max, bounds.today].join('|');
        if (!calendarGrids.has(key)) {
            if (calendarGrids.size > 64) calendarGrids.clear();
            calendarGrids.set(key, monthGrid(year, month, bounds));
        }
        return calendarGrids.get(key);
    };

    Alpine.data('sigtaCalendar', (config = {}) => ({
        viewYear: config.year,
        viewMonth: config.month,
        selected: config.value ?? null,
        focused: config.focused,
        min: config.min ?? null,
        max: config.max ?? null,
        today: config.today ?? null,
        get view() {
            return { year: this.viewYear, month: this.viewMonth };
        },
        bounds() {
            return { selected: this.selected, min: this.min, max: this.max, today: this.today ?? todayIso() };
        },
        get title() {
            return monthTitle(this.viewYear, this.viewMonth);
        },
        get canPrev() {
            return canShowPrev(this.view, this.min);
        },
        get canNext() {
            return canShowNext(this.view, this.max);
        },
        get cells() {
            return calendarGrid(this.viewYear, this.viewMonth, this.bounds());
        },
        isDisabled(iso) {
            return isOutOfRange(iso, this.min, this.max);
        },
        goTo(iso) {
            const { year, month } = monthOf(iso);
            this.viewYear = year;
            this.viewMonth = month;
        },
        showMonth(delta) {
            const { year, month } = shiftMonth(this.view, delta);
            this.viewYear = year;
            this.viewMonth = month;
            this.focused = initialFocus(this.view, this.bounds());
        },
        prevMonth() {
            if (this.canPrev) this.showMonth(-1);
        },
        nextMonth() {
            if (this.canNext) this.showMonth(1);
        },
        select(iso) {
            if (this.isDisabled(iso)) return;
            this.selected = iso;
            this.goTo(iso);
            this.focused = iso;
            this.$nextTick(() => this.sync());
        },
        // A value set from outside (the date picker's typed date), shown as a server-rendered one would be.
        setValue(iso) {
            this.selected = iso || null;
            if (this.selected) this.goTo(this.selected);
            this.focused = initialFocus(this.view, this.bounds());
        },
        sync() {
            const el = this.$refs.value;
            if (el) {
                el.value = this.selected ?? '';
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
            this.$dispatch('calendar-change', { value: this.selected, label: formatDate(this.selected) });
        },
        focusDate(iso) {
            this.focused = iso;
            this.goTo(iso);
            this.$nextTick(() => this.$refs.grid.querySelector(`[data-date="${iso}"]`)?.focus());
        },
        // Arrows, Home / End and PageUp / PageDown move the focus (WAI-ARIA date grid); a day out of range is refused.
        key(event) {
            const target = keyTarget(this.focused, event.key);
            if (target === null) return;
            event.preventDefault();
            if (!this.isDisabled(target)) this.focusDate(target);
        },
    }));

    /* Date picker — the typed text and the Y-m-d value around a calendar in
       an anchored panel (open / close is sigtaDropdown's, one scope in).
       Typed dates are parsed by the core and must fall within min / max; the
       hidden input carries name / wire:model and hears input + change. */
    Alpine.data('sigtaDatePicker', (config = {}) => ({
        value: config.value ?? null,
        display: formatDate(config.value),
        min: config.min ?? null,
        max: config.max ?? null,
        commit() {
            const hidden = this.$refs.hidden;
            hidden.value = this.value ?? '';
            hidden.dispatchEvent(new Event('input', { bubbles: true }));
            hidden.dispatchEvent(new Event('change', { bubbles: true }));
        },
        // A day picked in the calendar (its calendar-change).
        pick(detail) {
            this.value = detail.value;
            this.display = detail.label;
            this.commit();
        },
        // The text field changed: a date in range is taken (and shown in the calendar), anything else reverts.
        typed() {
            const iso = parseDate(this.display);
            if (iso === null || (iso && isOutOfRange(iso, this.min, this.max))) {
                this.display = formatDate(this.value);
            } else {
                this.value = iso || null;
                this.display = formatDate(this.value);
                const calendar = this.$root.querySelector('[data-sigta="calendar"]');
                if (calendar) Alpine.$data(calendar).setValue(this.value);
            }
            this.commit();
        },
    }));

    /* Time picker — a combobox over a listbox of times every `step` minutes
       between min and max (the core's list, the same the server rendered).
       The value is HH:MM (24h) whatever the label format; typed times are
       parsed by the core and must fall within min / max. */
    Alpine.data('sigtaTimePicker', (config = {}) => ({
        open: false,
        value: config.value ?? null,
        display: formatTime(config.value, config.format),
        format: String(config.format ?? '24'),
        min: config.min ?? null,
        max: config.max ?? null,
        opts: timeOptions(config),
        activeIndex: -1,
        toggle() {
            this.open ? this.close() : this.openList();
        },
        openList() {
            this.open = true;
            this.$nextTick(() => this.setActive(activeFor(this.opts, this.value)));
        },
        close(focus = false) {
            this.open = false;
            this.activeIndex = -1;
            if (focus) this.$refs.input.focus();
        },
        setActive(index) {
            if (index < 0) return;
            this.activeIndex = index;
            this.$nextTick(() => this.$refs.list?.children[this.activeIndex]?.scrollIntoView({ block: 'nearest' }));
        },
        move(delta) {
            if (!this.open) return this.openList();
            this.setActive(moveActive(this.activeIndex, delta, this.opts.length));
        },
        chooseActive() {
            const option = this.opts[this.activeIndex];
            if (option) this.choose(option.value, option.label);
        },
        choose(value, label) {
            this.value = value;
            this.display = label;
            this.commit();
            this.close(true);
        },
        commit() {
            const hidden = this.$refs.hidden;
            hidden.value = this.value ?? '';
            hidden.dispatchEvent(new Event('input', { bubbles: true }));
            hidden.dispatchEvent(new Event('change', { bubbles: true }));
        },
        // The text field changed: a time in range is taken, an emptied field clears, anything else reverts.
        typed() {
            const value = parseTime(this.display);
            if (value === null || (value && isTimeOutOfRange(value, this.min, this.max))) {
                this.display = formatTime(this.value, this.format);
                return;
            }
            this.value = value || null;
            this.display = formatTime(this.value, this.format);
            this.commit();
        },
    }));

    /* Page-wide watchers from the core: loaders land, busy buttons wait a
       beat, rows added to a motion list after the page settles unfold in. */
    observeLoaders();
    observeBusy();
    let ready = false;
    document.addEventListener('alpine:initialized', () => requestAnimationFrame(() => { ready = true; }));
    observeMotionLists(document.documentElement, () => ready);

    /* Livewire keeps rows in motion lists alive long enough to leave. A row
       the server drops would be removed outright by the morph, so just before
       the morph it is written back into the incoming HTML beside a neighbour
       that stays (or, if none stays, held back from the removal), then folded
       away once the morph is done. A row mid-completion keeps its optimistic
       flags through the morph the same way. */
    if (window.Livewire?.hook) {
        const keyOf = (el) => el?.getAttribute?.('wire:key') || el?.id || null;
        const lookup = (root, key) =>
            root.querySelector(`[wire\\:key="${CSS.escape(key)}"]`) ?? root.querySelector(`[id="${CSS.escape(key)}"]`);
        const held = new WeakSet();
        const leaving = new Set();
        const undone = new Set();

        // Writes the row back into the incoming HTML beside its nearest
        // neighbour that survives (earlier rows are placed first, so a run of
        // dropped rows keeps its order). The whitespace between them goes
        // along too: the morph walks text nodes as well, and a missing one
        // makes it move the row, which restarts the row's animations.
        const between = (first, last) => {
            const nodes = [];
            for (let node = first.nextSibling; node && node !== last; node = node.nextSibling) nodes.push(node.cloneNode(true));
            return nodes;
        };
        const rehome = (row, toEl) => {
            for (let sibling = row.previousElementSibling; sibling; sibling = sibling.previousElementSibling) {
                const key = keyOf(sibling);
                const anchor = key && lookup(toEl, key);
                if (anchor) {
                    anchor.after(...between(sibling, row), row.cloneNode(true));
                    return true;
                }
            }
            for (let sibling = row.nextElementSibling; sibling; sibling = sibling.nextElementSibling) {
                const key = keyOf(sibling);
                const anchor = key && lookup(toEl, key);
                if (anchor) {
                    anchor.before(row.cloneNode(true), ...between(row, sibling));
                    return true;
                }
            }
            return false;
        };

        window.Livewire.hook('morph', ({ el, toEl }) => {
            // A nested component's rows are its own morph's to keep or drop: the
            // parent's new HTML never carries them, so they would read as dropped
            // and fold away whenever the parent re-renders.
            const owner = el.closest('[wire\\:id]');
            const rows = new Set([
                ...el.querySelectorAll('[data-sigta-motion] > *'),
                ...el.querySelectorAll('[data-sigta-completing]'),
            ]);
            Array.from(rows)
                .filter((row) => row.parentElement?.closest('[wire\\:id]') === owner)
                .sort((a, b) => (a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1))
                .forEach((row) => {
                    const key = keyOf(row);
                    if (!key) return;
                    const twin = lookup(toEl, key);
                    if (twin) {
                        // The server wants the row (still, or again, after an undo).
                        dropped.delete(row);
                        if (row.hasAttribute('data-sigta-leaving')) revive(row);
                        if (row.hasAttribute('data-sigta-completing')) {
                            twin.setAttribute('data-sigta-done', '');
                            twin.setAttribute('data-sigta-completing', '');
                        } else if (row.hasAttribute('data-sigta-done') && !twin.hasAttribute('data-sigta-done')) {
                            undone.add(row);
                        }
                        return;
                    }
                    dropped.add(row);
                    if (!rehome(row, toEl)) held.add(row);
                    leaving.add(row);
                });
        });

        window.Livewire.hook('morph.removing', ({ el, skip }) => {
            if (held.has(el)) skip();
        });

        // A loader mid-landing keeps its flag through a morph (a poll, say),
        // or its stamp would start before the circle has risen.
        window.Livewire.hook('morph.updating', ({ el, toEl }) => {
            if (el.hasAttribute?.('data-landing')) toEl.setAttribute('data-landing', '');
        });

        window.Livewire.hook('morphed', () => {
            // A row the server says is not done after all gets its tick back
            // as rendered; the morph only patched attributes, not the ticked box.
            undone.forEach((row) => row.querySelectorAll('input[type="checkbox"]').forEach((box) => {
                box.checked = box.hasAttribute('checked');
            }));
            undone.clear();
            leaving.forEach((row) => {
                // A completing row leaves when its commit settles; one already
                // folding just carries on.
                if (row.isConnected && !row.hasAttribute('data-sigta-completing') && !row.hasAttribute('data-sigta-leaving')) leave(row);
            });
            leaving.clear();
        });
    }

    installGlobals(api);
    Alpine.magic('sigta', () => api);
});
