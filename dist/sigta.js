/* ---------------------------------------------------------------------------
   Sigta UI runtime

   Registers Alpine behaviors for interactive components. Alpine ships with
   Livewire, so this file assumes `alpine:init` will fire; it is a no-op on
   pages without Livewire/Alpine.

   Source of truth. dist/sigta.js is a verbatim copy (no build step); a
   bundling step arrives when the first dependency or minification need does.
--------------------------------------------------------------------------- */
document.addEventListener('alpine:init', () => {
    const Alpine = window.Alpine;
    if (!Alpine) return;

    /* Dropdown / popover — open state + roving focus over menu items. */
    Alpine.data('sigtaDropdown', () => ({
        open: false,
        toggle() {
            this.open = !this.open;
            if (this.open) this.$nextTick(() => this.focusItem(0));
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
            document.documentElement.style.removeProperty('--sigta-floating-space');
        },
        publish() {
            const bottom = parseFloat(getComputedStyle(this.$root).paddingBottom) || 0;
            const space = this.visible ? this.$refs.bar.offsetHeight + bottom * 2 : 0;
            document.documentElement.style.setProperty('--sigta-floating-space', `${space}px`);
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
            const next = tabs[(i + delta + tabs.length) % tabs.length];
            if (next) {
                this.active = next.dataset.name;
                next.focus();
            }
        },
    }));

    /* Combobox core — shared by Select (rich), Autocomplete, Command, Pillbox.
       Options are DOM elements with [role="option"] and data-value; filtering
       toggles `hidden`; the active option is tracked via aria-activedescendant
       so screen readers follow without moving focus off the input. */
    Alpine.data('sigtaCombobox', (config = {}) => ({
        open: false,
        query: '',
        value: config.multiple ? (config.initial ?? []) : (config.initial ?? null),
        multiple: config.multiple ?? false,
        activeId: null,

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

        // Float the listbox with fixed positioning so it escapes any ancestor
        // overflow (a card/form bounding box) instead of being clipped, and
        // flip above the field when there isn't room below. Needs x-ref="anchor"
        // (the input/trigger) and x-ref="panel" (the listbox); a no-op without
        // them (e.g. the command palette, which lives in a dialog).
        positionPanel() {
            const anchor = this.$refs.anchor;
            const panel = this.$refs.panel;
            if (!anchor || !panel) return;

            const rect = anchor.getBoundingClientRect();
            const gap = 6;
            const spaceBelow = window.innerHeight - rect.bottom;
            const flip = spaceBelow < panel.offsetHeight + gap && rect.top > spaceBelow;

            panel.style.position = 'fixed';
            panel.style.left = `${Math.round(rect.left)}px`;
            panel.style.width = `${Math.round(rect.width)}px`;
            if (flip) {
                panel.style.top = 'auto';
                panel.style.bottom = `${Math.round(window.innerHeight - rect.top + gap)}px`;
            } else {
                panel.style.bottom = 'auto';
                panel.style.top = `${Math.round(rect.bottom + gap)}px`;
            }
        },
        visible() {
            return this.options().filter((o) => !o.hidden);
        },
        filter() {
            const q = this.query.trim().toLowerCase();
            this.options().forEach((o) => {
                o.hidden = q !== '' && !o.textContent.toLowerCase().includes(q);
            });
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
            const i = opts.indexOf(this.activeEl());
            this.setActive(opts[(i + delta + opts.length) % opts.length]);
        },
        chooseActive() {
            const el = this.activeEl();
            if (el) this.choose(el);
        },
        choose(el) {
            const value = el.dataset.value ?? el.textContent.trim();
            const label = (el.dataset.label ?? el.textContent).trim();

            if (this.multiple) {
                this.value = this.value.includes(value)
                    ? this.value.filter((v) => v !== value)
                    : [...this.value, value];
            } else {
                this.value = value;
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
            return this.multiple ? this.value.includes(value) : this.value === value;
        },
        labelFor(value) {
            const el = this.options().find((o) => (o.dataset.value ?? o.textContent.trim()) === value);
            return el ? (el.dataset.label ?? el.textContent).trim() : value;
        },
        syncInput() {
            const input = this.$refs.value;
            if (!input) return;
            input.value = this.multiple ? JSON.stringify(this.value) : (this.value ?? '');
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        },
    }));

    /* Toast stack — driven by `sigta-toast` window events. From Livewire:
       Sigta::toast('…') or $this->dispatch('sigta-toast', heading: '…', variant: 'success'); */
    Alpine.data('sigtaToasts', () => ({
        toasts: [],
        counter: 0,
        add(detail = {}) {
            const id = ++this.counter;
            this.toasts.push({
                id,
                heading: detail.heading ?? null,
                text: detail.text ?? '',
                variant: detail.variant ?? 'info',
                link: detail.link ?? null,
            });
            const duration = detail.duration ?? 5000;
            if (duration > 0) setTimeout(() => this.remove(id), duration);
        },
        remove(id) {
            this.toasts = this.toasts.filter((t) => t.id !== id);
        },
    }));

    /* Toast trigger helpers — window.Sigta.toast() and Alpine $sigta.toast(). */
    const sigtaToast = (messageOrOptions, options) => {
        let detail = {};
        if (typeof messageOrOptions === 'string') {
            detail.text = messageOrOptions;
            if (options && typeof options === 'object') {
                detail = { ...detail, ...options };
            }
        } else if (messageOrOptions && typeof messageOrOptions === 'object') {
            detail = messageOrOptions;
        }
        window.dispatchEvent(new CustomEvent('sigta-toast', { detail }));
    };

    window.Sigta = window.Sigta || {};
    window.Sigta.toast = sigtaToast;

    Alpine.magic('sigta', () => ({
        toast: sigtaToast,
    }));
});
