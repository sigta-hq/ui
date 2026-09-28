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
       Sigta::toast('…') or $this->dispatch('sigta-toast', heading: '…', variant: 'success');
       An `action` ({ text, event, detail } or { text, onClick }) adds a button —
       "Undo" after a completion — that fires the event (through Livewire when it
       is on the page, so #[On] listeners hear it) and closes the toast. A `key`
       makes a toast replace the one before it with the same key, so ticking
       off five items in a row leaves one toast, not a stack of five. */
    Alpine.data('sigtaToasts', () => ({
        toasts: [],
        counter: 0,
        add(detail = {}) {
            const id = ++this.counter;
            if (detail.key) this.toasts = this.toasts.filter((t) => t.key !== detail.key);
            this.toasts.push({
                key: detail.key ?? null,
                id,
                heading: detail.heading ?? null,
                text: detail.text ?? '',
                variant: detail.variant ?? 'info',
                link: detail.link ?? null,
                action: detail.action ?? null,
            });
            const duration = detail.duration ?? 5000;
            if (duration > 0) setTimeout(() => this.remove(id), duration);
        },
        remove(id) {
            this.toasts = this.toasts.filter((t) => t.id !== id);
        },
        act(toast) {
            const action = toast.action;
            if (!action) return;
            if (typeof action.onClick === 'function') action.onClick();
            if (action.event) {
                if (window.Livewire?.dispatch) window.Livewire.dispatch(action.event, action.detail ?? {});
                else window.dispatchEvent(new CustomEvent(action.event, { detail: action.detail ?? {} }));
            }
            this.remove(toast.id);
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

    /* -----------------------------------------------------------------------
       Motion — ink on paper. Loaders draw, a finished row is ticked, crossed
       out and folded away, and the bigger moments get a flourish. Everything
       here decorates state the markup already carries (data-state, ARIA,
       text), so each helper checks prefers-reduced-motion and quietly skips
       its flourish; the work itself (a commit, a removal) always happens.
    ----------------------------------------------------------------------- */
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const reduced = () => reducedMotion.matches;
    const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
    const settled = (animation) => animation.finished.then(() => {}, () => {});

    // Theme timings and colours, read at call time so a re-themed subtree wins.
    const token = (name, fallback, el = document.documentElement) =>
        getComputedStyle(el).getPropertyValue(name).trim() || fallback;
    const duration = (name, fallback, el) => {
        const value = token(name, '', el);
        const number = parseFloat(value);
        if (Number.isNaN(number)) return fallback;
        return value.endsWith('ms') ? number : value.endsWith('s') ? number * 1000 : number;
    };
    const pen = (el) => token('--sigta-curve-pen', 'cubic-bezier(.65, 0, .35, 1)', el);
    const spring = (el) => token('--sigta-curve-spring', 'cubic-bezier(.34, 1.56, .64, 1)', el);
    const settle = (el) => token('--sigta-curve-out', 'cubic-bezier(.2, .8, .2, 1)', el);

    const elementOf = (target) => (typeof target === 'string' ? document.querySelector(target) : target) ?? null;

    // Flourishes draw on one fixed layer above the page, outside every Livewire
    // component so no morph touches them; inside an open dialog when the
    // target is in one, since a modal sits in the top layer above the page.
    const overlay = (target) => {
        const host = target?.closest?.('dialog[open]') ?? document.body;
        let layer = Array.from(host.children).find((child) => child.hasAttribute('data-sigta-overlay'));
        if (!layer) {
            layer = document.createElement('div');
            layer.setAttribute('data-sigta-overlay', '');
            layer.setAttribute('aria-hidden', 'true');
            layer.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:95;overflow:hidden';
            host.appendChild(layer);
        }
        return layer;
    };

    // A flourish rides along with its element: placed once on the fixed layer,
    // then shifted every frame by however far the element has moved since (a
    // scroll, a layout change) until the flourish is gone. It stays where it
    // was if the element leaves the page.
    const follow = (el, node) => {
        const start = el.getBoundingClientRect();
        const step = () => {
            if (!node.isConnected) return;
            if (el.isConnected) {
                const now = el.getBoundingClientRect();
                node.style.transform = `translate(${now.left - start.left}px, ${now.top - start.top}px)`;
            }
            requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    };

    const svgNode = (name, attributes = {}) => {
        const node = document.createElementNS('http://www.w3.org/2000/svg', name);
        Object.entries(attributes).forEach(([key, value]) => node.setAttribute(key, value));
        return node;
    };

    /* Burst — short ink strokes flick out from an element and lift off: the
       small "yes" for a ticked item, a starred thing, a sent message. They
       start on an ellipse hugging the element, so a wide button gets a burst
       along its edges rather than a far-flung circle. */
    const burst = (target, options = {}) => {
        const el = elementOf(target);
        if (reduced() || !el?.isConnected) return;
        const rect = el.getBoundingClientRect();
        const gap = options.gap ?? 4;
        const rx = rect.width / 2 + gap;
        const ry = rect.height / 2 + gap;
        const reach = options.reach ?? Math.min(14, Math.max(7, Math.min(rx, ry) * 0.7));
        const count = options.count ?? (rect.width > rect.height * 2 ? 10 : 8);
        const width = (rx + reach) * 2 + 8;
        const height = (ry + reach) * 2 + 8;
        const svg = svgNode('svg', {
            width, height, viewBox: `${-width / 2} ${-height / 2} ${width} ${height}`, fill: 'none',
        });
        svg.style.cssText = `position:absolute;left:${rect.left + rect.width / 2 - width / 2}px;top:${rect.top + rect.height / 2 - height / 2}px;overflow:visible;color:${options.color ?? 'var(--sigta-fg)'}`;
        const time = duration('--sigta-dur-draw', 420) * 1.3;
        const turn = Math.random() * Math.PI;
        for (let i = 0; i < count; i++) {
            const angle = turn + (i / count) * Math.PI * 2 + (Math.random() - 0.5) * 0.35;
            const x = Math.cos(angle) * rx;
            const y = Math.sin(angle) * ry;
            // Flick outward along the ellipse's normal at that point.
            const nx = Math.cos(angle) / rx;
            const ny = Math.sin(angle) / ry;
            const n = Math.hypot(nx, ny);
            const length = reach * (0.65 + Math.random() * 0.5);
            const stroke = svgNode('path', {
                d: `M${x.toFixed(2)} ${y.toFixed(2)}L${(x + (nx / n) * length).toFixed(2)} ${(y + (ny / n) * length).toFixed(2)}`,
                pathLength: 100, stroke: 'currentColor', 'stroke-width': options.width ?? 2,
                'stroke-linecap': 'round', 'stroke-dasharray': '100 200', 'stroke-dashoffset': '101',
            });
            svg.appendChild(stroke);
            stroke.animate(
                [{ strokeDashoffset: '101' }, { strokeDashoffset: '0', offset: 0.45 }, { strokeDashoffset: '-101' }],
                { duration: time, delay: (i % 2) * 40, easing: pen(el), fill: 'both' },
            );
        }
        overlay(el).appendChild(svg);
        follow(el, svg);
        setTimeout(() => svg.remove(), time + 200);
    };

    /* Ring — a pen circles what just happened, overshooting where it closes
       the way a hand does, holds a beat, and fades. */
    const ring = (target, options = {}) => {
        const el = elementOf(target);
        if (reduced() || !el?.isConnected) return;
        const rect = el.getBoundingClientRect();
        const pad = options.pad ?? 8;
        const rx = rect.width / 2 + pad;
        const ry = rect.height / 2 + pad * 0.75;
        const wobble = 0.025 + Math.random() * 0.025;
        const phase = Math.random() * Math.PI * 2;
        const tilt = -0.045;
        const steps = 56;
        const start = -2.3;
        const sweep = Math.PI * 2 + 0.5;
        let d = '';
        for (let i = 0; i <= steps; i++) {
            const t = start + (i / steps) * sweep;
            // Swell slightly as it goes, so the overshoot passes outside the start.
            const r = 1 + Math.sin(t * 2 + phase) * wobble + (i / steps) * 0.07;
            const x = Math.cos(t) * rx * r;
            const y = Math.sin(t) * ry * r;
            d += `${i ? 'L' : 'M'}${(x * Math.cos(tilt) - y * Math.sin(tilt)).toFixed(2)} ${(x * Math.sin(tilt) + y * Math.cos(tilt)).toFixed(2)}`;
        }
        const width = rx * 2.3 + 16;
        const height = ry * 2.3 + 16;
        const svg = svgNode('svg', {
            width, height, viewBox: `${-width / 2} ${-height / 2} ${width} ${height}`, fill: 'none',
        });
        svg.style.cssText = `position:absolute;left:${rect.left + rect.width / 2 - width / 2}px;top:${rect.top + rect.height / 2 - height / 2}px;overflow:visible;color:${options.color ?? 'var(--sigta-fg)'}`;
        const path = svgNode('path', {
            d, pathLength: 100, stroke: 'currentColor', 'stroke-width': options.width ?? 2,
            'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-dasharray': '100 200', 'stroke-dashoffset': '101',
        });
        svg.appendChild(path);
        overlay(el).appendChild(svg);
        follow(el, svg);
        const draw = duration('--sigta-dur-draw', 420) * 1.6;
        const hold = options.hold ?? 900;
        path.animate([{ strokeDashoffset: '101' }, { strokeDashoffset: '0' }], { duration: draw, easing: pen(el), fill: 'forwards' });
        settled(svg.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 360, delay: draw + hold, fill: 'forwards' }))
            .then(() => svg.remove());
    };

    /* Note — a handwritten word in the margin ("done!", "saved") written in
       beside an element; it floats up and fades. Decorative, so say the same
       thing in text or ARIA somewhere the page keeps. */
    const note = (target, text = 'nice!', options = {}) => {
        const el = elementOf(target);
        if (reduced() || !el?.isConnected || !text) return;
        const rect = el.getBoundingClientRect();
        const holder = document.createElement('div');
        holder.style.cssText = `position:absolute;left:${rect.right + 6}px;top:${rect.top - 16}px`;
        const word = document.createElement('span');
        word.textContent = text;
        word.className = 'sigta-write';
        word.style.cssText = `display:inline-block;font-family:var(--sigta-font-hand);font-weight:600;font-size:${options.size ?? '1.4rem'};line-height:1;white-space:nowrap;color:${options.color ?? 'var(--sigta-fg)'};rotate:${options.rotate ?? -6}deg`;
        holder.appendChild(word);
        overlay(el).appendChild(holder);
        // Keep it on screen: flip to the left of the element, or below it.
        const box = holder.getBoundingClientRect();
        if (box.right > window.innerWidth - 8) holder.style.left = `${Math.max(8, rect.left - box.width - 6)}px`;
        if (box.top < 8) holder.style.top = `${rect.bottom + 4}px`;
        follow(el, holder);
        settled(word.animate(
            [{ translate: '0 0', opacity: 1 }, { translate: '0 -0.6rem', opacity: 0 }],
            { duration: 520, delay: options.hold ?? 1100, easing: 'ease-in', fill: 'forwards' },
        )).then(() => holder.remove());
    };

    /* Confetti — paper, not glitter: slips of paper, ink dots and a few
       highlighter squiggles tumble up and flutter down. For milestones (inbox
       zero, a finished import), never for every click. */
    const confetti = (target = null, options = {}) => {
        if (reduced()) return;
        const el = elementOf(target);
        const layer = overlay(el);
        const canvas = document.createElement('canvas');
        const ratio = Math.min(window.devicePixelRatio || 1, 2);
        const width = window.innerWidth;
        const height = window.innerHeight;
        canvas.width = width * ratio;
        canvas.height = height * ratio;
        canvas.style.cssText = 'position:absolute;inset:0;width:100%;height:100%';
        layer.appendChild(canvas);
        const ctx = canvas.getContext('2d');
        ctx.scale(ratio, ratio);

        const ink = token('--sigta-fg', 'black');
        const paper = token('--sigta-surface-raised', 'white');
        const palette = [
            ink, ink, ink, token('--sigta-fg-muted', 'gray'), token('--sigta-fg-muted', 'gray'),
            token('--sigta-line', 'silver'), token('--sigta-line', 'silver'), paper, paper,
            token('--sigta-accent', 'gold'), token('--sigta-accent', 'gold'),
        ];
        const rect = el?.getBoundingClientRect();
        const originX = rect ? rect.left + rect.width / 2 : width / 2;
        const originY = rect ? rect.top + rect.height / 2 : height * 0.3;
        const kinds = ['slip', 'slip', 'slip', 'dot', 'squiggle'];
        const pieces = Array.from({ length: options.count ?? 90 }, () => {
            const angle = -Math.PI / 2 + (Math.random() - 0.5) * Math.PI * 1.15;
            const speed = 5 + Math.random() * 10;
            const color = palette[Math.floor(Math.random() * palette.length)];
            return {
                x: originX, y: originY,
                vx: Math.cos(angle) * speed, vy: Math.sin(angle) * speed,
                spin: Math.random() * Math.PI * 2, vspin: (Math.random() - 0.5) * 0.35,
                flutter: Math.random() * Math.PI * 2,
                kind: kinds[Math.floor(Math.random() * kinds.length)],
                w: 6 + Math.random() * 7, h: 3 + Math.random() * 3,
                color, outline: color === paper,
            };
        });

        const life = options.duration ?? 2400;
        const began = performance.now();
        let last = began;
        const frame = (now) => {
            const t = now - began;
            const step = Math.min(2.5, (now - last) / 16.7);
            last = now;
            ctx.clearRect(0, 0, width, height);
            ctx.globalAlpha = t > life * 0.7 ? Math.max(0, 1 - (t - life * 0.7) / (life * 0.3)) : 1;
            for (const p of pieces) {
                p.vx *= Math.pow(0.985, step);
                p.vy = p.vy * Math.pow(0.985, step) + 0.3 * step;
                p.flutter += 0.13 * step;
                p.x += (p.vx + Math.sin(p.flutter) * 0.7) * step;
                p.y += p.vy * step;
                p.spin += p.vspin * step;
                ctx.save();
                ctx.translate(p.x, p.y);
                ctx.rotate(p.spin);
                if (p.kind === 'slip') {
                    // A slip of paper turning over as it falls.
                    ctx.scale(1, Math.cos(p.flutter));
                    ctx.fillStyle = p.color;
                    ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                    if (p.outline) {
                        ctx.strokeStyle = ink;
                        ctx.lineWidth = 0.8;
                        ctx.strokeRect(-p.w / 2, -p.h / 2, p.w, p.h);
                    }
                } else if (p.kind === 'dot') {
                    ctx.fillStyle = p.outline ? ink : p.color;
                    ctx.beginPath();
                    ctx.arc(0, 0, p.h * 0.55, 0, Math.PI * 2);
                    ctx.fill();
                } else {
                    ctx.strokeStyle = p.outline ? ink : p.color;
                    ctx.lineWidth = 1.8;
                    ctx.lineCap = 'round';
                    ctx.beginPath();
                    ctx.moveTo(-p.w, 0);
                    ctx.bezierCurveTo(-p.w / 3, -p.h * 1.4, p.w / 3, p.h * 1.4, p.w, 0);
                    ctx.stroke();
                }
                ctx.restore();
            }
            if (t < life) requestAnimationFrame(frame);
            else canvas.remove();
        };
        requestAnimationFrame(frame);
    };

    /* Celebrate — one call per size of moment:
         small  → a burst (a ticked item, a starred thing)
         medium → a pen ring and a handwritten note (a finished step)
         big    → paper confetti and a note (inbox zero, a finished import)
       From Livewire: Sigta::celebrate('big', text: 'Inbox zero!') or
       $this->dispatch('sigta-celebrate', level: 'big', target: '#inbox'). */
    const celebrate = (target = null, level = 'small', options = {}) => {
        const el = elementOf(target);
        if (level === 'big') {
            confetti(el, options);
            if (el && options.text !== false) note(el, options.text ?? 'well done!', options);
        } else if (level === 'medium') {
            ring(el, options);
            if (options.text !== false) note(el, options.text ?? 'nice!', options);
        } else {
            burst(el, options);
        }
    };

    /* Fold / unfold — a leaving row slides aside and the gap closes after it;
       an arriving row opens its gap, settles in and carries a brief
       highlighter wash so the eye finds it. Block rows only; a table row or a
       display:contents wrapper just fades. */
    const edges = ['paddingTop', 'paddingBottom', 'marginTop', 'marginBottom', 'borderTopWidth', 'borderBottomWidth'];
    const foldable = (style) => !['table-row', 'contents', 'inline'].includes(style.display);
    const shape = (el, style) => {
        const open = { height: `${el.getBoundingClientRect().height}px` };
        const shut = { height: '0px' };
        edges.forEach((edge) => { open[edge] = style[edge]; shut[edge] = '0px'; });
        return [open, shut];
    };

    const fold = (el) => {
        if (!el?.isConnected || reduced()) return Promise.resolve();
        const style = getComputedStyle(el);
        const time = duration('--sigta-dur-leave', 360, el);
        el.style.pointerEvents = 'none';
        if (!foldable(style)) {
            return settled(el.animate([{ opacity: 1 }, { opacity: 0 }], { duration: time, fill: 'forwards' }));
        }
        const [open, shut] = shape(el, style);
        el.style.overflow = 'hidden';
        el.style.boxSizing = 'border-box';
        return settled(el.animate([
            { ...open, opacity: 1, translate: '0 0', offset: 0 },
            { ...open, opacity: 0, translate: '1.25rem 0', offset: 0.45 },
            { ...shut, opacity: 0, translate: '1.25rem 0', offset: 1 },
        ], { duration: time * 1.6, easing: 'cubic-bezier(.4, 0, .2, 1)', fill: 'forwards' }));
    };

    const unfold = (el) => {
        if (!el?.isConnected || reduced()) return Promise.resolve();
        const style = getComputedStyle(el);
        const time = duration('--sigta-dur-draw', 420, el);
        if (!foldable(style)) {
            return settled(el.animate([{ opacity: 0 }, { opacity: 1 }], { duration: time }));
        }
        const [open, shut] = shape(el, style);
        const accent = token('--sigta-accent', 'gold', el);
        el.style.overflow = 'hidden';
        el.animate(
            [{ boxShadow: `inset 0 0 0 100vmax color-mix(in srgb, ${accent} 45%, transparent)` }, { boxShadow: 'inset 0 0 0 100vmax transparent' }],
            { duration: 1600, delay: time * 0.6, easing: 'ease-out' },
        );
        return settled(el.animate([
            { ...shut, opacity: 0, translate: '0 -0.35rem', offset: 0 },
            { ...open, opacity: 0, translate: '0 -0.35rem', offset: 0.45 },
            { ...open, opacity: 1, translate: '0 0', offset: 1 },
        ], { duration: time * 1.4, easing: settle(el) })).then(() => { el.style.overflow = ''; });
    };

    /* Leave — fold a row away and remove it. Pass { remove: false } when a
       framework owns the node (an Alpine x-for): fold, then drop the item. */
    const leave = async (target, options = {}) => {
        const el = elementOf(target);
        if (!el?.isConnected) return;
        el.setAttribute('data-sigta-leaving', '');
        await fold(el);
        // Brought back mid-fold (an undo that beat the animation): it stays.
        if (!el.hasAttribute('data-sigta-leaving')) return;
        if (options.remove !== false) el.remove();
    };

    // Undo a fold in progress and hand the row back to the page.
    const revive = (el) => {
        el.removeAttribute('data-sigta-leaving');
        el.getAnimations().forEach((animation) => animation.cancel());
        ['pointerEvents', 'overflow', 'boxSizing'].forEach((property) => { el.style[property] = ''; });
    };

    const shake = (el) => {
        if (reduced() || !el?.isConnected) return;
        el.animate(
            [{ translate: '0 0' }, { translate: '-5px 0' }, { translate: '4px 0' }, { translate: '-2px 0' }, { translate: '0 0' }],
            { duration: 380, easing: 'ease-in-out' },
        );
    };

    const rowOf = (target) => {
        const el = elementOf(target);
        return el?.closest?.('[data-sigta-row], [data-sigta-motion] > *, [wire\\:key]') ?? el;
    };

    // Rows the server dropped in a morph but that were kept on screen to leave.
    const dropped = new WeakSet();

    /* Complete — the moment a task, a message, a to-do is done: its box
       ticks, a burst flicks out, each [data-sigta-strike] is crossed through,
       then the row folds away. `commit` does the real work ($wire.complete(id),
       an array filter, a fetch); if it throws or rejects, the row is put back.
       Inside Livewire the commit goes out at once and the row leaves only if
       the server drops it; elsewhere the row folds first, then commit runs.

         <sigta:checkbox x-on:change="$sigta.complete($el, () => $wire.complete({{ $task->id }}))" />

       Options: stamp ('Done' or true) presses a stamp on the row; leave: false
       keeps it in place, crossed out; defer: true folds first and commits
       after, even inside Livewire (an Alpine x-for list within a component). */
    const complete = async (target, commit = null, options = {}) => {
        const row = rowOf(target);
        if (!row || row.hasAttribute('data-sigta-completing')) return false;
        const source = elementOf(target);
        const quiet = reduced();
        row.setAttribute('data-sigta-done', '');
        row.setAttribute('data-sigta-completing', '');
        let stamp = null;
        if (!quiet) {
            const from = source?.matches?.('input, button') ? source : row.querySelector('input[type="checkbox"]') ?? source;
            burst(from, { reach: 10 });
            if (options.stamp) {
                stamp = document.createElement('span');
                stamp.className = 'sigta-stamp';
                stamp.setAttribute('aria-hidden', 'true');
                stamp.textContent = options.stamp === true ? 'Done' : options.stamp;
                if (getComputedStyle(row).position === 'static') row.style.position = 'relative';
                row.appendChild(stamp);
            }
        }
        const marked = wait(quiet ? 0 : duration('--sigta-dur-draw', 420, row) + 280);
        const run = () => Promise.resolve(typeof commit === 'function' ? commit() : commit);
        const atOnce = options.defer === undefined ? !!row.closest('[wire\\:id]') : !options.defer;
        try {
            if (atOnce) {
                // The optimistic flags hold only while the commit is out; from
                // its answer on, what the server renders wins.
                const committed = run().finally(() => row.removeAttribute('data-sigta-completing'));
                await Promise.all([committed, marked]);
                if (dropped.has(row)) await leave(row);
            } else {
                await marked;
                if (options.leave !== false) await fold(row);
                await run();
                row.removeAttribute('data-sigta-completing');
                if (options.leave !== false && !commit) row.remove();
            }
            return true;
        } catch (error) {
            row.getAnimations().forEach((animation) => animation.cancel());
            row.style.pointerEvents = '';
            row.style.overflow = '';
            row.removeAttribute('data-sigta-completing');
            row.removeAttribute('data-sigta-done');
            stamp?.remove();
            if (source?.type === 'checkbox') source.checked = false;
            shake(row);
            console.error(error);
            return false;
        }
    };

    /* Loader landing — when a working compass is told it is done, it finishes
       the turn it is on and closes its circle, then the circle's front rises
       into the mark's arc, and only then does the CSS done state (the stamp)
       take over. data-landing keeps the working motion running underneath
       until the landing hands over. Without this script, or with reduced
       motion, the loader simply lands. */
    const landLoader = (loader) => {
        if (reduced() || loader.dataset.variant !== 'compass' || loader.hasAttribute('data-landing')) return;
        loader.setAttribute('data-landing', '');
        const deg = parseFloat(getComputedStyle(loader).getPropertyValue('--sigta-loader-deg')) || 0;
        const end = Math.max(360, Math.ceil(deg / 360 - 1e-6) * 360);
        const turn = 380 + 520 * ((end - deg) / 360);
        const rise = 320;
        const ease = settle(loader);
        const landing = loader.animate([
            { '--sigta-loader-deg': String(deg), '--sigta-loader-fill': '0', '--sigta-loader-rise': '0', easing: ease },
            { '--sigta-loader-deg': String(end), '--sigta-loader-fill': '1', '--sigta-loader-rise': '0', offset: turn / (turn + rise), easing: ease },
            { '--sigta-loader-deg': String(end), '--sigta-loader-fill': '1', '--sigta-loader-rise': '1' },
        ], { duration: turn + rise, fill: 'forwards' });
        settled(landing).then(() => {
            loader.removeAttribute('data-landing');
            landing.cancel();
        });
    };
    new MutationObserver((records) => records.forEach((record) => {
        const loader = record.target;
        if (loader.dataset?.sigta === 'loader' && loader.dataset.state === 'done' && record.oldValue === 'working') landLoader(loader);
    })).observe(document.documentElement, { attributes: true, attributeFilter: ['data-state'], attributeOldValue: true, subtree: true });

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
            const rows = new Set([
                ...el.querySelectorAll('[data-sigta-motion] > *'),
                ...el.querySelectorAll('[data-sigta-completing]'),
            ]);
            Array.from(rows)
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

    /* Motion lists — rows added to a [data-sigta-motion] list after the page
       has settled unfold in, whoever adds them (a morph, an x-for, plain DOM).
       A node that was also removed in the same batch was only moved. */
    let ready = false;
    const born = new WeakMap();
    document.addEventListener('alpine:initialized', () => requestAnimationFrame(() => { ready = true; }));
    new MutationObserver((records) => {
        if (!ready) return;
        const now = performance.now();
        const moved = new Set();
        records.forEach((record) => record.removedNodes.forEach((node) => moved.add(node)));
        records.forEach((record) => {
            record.addedNodes.forEach((node) => {
                if (node.nodeType !== 1) return;
                if (node.matches('[data-sigta-motion]')) born.set(node, now);
                node.querySelectorAll('[data-sigta-motion]').forEach((list) => born.set(list, now));
            });
        });
        records.forEach((record) => {
            const list = record.target;
            if (record.type !== 'childList' || list.nodeType !== 1 || !list.hasAttribute('data-sigta-motion')) return;
            if (now - (born.get(list) ?? -Infinity) < 400) return;
            record.addedNodes.forEach((node) => {
                if (node.nodeType === 1 && !moved.has(node) && !node.hasAttribute('data-sigta-leaving')) unfold(node);
            });
        });
    }).observe(document.documentElement, { childList: true, subtree: true });

    /* Count — a number that bumps when it changes (a morph, an x-text, a
       plain write), rising when it grows and dropping when it shrinks. */
    const numberIn = (el) => parseFloat(el.textContent.replace(/[^\d.-]/g, ''));
    Alpine.data('sigtaCount', () => ({
        last: null,
        observer: null,
        init() {
            this.last = numberIn(this.$root);
            this.observer = new MutationObserver(() => this.bump());
            this.observer.observe(this.$root, { childList: true, characterData: true, subtree: true });
        },
        destroy() {
            this.observer?.disconnect();
        },
        bump() {
            const value = numberIn(this.$root);
            if (Number.isNaN(value) || value === this.last) return;
            const rise = value > this.last;
            this.last = value;
            if (reduced()) return;
            this.$root.animate([
                { translate: `0 ${rise ? '0.45em' : '-0.45em'}`, opacity: 0.2, scale: 1 },
                { translate: '0 0', opacity: 1, scale: 1.2, offset: 0.55 },
                { translate: '0 0', opacity: 1, scale: 1 },
            ], { duration: 440, easing: spring(this.$root) });
        },
    }));

    /* Save state — the compass stands in for "Saving…": it draws while a
       save is in flight, lands with a handwritten "Saved", and shakes red when
       a save fails (a validation error counts). It follows Livewire by itself —
       data-target names the actions and/or properties to watch on the closest
       component — or listens for sigta-save-state events: $sigta.save(promise). */
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
            window.addEventListener('sigta-save-state', this.onEvent);
            this.followLivewire();
        },
        destroy() {
            window.removeEventListener('sigta-save-state', this.onEvent);
            this.stopFollowing?.();
            this.component?.removeEventListener('input', this.onInput);
            clearTimeout(this.timer);
        },
        get mark() {
            return { dirty: 'queued', saving: 'working', saved: 'done', failed: 'failed' }[this.state] ?? 'idle';
        },
        set(state) {
            if (!['idle', 'dirty', 'saving', 'saved', 'failed'].includes(state)) return;
            clearTimeout(this.timer);
            this.state = state;
            if (state === 'saved') this.timer = setTimeout(() => { this.state = 'idle'; }, 2600);
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

    /* Save — drive named (or unnamed) save states from any promise:
         $sigta.save(fetch('/profile', { method: 'POST', body }), 'profile') */
    const saveState = (state, name = null) =>
        window.dispatchEvent(new CustomEvent('sigta-save-state', { detail: { name, state } }));
    const save = (work, name = null) => {
        saveState('saving', name);
        return Promise.resolve(typeof work === 'function' ? work() : work).then(
            (value) => { saveState('saved', name); return value; },
            (error) => { saveState('failed', name); throw error; },
        );
    };

    /* Job — a queued or running piece of work whose markup carries its state
       (a poll or a morph updates data-state); this adds only the moment it
       lands: a burst from the compass when it turns done. */
    Alpine.data('sigtaJob', () => ({
        state: null,
        observer: null,
        init() {
            this.state = this.$root.dataset.state;
            this.observer = new MutationObserver(() => {
                const next = this.$root.dataset.state;
                if (next === this.state) return;
                if (next === 'done') burst(this.$root.querySelector('[data-sigta="loader"]') ?? this.$root, { reach: 12 });
                this.state = next;
            });
            this.observer.observe(this.$root, { attributes: true, attributeFilter: ['data-state'] });
        },
        destroy() {
            this.observer?.disconnect();
        },
    }));

    window.addEventListener('sigta-celebrate', (event) => {
        const { level = 'small', target = null, text } = event.detail ?? {};
        celebrate(target, level, text === undefined ? {} : { text });
    });

    const api = { toast: sigtaToast, complete, leave, celebrate, burst, ring, note, confetti, save, saveState };

    window.Sigta = window.Sigta || {};
    Object.assign(window.Sigta, api);

    Alpine.magic('sigta', () => api);
});
