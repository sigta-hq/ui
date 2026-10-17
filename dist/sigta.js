/* Sigta UI — Livewire runtime. Built by scripts/build-livewire.js from resources/js/sigta.js and packages/core/src; do not edit. sources:d9db7e9fb8b4bafd8aa2e20a13c8156d81be8dfa */
(() => {
  // packages/core/src/env.ts
  var isBrowser = () => typeof window !== "undefined" && typeof document !== "undefined";
  var reducedQuery = null;
  function reduced() {
    if (!isBrowser() || typeof window.matchMedia !== "function") return true;
    reducedQuery ?? (reducedQuery = window.matchMedia("(prefers-reduced-motion: reduce)"));
    return reducedQuery.matches;
  }
  var wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
  var settled = (animation) => animation.finished.then(() => {
  }, () => {
  });
  function token(name, fallback, el) {
    if (!isBrowser()) return fallback;
    return getComputedStyle(el ?? document.documentElement).getPropertyValue(name).trim() || fallback;
  }
  function duration(name, fallback, el) {
    const value = token(name, "", el);
    const number = parseFloat(value);
    if (Number.isNaN(number)) return fallback;
    return value.endsWith("ms") ? number : value.endsWith("s") ? number * 1e3 : number;
  }
  var curves = {
    out: "cubic-bezier(.2, .8, .2, 1)",
    pen: "cubic-bezier(.65, 0, .35, 1)",
    spring: "cubic-bezier(.34, 1.56, .64, 1)"
  };
  var pen = (el) => token("--sigta-curve-pen", curves.pen, el);
  var spring = (el) => token("--sigta-curve-spring", curves.spring, el);
  var settle = (el) => token("--sigta-curve-out", curves.out, el);
  function elementOf(target) {
    if (!isBrowser() || !target) return null;
    return typeof target === "string" ? document.querySelector(target) : target;
  }

  // packages/core/src/celebrate.ts
  function overlay(target) {
    const host = target?.closest?.("dialog[open]") ?? document.body;
    let layer = Array.from(host.children).find((child) => child.hasAttribute("data-sigta-overlay"));
    if (!layer) {
      layer = document.createElement("div");
      layer.setAttribute("data-sigta-overlay", "");
      layer.setAttribute("aria-hidden", "true");
      layer.style.cssText = "position:fixed;inset:0;pointer-events:none;z-index:95;overflow:hidden";
      host.appendChild(layer);
    }
    return layer;
  }
  function follow(el, node) {
    const start = el.getBoundingClientRect();
    const step2 = () => {
      if (!node.isConnected) return;
      if (el.isConnected) {
        const now = el.getBoundingClientRect();
        node.style.transform = `translate(${now.left - start.left}px, ${now.top - start.top}px)`;
      }
      requestAnimationFrame(step2);
    };
    requestAnimationFrame(step2);
  }
  function svgNode(name, attributes = {}) {
    const node = document.createElementNS("http://www.w3.org/2000/svg", name);
    Object.entries(attributes).forEach(([key, value]) => node.setAttribute(key, String(value)));
    return node;
  }
  function burst(target, options = {}) {
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
    const svg = svgNode("svg", {
      width,
      height,
      viewBox: `${-width / 2} ${-height / 2} ${width} ${height}`,
      fill: "none"
    });
    svg.style.cssText = `position:absolute;left:${rect.left + rect.width / 2 - width / 2}px;top:${rect.top + rect.height / 2 - height / 2}px;overflow:visible;color:${options.color ?? "var(--sigta-fg)"}`;
    const time = duration("--sigta-dur-draw", 420) * 1.3;
    const turn = Math.random() * Math.PI;
    for (let i = 0; i < count; i++) {
      const angle = turn + i / count * Math.PI * 2 + (Math.random() - 0.5) * 0.35;
      const x = Math.cos(angle) * rx;
      const y = Math.sin(angle) * ry;
      const nx = Math.cos(angle) / rx;
      const ny = Math.sin(angle) / ry;
      const n = Math.hypot(nx, ny);
      const length = reach * (0.65 + Math.random() * 0.5);
      const stroke = svgNode("path", {
        d: `M${x.toFixed(2)} ${y.toFixed(2)}L${(x + nx / n * length).toFixed(2)} ${(y + ny / n * length).toFixed(2)}`,
        pathLength: 100,
        stroke: "currentColor",
        "stroke-width": options.width ?? 2,
        "stroke-linecap": "round",
        "stroke-dasharray": "100 200",
        "stroke-dashoffset": "101"
      });
      svg.appendChild(stroke);
      stroke.animate(
        [{ strokeDashoffset: "101" }, { strokeDashoffset: "0", offset: 0.45 }, { strokeDashoffset: "-101" }],
        { duration: time, delay: i % 2 * 40, easing: pen(el), fill: "both" }
      );
    }
    overlay(el).appendChild(svg);
    follow(el, svg);
    setTimeout(() => svg.remove(), time + 200);
  }
  function ring(target, options = {}) {
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
    let d = "";
    for (let i = 0; i <= steps; i++) {
      const t = start + i / steps * sweep;
      const r = 1 + Math.sin(t * 2 + phase) * wobble + i / steps * 0.07;
      const x = Math.cos(t) * rx * r;
      const y = Math.sin(t) * ry * r;
      d += `${i ? "L" : "M"}${(x * Math.cos(tilt) - y * Math.sin(tilt)).toFixed(2)} ${(x * Math.sin(tilt) + y * Math.cos(tilt)).toFixed(2)}`;
    }
    const width = rx * 2.3 + 16;
    const height = ry * 2.3 + 16;
    const svg = svgNode("svg", {
      width,
      height,
      viewBox: `${-width / 2} ${-height / 2} ${width} ${height}`,
      fill: "none"
    });
    svg.style.cssText = `position:absolute;left:${rect.left + rect.width / 2 - width / 2}px;top:${rect.top + rect.height / 2 - height / 2}px;overflow:visible;color:${options.color ?? "var(--sigta-fg)"}`;
    const path = svgNode("path", {
      d,
      pathLength: 100,
      stroke: "currentColor",
      "stroke-width": options.width ?? 2,
      "stroke-linecap": "round",
      "stroke-linejoin": "round",
      "stroke-dasharray": "100 200",
      "stroke-dashoffset": "101"
    });
    svg.appendChild(path);
    overlay(el).appendChild(svg);
    follow(el, svg);
    const draw = duration("--sigta-dur-draw", 420) * 1.6;
    const hold = options.hold ?? 900;
    path.animate([{ strokeDashoffset: "101" }, { strokeDashoffset: "0" }], { duration: draw, easing: pen(el), fill: "forwards" });
    settled(svg.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 360, delay: draw + hold, fill: "forwards" })).then(() => svg.remove());
  }
  function note(target, text = "nice!", options = {}) {
    const el = elementOf(target);
    if (reduced() || !el?.isConnected || !text) return;
    const rect = el.getBoundingClientRect();
    const holder = document.createElement("div");
    holder.style.cssText = `position:absolute;left:${rect.right + 6}px;top:${rect.top - 16}px`;
    const word = document.createElement("span");
    word.textContent = text;
    word.className = "sigta-write";
    word.style.cssText = `display:inline-block;font-family:var(--sigta-font-hand);font-weight:600;font-size:${options.size ?? "1.4rem"};line-height:1;white-space:nowrap;color:${options.color ?? "var(--sigta-fg)"};rotate:${options.rotate ?? -6}deg`;
    holder.appendChild(word);
    overlay(el).appendChild(holder);
    const box = holder.getBoundingClientRect();
    if (box.right > window.innerWidth - 8) holder.style.left = `${Math.max(8, rect.left - box.width - 6)}px`;
    if (box.top < 8) holder.style.top = `${rect.bottom + 4}px`;
    follow(el, holder);
    settled(word.animate(
      [{ translate: "0 0", opacity: 1 }, { translate: "0 -0.6rem", opacity: 0 }],
      { duration: 520, delay: options.hold ?? 1100, easing: "ease-in", fill: "forwards" }
    )).then(() => holder.remove());
  }
  function confetti(target = null, options = {}) {
    if (reduced()) return;
    const el = elementOf(target);
    const layer = overlay(el);
    const canvas = document.createElement("canvas");
    const ratio = Math.min(window.devicePixelRatio || 1, 2);
    const width = window.innerWidth;
    const height = window.innerHeight;
    canvas.width = width * ratio;
    canvas.height = height * ratio;
    canvas.style.cssText = "position:absolute;inset:0;width:100%;height:100%";
    layer.appendChild(canvas);
    const ctx = canvas.getContext("2d");
    if (!ctx) return;
    ctx.scale(ratio, ratio);
    const ink = token("--sigta-fg", "black");
    const paper = token("--sigta-surface-raised", "white");
    const palette = [
      ink,
      ink,
      ink,
      token("--sigta-fg-muted", "gray"),
      token("--sigta-fg-muted", "gray"),
      token("--sigta-line", "silver"),
      token("--sigta-line", "silver"),
      paper,
      paper,
      token("--sigta-accent", "gold"),
      token("--sigta-accent", "gold")
    ];
    const rect = el?.getBoundingClientRect();
    const originX = rect ? rect.left + rect.width / 2 : width / 2;
    const originY = rect ? rect.top + rect.height / 2 : height * 0.3;
    const kinds = ["slip", "slip", "slip", "dot", "squiggle"];
    const pieces = Array.from({ length: options.count ?? 90 }, () => {
      const angle = -Math.PI / 2 + (Math.random() - 0.5) * Math.PI * 1.15;
      const speed = 5 + Math.random() * 10;
      const color = palette[Math.floor(Math.random() * palette.length)];
      return {
        x: originX,
        y: originY,
        vx: Math.cos(angle) * speed,
        vy: Math.sin(angle) * speed,
        spin: Math.random() * Math.PI * 2,
        vspin: (Math.random() - 0.5) * 0.35,
        flutter: Math.random() * Math.PI * 2,
        kind: kinds[Math.floor(Math.random() * kinds.length)],
        w: 6 + Math.random() * 7,
        h: 3 + Math.random() * 3,
        color,
        outline: color === paper
      };
    });
    const life = options.duration ?? 2400;
    const began = performance.now();
    let last = began;
    const frame = (now) => {
      const t = now - began;
      const step2 = Math.min(2.5, (now - last) / 16.7);
      last = now;
      ctx.clearRect(0, 0, width, height);
      ctx.globalAlpha = t > life * 0.7 ? Math.max(0, 1 - (t - life * 0.7) / (life * 0.3)) : 1;
      for (const p of pieces) {
        p.vx *= Math.pow(0.985, step2);
        p.vy = p.vy * Math.pow(0.985, step2) + 0.3 * step2;
        p.flutter += 0.13 * step2;
        p.x += (p.vx + Math.sin(p.flutter) * 0.7) * step2;
        p.y += p.vy * step2;
        p.spin += p.vspin * step2;
        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate(p.spin);
        if (p.kind === "slip") {
          ctx.scale(1, Math.cos(p.flutter));
          ctx.fillStyle = p.color;
          ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
          if (p.outline) {
            ctx.strokeStyle = ink;
            ctx.lineWidth = 0.8;
            ctx.strokeRect(-p.w / 2, -p.h / 2, p.w, p.h);
          }
        } else if (p.kind === "dot") {
          ctx.fillStyle = p.outline ? ink : p.color;
          ctx.beginPath();
          ctx.arc(0, 0, p.h * 0.55, 0, Math.PI * 2);
          ctx.fill();
        } else {
          ctx.strokeStyle = p.outline ? ink : p.color;
          ctx.lineWidth = 1.8;
          ctx.lineCap = "round";
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
  }
  function celebrate(target = null, level = "small", options = {}) {
    const el = elementOf(target);
    if (level === "big") {
      confetti(el, options);
      if (el && options.text !== false) note(el, options.text ?? "well done!", options);
    } else if (level === "medium") {
      ring(el, options);
      if (options.text !== false) note(el, options.text ?? "nice!", options);
    } else {
      burst(el, options);
    }
  }

  // packages/core/src/fold.ts
  var edges = ["paddingTop", "paddingBottom", "marginTop", "marginBottom", "borderTopWidth", "borderBottomWidth"];
  var foldable = (style) => !["table-row", "contents", "inline"].includes(style.display);
  function shape(el, style) {
    const open = { height: `${el.getBoundingClientRect().height}px` };
    const shut = { height: "0px" };
    edges.forEach((edge) => {
      open[edge] = style[edge];
      shut[edge] = "0px";
    });
    return [open, shut];
  }
  function fold(el) {
    if (!el?.isConnected || reduced()) return Promise.resolve();
    const style = getComputedStyle(el);
    const time = duration("--sigta-dur-leave", 360, el);
    el.style.pointerEvents = "none";
    if (!foldable(style)) {
      return settled(el.animate([{ opacity: 1 }, { opacity: 0 }], { duration: time, fill: "forwards" }));
    }
    const [open, shut] = shape(el, style);
    el.style.overflow = "hidden";
    el.style.boxSizing = "border-box";
    return settled(el.animate([
      { ...open, opacity: 1, translate: "0 0", offset: 0 },
      { ...open, opacity: 0, translate: "1.25rem 0", offset: 0.45 },
      { ...shut, opacity: 0, translate: "1.25rem 0", offset: 1 }
    ], { duration: time * 1.6, easing: "cubic-bezier(.4, 0, .2, 1)", fill: "forwards" }));
  }
  function unfold(el) {
    if (!el?.isConnected || reduced()) return Promise.resolve();
    const style = getComputedStyle(el);
    const time = duration("--sigta-dur-draw", 420, el);
    if (!foldable(style)) {
      return settled(el.animate([{ opacity: 0 }, { opacity: 1 }], { duration: time }));
    }
    const [open, shut] = shape(el, style);
    const accent = token("--sigta-accent", "gold", el);
    el.style.overflow = "hidden";
    el.animate(
      [{ boxShadow: `inset 0 0 0 100vmax color-mix(in srgb, ${accent} 45%, transparent)` }, { boxShadow: "inset 0 0 0 100vmax transparent" }],
      { duration: 1600, delay: time * 0.6, easing: "ease-out" }
    );
    return settled(el.animate([
      { ...shut, opacity: 0, translate: "0 -0.35rem", offset: 0 },
      { ...open, opacity: 0, translate: "0 -0.35rem", offset: 0.45 },
      { ...open, opacity: 1, translate: "0 0", offset: 1 }
    ], { duration: time * 1.4, easing: settle(el) })).then(() => {
      el.style.overflow = "";
    });
  }
  function detach(el) {
    const before = el.previousSibling;
    const after = el.nextSibling;
    el.remove();
    if (before?.nodeType === Node.TEXT_NODE && after?.nodeType === Node.TEXT_NODE) {
      before.nodeValue += after.nodeValue ?? "";
      after.remove();
    }
  }
  async function leave(target, options = {}) {
    const el = elementOf(target);
    if (!el?.isConnected) return;
    el.setAttribute("data-sigta-leaving", "");
    await fold(el);
    if (!el.hasAttribute("data-sigta-leaving")) return;
    if (options.remove !== false) detach(el);
  }
  function revive(el) {
    el.removeAttribute("data-sigta-leaving");
    el.getAnimations().forEach((animation) => animation.cancel());
    ["pointerEvents", "overflow", "boxSizing"].forEach((property) => {
      el.style[property] = "";
    });
  }
  function shake(el) {
    if (reduced() || !el?.isConnected) return;
    el.animate(
      [{ translate: "0 0" }, { translate: "-5px 0" }, { translate: "4px 0" }, { translate: "-2px 0" }, { translate: "0 0" }],
      { duration: 380, easing: "ease-in-out" }
    );
  }

  // packages/core/src/complete.ts
  var ROW_SELECTOR = "[data-sigta-row], [data-sigta-motion] > *";
  function rowOf(target, rowSelector) {
    const el = elementOf(target);
    const selector = rowSelector ? `${ROW_SELECTOR}, ${rowSelector}` : ROW_SELECTOR;
    return el?.closest?.(selector) ?? el;
  }
  async function complete(target, commit = null, options = {}) {
    const row = rowOf(target, options.rowSelector);
    if (!row || row.hasAttribute("data-sigta-completing")) return false;
    const source = elementOf(target);
    const quiet = reduced();
    row.setAttribute("data-sigta-done", "");
    row.setAttribute("data-sigta-completing", "");
    let stamp = null;
    if (!quiet) {
      const from = source?.matches?.("input, button") ? source : row.querySelector('input[type="checkbox"]') ?? source;
      burst(from, { reach: 10 });
      if (options.stamp) {
        stamp = document.createElement("span");
        stamp.className = "sigta-stamp";
        stamp.setAttribute("aria-hidden", "true");
        stamp.textContent = options.stamp === true ? "Done" : options.stamp;
        if (getComputedStyle(row).position === "static") row.style.position = "relative";
        row.appendChild(stamp);
      }
    }
    const marked = wait(quiet ? 0 : duration("--sigta-dur-draw", 420, row) + 280);
    const run = () => Promise.resolve(typeof commit === "function" ? commit() : commit);
    try {
      if (options.atOnce) {
        const committed = run().finally(() => row.removeAttribute("data-sigta-completing"));
        await Promise.all([committed, marked]);
        await options.onCommitted?.(row);
      } else {
        await marked;
        if (options.leave !== false) {
          row.setAttribute("data-sigta-leaving", "");
          await fold(row);
        }
        await run();
        row.removeAttribute("data-sigta-completing");
        if (options.leave !== false && !commit) row.remove();
      }
      return true;
    } catch (error) {
      row.getAnimations().forEach((animation) => animation.cancel());
      row.style.pointerEvents = "";
      row.style.overflow = "";
      row.removeAttribute("data-sigta-completing");
      row.removeAttribute("data-sigta-done");
      stamp?.remove();
      if (source instanceof HTMLInputElement && source.type === "checkbox") source.checked = false;
      shake(row);
      console.error(error);
      return false;
    }
  }

  // packages/core/src/observe.ts
  function landLoader(loader) {
    if (reduced() || loader.dataset.variant !== "compass" || loader.hasAttribute("data-landing")) return;
    loader.setAttribute("data-landing", "");
    const deg = parseFloat(getComputedStyle(loader).getPropertyValue("--sigta-loader-deg")) || 0;
    const end = Math.max(360, Math.ceil(deg / 360 - 1e-6) * 360);
    const turn = 380 + 520 * ((end - deg) / 360);
    const rise = 320;
    const ease = settle(loader);
    const landing = loader.animate([
      { "--sigta-loader-deg": String(deg), "--sigta-loader-fill": "0", "--sigta-loader-rise": "0", easing: ease },
      { "--sigta-loader-deg": String(end), "--sigta-loader-fill": "1", "--sigta-loader-rise": "0", offset: turn / (turn + rise), easing: ease },
      { "--sigta-loader-deg": String(end), "--sigta-loader-fill": "1", "--sigta-loader-rise": "1" }
    ], { duration: turn + rise, fill: "forwards" });
    settled(landing).then(() => {
      loader.removeAttribute("data-landing");
      landing.cancel();
    });
  }
  function observeLoaders(root = document.documentElement) {
    const observer = new MutationObserver((records) => records.forEach((record) => {
      const loader = record.target;
      if (loader.dataset?.sigta === "loader" && loader.dataset.state === "done" && record.oldValue === "working") landLoader(loader);
    }));
    observer.observe(root, { attributes: true, attributeFilter: ["data-state"], attributeOldValue: true, subtree: true });
    return () => observer.disconnect();
  }
  var busyTimers = /* @__PURE__ */ new WeakMap();
  function busyButtons(el) {
    if (el.matches("form")) return Array.from(el.querySelectorAll('[data-sigta="button"][type="submit"]'));
    return el.matches('[data-sigta="button"]') ? [el] : [];
  }
  function busyChanged(target) {
    const buttons = busyButtons(target);
    if (buttons.length === 0) return;
    clearTimeout(busyTimers.get(target));
    if (!target.hasAttribute("data-loading")) {
      buttons.forEach((button) => button.removeAttribute("data-sigta-busy"));
      return;
    }
    busyTimers.set(target, setTimeout(() => {
      if (target.hasAttribute("data-loading")) buttons.forEach((button) => button.setAttribute("data-sigta-busy", ""));
    }, duration("--sigta-dur-busy", 500, target)));
  }
  function observeBusy(root = document.documentElement) {
    const observer = new MutationObserver((records) => records.forEach(({ target }) => busyChanged(target)));
    observer.observe(root, { attributes: true, attributeFilter: ["data-loading"], subtree: true });
    return () => observer.disconnect();
  }
  function observeMotionLists(root = document.documentElement, ready = () => true) {
    const born = /* @__PURE__ */ new WeakMap();
    const observer = new MutationObserver((records) => {
      if (!ready()) return;
      const now = performance.now();
      const moved = /* @__PURE__ */ new Set();
      records.forEach((record) => record.removedNodes.forEach((node) => moved.add(node)));
      records.forEach((record) => {
        record.addedNodes.forEach((node) => {
          if (node.nodeType !== 1) return;
          const el = node;
          if (el.matches("[data-sigta-motion]")) born.set(el, now);
          el.querySelectorAll("[data-sigta-motion]").forEach((list) => born.set(list, now));
        });
      });
      records.forEach((record) => {
        const list = record.target;
        if (record.type !== "childList" || list.nodeType !== 1 || !list.hasAttribute("data-sigta-motion")) return;
        if (now - (born.get(list) ?? -Infinity) < 400) return;
        record.addedNodes.forEach((node) => {
          if (node.nodeType === 1 && !moved.has(node) && !node.hasAttribute("data-sigta-leaving")) unfold(node);
        });
      });
    });
    observer.observe(root, { childList: true, subtree: true });
    return () => observer.disconnect();
  }
  var numberIn = (el) => parseFloat((el.textContent ?? "").replace(/[^\d.-]/g, ""));
  function bumpCount(el, rise) {
    if (reduced()) return;
    el.animate([
      { translate: `0 ${rise ? "0.45em" : "-0.45em"}`, opacity: 0.2, scale: 1 },
      { translate: "0 0", opacity: 1, scale: 1.2, offset: 0.55 },
      { translate: "0 0", opacity: 1, scale: 1 }
    ], { duration: 440, easing: spring(el) });
  }
  function observeCount(el) {
    let last = numberIn(el);
    const observer = new MutationObserver(() => {
      const value = numberIn(el);
      if (Number.isNaN(value) || value === last) return;
      const rise = value > last;
      last = value;
      bumpCount(el, rise);
    });
    observer.observe(el, { childList: true, characterData: true, subtree: true });
    return () => observer.disconnect();
  }
  function observeJob(el) {
    let state = el.dataset.state;
    const observer = new MutationObserver(() => {
      const next = el.dataset.state;
      if (next === state) return;
      if (next === "done") burst(el.querySelector('[data-sigta="loader"]') ?? el, { reach: 12 });
      state = next;
    });
    observer.observe(el, { attributes: true, attributeFilter: ["data-state"] });
    return () => observer.disconnect();
  }

  // packages/core/src/toast.ts
  var TOAST_EVENT = "sigta-toast";
  var DEFAULT_TOAST_DURATION = 5e3;
  var TOAST_LEAVE_MS = 150;
  function toastDetail(messageOrOptions, options) {
    if (typeof messageOrOptions === "string") {
      return options && typeof options === "object" ? { text: messageOrOptions, ...options } : { text: messageOrOptions };
    }
    return messageOrOptions && typeof messageOrOptions === "object" ? messageOrOptions : {};
  }
  function toast(messageOrOptions, options) {
    if (!isBrowser()) return;
    window.dispatchEvent(new CustomEvent(TOAST_EVENT, { detail: toastDetail(messageOrOptions, options) }));
  }
  function pushToast(list, detail, id) {
    const kept = detail.key ? list.filter((t) => t.key !== detail.key) : list.slice();
    kept.push({
      key: detail.key ?? null,
      id,
      leaving: false,
      heading: detail.heading ?? null,
      text: detail.text ?? "",
      variant: detail.variant ?? "info",
      link: detail.link ?? null,
      action: detail.action ?? null
    });
    return kept;
  }
  var dropToast = (list, id) => list.filter((t) => t.id !== id);
  var leaveToast = (list, id) => list.map((t) => t.id === id ? { ...t, leaving: true } : t);
  var toastDuration = (detail) => detail.duration ?? DEFAULT_TOAST_DURATION;
  function runToastAction(action, dispatch = (event, detail) => window.dispatchEvent(new CustomEvent(event, { detail }))) {
    if (!action) return;
    if (typeof action.onClick === "function") action.onClick();
    if (action.event) dispatch(action.event, action.detail ?? {});
  }

  // packages/core/src/save.ts
  var SAVE_STATES = ["idle", "dirty", "saving", "saved", "failed"];
  var SAVE_EVENT = "sigta-save-state";
  var SAVED_HOLD = 2600;
  var isSaveState = (state) => SAVE_STATES.includes(state);
  function saveMark(state) {
    return { dirty: "queued", saving: "working", saved: "done", failed: "failed" }[state] ?? "idle";
  }
  function saveState(state, name = null) {
    if (!isBrowser()) return;
    window.dispatchEvent(new CustomEvent(SAVE_EVENT, { detail: { name, state } }));
  }
  function save(work, name = null) {
    saveState("saving", name);
    return Promise.resolve(typeof work === "function" ? work() : work).then(
      (value) => {
        saveState("saved", name);
        return value;
      },
      (error) => {
        saveState("failed", name);
        throw error;
      }
    );
  }

  // packages/core/src/floating.ts
  function positionPanel(anchor, panel, options = {}) {
    if (!anchor || !panel) return;
    const gap = options.gap ?? 6;
    const rect = anchor.getBoundingClientRect();
    const spaceBelow = window.innerHeight - rect.bottom;
    const flip = spaceBelow < panel.offsetHeight + gap && rect.top > spaceBelow;
    panel.style.position = "fixed";
    panel.style.left = `${Math.round(rect.left)}px`;
    if (options.matchWidth !== false) panel.style.width = `${Math.round(rect.width)}px`;
    if (flip) {
      panel.style.top = "auto";
      panel.style.bottom = `${Math.round(window.innerHeight - rect.top + gap)}px`;
    } else {
      panel.style.bottom = "auto";
      panel.style.top = `${Math.round(rect.bottom + gap)}px`;
    }
  }

  // packages/core/src/combobox.ts
  var matchesQuery = (text, query) => {
    const q = query.trim().toLowerCase();
    return q === "" || text.toLowerCase().includes(q);
  };
  var step = (current, delta, count) => count === 0 ? -1 : (current + delta + count) % count;
  function choose(value, chosen, multiple) {
    if (!multiple) return chosen;
    const list = Array.isArray(value) ? value : [];
    return list.includes(chosen) ? list.filter((v) => v !== chosen) : [...list, chosen];
  }
  var isChosen = (value, candidate) => Array.isArray(value) ? value.includes(candidate) : value === candidate;
  var serialize = (value) => Array.isArray(value) ? JSON.stringify(value) : value ?? "";

  // packages/core/src/globals.ts
  var CELEBRATE_EVENT = "sigta-celebrate";
  var baseApi = { toast, complete, leave, celebrate, burst, ring, note, confetti, save, saveState };
  function createApi(overrides = {}) {
    return { ...baseApi, ...overrides };
  }
  function installGlobals(api2) {
    if (!isBrowser()) return () => {
    };
    window.Sigta = Object.assign(window.Sigta || {}, api2);
    const onCelebrate = (event) => {
      const { level = "small", target = null, text } = event.detail ?? {};
      api2.celebrate(target, level, text === void 0 ? {} : { text });
    };
    window.addEventListener(CELEBRATE_EVENT, onCelebrate);
    return () => window.removeEventListener(CELEBRATE_EVENT, onCelebrate);
  }

  // packages/core/src/format.ts
  var pad2 = (n) => String(n).padStart(2, "0");
  function toInt(value) {
    const n = Math.trunc(Number(value ?? 0));
    return Number.isFinite(n) ? n : 0;
  }
  function formatDuration(minutes, format = "compact") {
    const total = Math.max(0, toInt(minutes));
    const h = Math.floor(total / 60);
    const m = total % 60;
    if (format === "clock") return `${pad2(h)}:${pad2(m)}`;
    return h === 0 ? `${m}m` : m === 0 ? `${h}h` : `${h}h ${m}m`;
  }
  function shift(value, places) {
    const [mantissa, exponent = "0"] = String(value).split("e");
    return Number(`${mantissa}e${Number(exponent) + places}`);
  }
  function numberFormat(value, decimals = 0, decimal = ".", thousands = ",") {
    const places = Math.max(0, toInt(decimals));
    const abs = Math.abs(Number(value) || 0);
    const pre = Number(abs.toPrecision(15));
    const rounded = Math.round(shift(pre, places));
    const digits = places ? String(rounded).padStart(places + 1, "0") : String(rounded);
    const whole = places ? digits.slice(0, -places) : digits;
    const fraction = places ? digits.slice(-places) : "";
    const grouped = whole.replace(/\B(?=(\d{3})+(?!\d))/g, thousands);
    const sign = value < 0 && rounded !== 0 ? "-" : "";
    return sign + grouped + (places ? decimal + fraction : "");
  }
  function isNumeric(value) {
    if (typeof value === "number") return Number.isFinite(value);
    return typeof value === "string" && /^\s*[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?\s*$/.test(value);
  }
  function majorFromMinor(minor) {
    if (!isNumeric(minor)) return "";
    return numberFormat(toInt(minor) / 100, 2, ".", "").replace(/0+$/, "").replace(/\.$/, "");
  }
  function minorFromMajor(text) {
    const n = parseFloat(String(text ?? "").replace(",", "."));
    return Number.isFinite(n) ? Math.round(n * 100) : null;
  }
  function durationMinutes(value) {
    return isNumeric(value) ? Math.max(0, toInt(value)) : null;
  }
  function durationLabel(minutes, placeholder = "0m") {
    const total = durationMinutes(minutes) ?? 0;
    if (total <= 0) return placeholder;
    const h = Math.floor(total / 60);
    const m = total % 60;
    return h === 0 ? `${m}m` : m === 0 ? `${h}h` : `${h}h${m}m`;
  }
  function durationDraft(minutes) {
    const total = durationMinutes(minutes) ?? 0;
    return total > 0 ? formatDuration(total, "clock") : "";
  }
  function parseDuration(raw) {
    const s = (raw ?? "").trim();
    if (s === "") return 0;
    if (s.includes(":")) {
      const [hours, mins] = s.split(":");
      return Math.max(0, (parseInt(hours, 10) || 0) * 60 + (parseInt(mins, 10) || 0));
    }
    const n = parseInt(s, 10);
    return Number.isNaN(n) ? 0 : Math.max(0, n);
  }

  // packages/core/src/floating-bar.ts
  var FLOATING_SPACE = "--sigta-floating-space";
  function publishFloatingSpace(root, bar, visible) {
    if (!isBrowser()) return;
    const bottom = parseFloat(getComputedStyle(root).paddingBottom) || 0;
    const space = visible ? bar.offsetHeight + bottom * 2 : 0;
    document.documentElement.style.setProperty(FLOATING_SPACE, `${space}px`);
  }
  function clearFloatingSpace() {
    if (!isBrowser()) return;
    document.documentElement.style.removeProperty(FLOATING_SPACE);
  }

  // packages/core/src/calendar.ts
  var MONTH_NAMES = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  var SHORT_MONTH_NAMES = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
  var pad22 = (n) => String(n).padStart(2, "0");
  function utc(year, month, day) {
    const date = /* @__PURE__ */ new Date(0);
    date.setUTCFullYear(year, month - 1, day);
    return date;
  }
  var fromUtc = (date) => `${String(date.getUTCFullYear()).padStart(4, "0")}-${pad22(date.getUTCMonth() + 1)}-${pad22(date.getUTCDate())}`;
  function isoDate(year, month, day) {
    return fromUtc(utc(year, month, day));
  }
  function parseIso(iso) {
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(iso ?? ""));
    if (!match) return null;
    const [year, month, day] = [Number(match[1]), Number(match[2]), Number(match[3])];
    return isoDate(year, month, day) === iso ? { year, month, day } : null;
  }
  function todayIso(now = Date.now()) {
    const date = now instanceof Date ? now : new Date(now);
    return `${String(date.getFullYear()).padStart(4, "0")}-${pad22(date.getMonth() + 1)}-${pad22(date.getDate())}`;
  }
  function daysInMonth(year, month) {
    return utc(year, month + 1, 0).getUTCDate();
  }
  function addDays(iso, days) {
    const p = parseIso(iso);
    return p ? isoDate(p.year, p.month, p.day + days) : iso;
  }
  function addMonths(iso, months) {
    const p = parseIso(iso);
    if (!p) return iso;
    const target = shiftMonth(p, months);
    return isoDate(target.year, target.month, Math.min(p.day, daysInMonth(target.year, target.month)));
  }
  function shiftMonth(view, delta) {
    const index = view.year * 12 + (view.month - 1) + delta;
    return { year: Math.floor(index / 12), month: (index % 12 + 12) % 12 + 1 };
  }
  function weekdayIndex(iso) {
    const p = parseIso(iso);
    return p ? (utc(p.year, p.month, p.day).getUTCDay() + 6) % 7 : 0;
  }
  function isOutOfRange(iso, min, max) {
    return !!min && iso < min || !!max && iso > max;
  }
  function monthOf(iso) {
    const p = parseIso(iso);
    return p ? { year: p.year, month: p.month } : { year: 1970, month: 1 };
  }
  function monthTitle(year, month) {
    return `${MONTH_NAMES[month - 1]} ${year}`;
  }
  function dayLabel(iso) {
    const p = parseIso(iso);
    return p ? `${p.day} ${MONTH_NAMES[p.month - 1]} ${p.year}` : "";
  }
  function formatDate(iso) {
    const p = parseIso(iso);
    return p ? `${p.day} ${SHORT_MONTH_NAMES[p.month - 1]} ${p.year}` : "";
  }
  function parseDate(text) {
    const typed = String(text ?? "").trim();
    if (!typed) return "";
    if (/^\d{4}-\d{2}-\d{2}$/.test(typed)) return parseIso(typed) ? typed : null;
    const ts = Date.parse(typed);
    return Number.isNaN(ts) ? null : todayIso(ts);
  }
  function monthGrid(year, month, { selected = null, min = null, max = null, today = null } = {}) {
    const offset = weekdayIndex(isoDate(year, month, 1));
    const cells = [];
    for (let i = 0; i < 42; i++) {
      const date = isoDate(year, month, 1 - offset + i);
      const p = parseIso(date);
      cells.push({
        date,
        day: p.day,
        outside: p.month !== month || p.year !== year,
        disabled: isOutOfRange(date, min, max),
        selected: selected === date,
        today: today === date,
        label: dayLabel(date)
      });
    }
    return cells;
  }
  function canShowPrev(view, min) {
    return !min || isoDate(view.year, view.month, 0) >= min;
  }
  function canShowNext(view, max) {
    return !max || isoDate(view.year, view.month + 1, 1) <= max;
  }
  function initialFocus(view, { selected = null, min = null, max = null, today = null } = {}) {
    const inView = (iso) => {
      const p = parseIso(iso);
      return !!p && p.year === view.year && p.month === view.month && !isOutOfRange(iso, min, max);
    };
    if (inView(selected)) return selected;
    if (inView(today)) return today;
    for (let day = 1, last = daysInMonth(view.year, view.month); day <= last; day++) {
      const iso = isoDate(view.year, view.month, day);
      if (!isOutOfRange(iso, min, max)) return iso;
    }
    return isoDate(view.year, view.month, 1);
  }
  function keyTarget(focused, key) {
    switch (key) {
      case "ArrowLeft":
        return addDays(focused, -1);
      case "ArrowRight":
        return addDays(focused, 1);
      case "ArrowUp":
        return addDays(focused, -7);
      case "ArrowDown":
        return addDays(focused, 7);
      case "Home":
        return addDays(focused, -weekdayIndex(focused));
      case "End":
        return addDays(focused, 6 - weekdayIndex(focused));
      case "PageUp":
        return addMonths(focused, -1);
      case "PageDown":
        return addMonths(focused, 1);
      default:
        return null;
    }
  }

  // packages/core/src/time.ts
  var pad23 = (n) => String(n).padStart(2, "0");
  var int = (text) => {
    const n = Number.parseInt(String(text ?? ""), 10);
    return Number.isFinite(n) ? n : 0;
  };
  function timeToMinutes(value) {
    const [h, m] = String(value).split(":");
    return int(h) * 60 + int(m);
  }
  function minutesToTime(minutes) {
    return `${pad23(Math.floor(minutes / 60))}:${pad23(minutes % 60)}`;
  }
  function formatTime(value, format = "24") {
    if (!value) return "";
    const [h, m] = String(value).split(":").map(int);
    if (String(format) === "12") return `${h % 12 || 12}:${pad23(m)} ${h < 12 ? "AM" : "PM"}`;
    return `${pad23(h)}:${pad23(m)}`;
  }
  function parseTime(text) {
    const typed = String(text ?? "").trim();
    if (!typed) return "";
    const match = /^(\d{1,2}):(\d{2})\s*(am|pm)?$/i.exec(typed);
    if (!match) return null;
    let h = Number(match[1]);
    const m = Number(match[2]);
    const meridiem = match[3]?.toLowerCase();
    if (meridiem) {
      if (h === 12) h = 0;
      if (meridiem === "pm") h += 12;
    }
    return h < 24 && m < 60 ? `${pad23(h)}:${pad23(m)}` : null;
  }
  function isTimeOutOfRange(value, min, max) {
    const minutes = timeToMinutes(value);
    return !!min && minutes < timeToMinutes(min) || !!max && minutes > timeToMinutes(max);
  }
  function timeStep(step2) {
    return Math.max(1, Math.trunc(Number(step2 ?? 30)) || 0);
  }
  function timeOptions({ min = null, max = null, step: step2 = 30, format = "24" } = {}) {
    const every = timeStep(step2);
    const start = min ? timeToMinutes(min) : 0;
    const end = max ? timeToMinutes(max) : 23 * 60 + 59;
    const options = [];
    for (let minutes = start; minutes <= end; minutes += every) {
      const value = minutesToTime(minutes);
      options.push({ value, label: formatTime(value, format ?? "24") });
    }
    return options;
  }
  function moveActive(index, delta, count) {
    if (count <= 0) return -1;
    if (index < 0) return 0;
    return ((index + delta) % count + count) % count;
  }
  function activeFor(options, value) {
    if (!options.length) return -1;
    const index = options.findIndex((option) => option.value === value);
    return index >= 0 ? index : 0;
  }

  // packages/core/src/carousel.ts
  function slideOffsets(track) {
    const slides = Array.from(track.children);
    const base = slides[0]?.offsetLeft ?? 0;
    return slides.map((slide) => slide.offsetLeft - base);
  }
  function nearestSlide(offsets, scrollLeft) {
    let best = 0;
    let min = Infinity;
    offsets.forEach((offset, index) => {
      const distance = Math.abs(offset - scrollLeft);
      if (distance < min) {
        min = distance;
        best = index;
      }
    });
    return best;
  }
  function activeSlide(track) {
    return nearestSlide(slideOffsets(track), track.scrollLeft);
  }
  function scrollToSlide(track, index) {
    const target = track.children[index];
    if (!target) return;
    track.scrollTo({ left: slideOffsets(track)[index], behavior: reduced() ? "auto" : "smooth" });
  }

  // packages/core/src/autosize.ts
  function autosize(el, maxRows) {
    el.style.height = "auto";
    const line = parseFloat(getComputedStyle(el).lineHeight) || 20;
    const max = line * maxRows;
    el.style.height = Math.min(el.scrollHeight, max) + "px";
    el.style.overflowY = el.scrollHeight > max ? "auto" : "hidden";
  }

  // packages/core/src/files.ts
  var UNITS = ["B", "KB", "MB", "GB", "TB"];
  function formatBytes(bytes) {
    let n = bytes;
    let i = 0;
    while (n >= 1024 && i < UNITS.length - 1) {
      n /= 1024;
      i++;
    }
    return (i === 0 ? n : n.toFixed(n >= 10 ? 0 : 1)) + " " + UNITS[i];
  }
  function fileEntries(files) {
    return Array.from(files ?? []).map((file) => ({ name: file.name, size: formatBytes(file.size) }));
  }

  // packages/core/src/kanban.ts
  function dropBefore(spans, pointer) {
    let best = -1;
    let closest = Number.NEGATIVE_INFINITY;
    spans.forEach((span, index) => {
      const offset = pointer - span.start - span.size / 2;
      if (offset < 0 && offset > closest) {
        closest = offset;
        best = index;
      }
    });
    return best;
  }
  var vertical = (el) => {
    const box = el.getBoundingClientRect();
    return { start: box.top, size: box.height };
  };
  var escape = (id) => typeof CSS !== "undefined" && CSS.escape ? CSS.escape(id) : id.replace(/['\\\n]/g, (ch) => `\\${ch === "\n" ? "a " : ch}`);
  var horizontal = (el) => {
    const box = el.getBoundingClientRect();
    return { start: box.left, size: box.width };
  };
  function cardElement(board, id) {
    if (!id) return null;
    return board.querySelector(`[data-card-id='${escape(id)}']`);
  }
  function placeCard(body, card, y) {
    const cards = Array.from(body.querySelectorAll("[data-card-id]:not([data-dragging])"));
    const index = dropBefore(cards.map(vertical), y);
    if (index === -1) body.appendChild(card);
    else body.insertBefore(card, cards[index]);
  }
  function cardPosition(body, card) {
    return Array.from(body.querySelectorAll("[data-card-id]")).indexOf(card);
  }
  function columnElement(board, id) {
    if (!id) return null;
    return board.querySelector(`[data-sigta='kanban.column'][data-column='${escape(id)}']`);
  }
  function columnUnit(board, el) {
    while (el && el.parentNode !== board) el = el.parentNode;
    return el;
  }
  function columnUnits(board) {
    return Array.from(board.children).filter(
      (child) => child.matches(`[data-sigta='kanban.column']`) || child.querySelector(`[data-sigta='kanban.column']`) !== null
    );
  }
  function placeColumn(board, unit, x) {
    const others = columnUnits(board).filter((other) => other !== unit);
    const index = dropBefore(others.map(horizontal), x);
    if (index !== -1) {
      board.insertBefore(unit, others[index]);
      return;
    }
    const units = columnUnits(board);
    const last = units[units.length - 1];
    if (last && last !== unit) board.insertBefore(unit, last.nextSibling);
  }
  function columnPosition(board, unit) {
    return unit ? columnUnits(board).indexOf(unit) : 0;
  }
  var KANBAN_MOVE_EVENT = "sigta-kanban-move";
  var KANBAN_COLUMN_MOVE_EVENT = "sigta-kanban-column-move";

  // resources/js/sigta.js
  var dropped = /* @__PURE__ */ new WeakSet();
  var complete2 = (target, commit = null, options = {}) => {
    const row = rowOf(target, "[wire\\:key]");
    const atOnce = options.defer === void 0 ? !!row?.closest("[wire\\:id]") : !options.defer;
    return complete(target, commit, {
      ...options,
      atOnce,
      rowSelector: "[wire\\:key]",
      onCommitted: (committed) => dropped.has(committed) ? leave(committed) : void 0
    });
  };
  var api = createApi({ complete: complete2 });
  document.addEventListener("alpine:init", () => {
    const Alpine = window.Alpine;
    if (!Alpine) return;
    Alpine.data("sigtaDropdown", () => ({
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
        if (focusTrigger) this.$refs.trigger?.querySelector("button, [href], input")?.focus?.() ?? this.$refs.trigger?.focus?.();
      },
      items() {
        return Array.from(this.$root.querySelectorAll('[role="menuitem"]:not([aria-disabled="true"])'));
      },
      focusItem(index) {
        const items = this.items();
        if (!items.length) return;
        items[(index % items.length + items.length) % items.length].focus();
      },
      move(delta) {
        const items = this.items();
        const current = items.indexOf(document.activeElement);
        this.focusItem(current === -1 ? delta > 0 ? 0 : -1 : current + delta);
      }
    }));
    Alpine.data("sigtaShellSidebar", () => ({
      open: false,
      drawer: false,
      init() {
        const narrow = window.matchMedia("(width < 48rem)");
        this.drawer = narrow.matches;
        narrow.addEventListener("change", (event) => {
          this.drawer = event.matches;
          if (!event.matches) this.open = false;
        });
        this.$watch("open", (open) => {
          window.dispatchEvent(new CustomEvent("sigta-shell-nav", { detail: { open } }));
        });
      },
      toggle() {
        this.open = !this.open;
      },
      close() {
        this.open = false;
      }
    }));
    Alpine.data("sigtaCarousel", () => ({
      active: 0,
      count: 0,
      init() {
        this.count = this.$refs.track.children.length;
        this.$nextTick(() => this.sync());
      },
      sync() {
        this.active = activeSlide(this.$refs.track);
      },
      go(index) {
        scrollToSlide(this.$refs.track, index);
      },
      prev() {
        this.go(Math.max(0, this.active - 1));
      },
      next() {
        this.go(Math.min(this.count - 1, this.active + 1));
      },
      atStart() {
        return this.active <= 0;
      },
      atEnd() {
        return this.active >= this.count - 1;
      }
    }));
    Alpine.data("sigtaComposer", () => ({
      text: "",
      maxRows: 6,
      init() {
        this.$nextTick(() => this.resize());
      },
      resize() {
        autosize(this.$refs.input, this.maxRows);
      },
      submit() {
        const value = this.text.trim();
        if (!value) return;
        this.$refs.input.dispatchEvent(new CustomEvent("sigta-composer-submit", { detail: { text: value }, bubbles: true, composed: true, cancelable: true }));
        this.text = "";
        this.$nextTick(() => this.resize());
      }
    }));
    Alpine.data("sigtaFileUpload", () => ({
      dragging: 0,
      files: [],
      update(input) {
        this.files = fileEntries(input.files);
      },
      drop(event) {
        this.dragging = 0;
        if (!event.dataTransfer?.files?.length) return;
        this.$refs.input.files = event.dataTransfer.files;
        this.$refs.input.dispatchEvent(new Event("change", { bubbles: true }));
      }
    }));
    Alpine.data("sigtaFloatingBar", () => ({
      visible: false,
      showObserver: null,
      sizeObserver: null,
      init() {
        this.visible = this.$root.dataset.show === "true";
        this.showObserver = new MutationObserver(() => {
          this.visible = this.$root.dataset.show === "true";
        });
        this.showObserver.observe(this.$root, { attributes: true, attributeFilter: ["data-show"] });
        this.sizeObserver = new ResizeObserver(() => this.publish());
        this.sizeObserver.observe(this.$refs.bar);
        this.$watch("visible", () => this.$nextTick(() => this.publish()));
        this.publish();
      },
      destroy() {
        this.showObserver?.disconnect();
        this.sizeObserver?.disconnect();
        clearFloatingSpace();
      },
      publish() {
        publishFloatingSpace(this.$root, this.$refs.bar, this.visible);
      }
    }));
    Alpine.data("sigtaModal", (name = null) => ({
      name,
      show() {
        this.$refs.dialog?.showModal();
      },
      hide() {
        this.$refs.dialog?.close();
      },
      matches(detail) {
        return !detail || detail.name === void 0 || detail.name === null || detail.name === this.name;
      },
      onShow(detail) {
        if (this.matches(detail)) this.show();
      },
      onHide(detail) {
        if (this.matches(detail)) this.hide();
      },
      backdropClick(event) {
        if (event.target === this.$refs.dialog) this.hide();
      }
    }));
    Alpine.data("sigtaTabs", (active = null) => ({
      active,
      init() {
        if (!this.active) this.active = this.tabs()[0]?.dataset.name ?? null;
      },
      tabs() {
        return Array.from(this.$root.querySelectorAll('[role="tab"]'));
      },
      select(name) {
        this.active = name;
      },
      isActive(name) {
        return this.active === name;
      },
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
      }
    }));
    Alpine.data("sigtaCombobox", (config = {}) => ({
      open: false,
      query: "",
      value: config.multiple ? config.initial ?? [] : config.initial ?? null,
      multiple: config.multiple ?? false,
      activeId: null,
      // How many options the last query kept (null until one is typed): the
      // "no matches" line reads it, since Alpine cannot watch the options' DOM.
      matches: null,
      init() {
        this.$watch("open", (open) => {
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
          el.dataset.active = "true";
          this.activeId = el.id;
          el.scrollIntoView({ block: "nearest" });
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
        this.$nextTick(() => this.positionPanel());
      },
      deselect(value) {
        if (!this.multiple) return;
        this.value = this.value.filter((v) => v !== value);
        this.syncInput();
        this.$nextTick(() => this.positionPanel());
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
        input.dispatchEvent(new Event("input", { bubbles: true }));
        input.dispatchEvent(new Event("change", { bubbles: true }));
      }
    }));
    Alpine.data("sigtaMoneyInput", () => ({
      major: "",
      currency: "",
      open: false,
      init() {
        this.major = majorFromMinor(this.$refs.total.value);
        this.currency = this.$refs.currency.value;
        this.sync();
      },
      sync() {
        const el = this.$refs.total;
        el.value = minorFromMajor(this.major) ?? "";
        el.dispatchEvent(new Event("input", { bubbles: true }));
      },
      selectCurrency(code) {
        this.currency = code;
        const el = this.$refs.currency;
        el.value = code;
        el.dispatchEvent(new Event("input", { bubbles: true }));
        this.open = false;
      }
    }));
    Alpine.data("sigtaDurationInput", (placeholder = "0m") => ({
      minutes: null,
      editing: false,
      draft: "",
      placeholder,
      init() {
        this.minutes = durationMinutes(this.$refs.total.value);
      },
      get hasValue() {
        return this.minutes !== null && this.minutes > 0;
      },
      get label() {
        return durationLabel(this.minutes, this.placeholder);
      },
      edit() {
        this.draft = durationDraft(this.minutes);
        this.editing = true;
        this.$nextTick(() => {
          this.$refs.editor.focus();
          this.$refs.editor.select();
        });
      },
      commit() {
        if (!this.editing) return;
        const total = parseDuration(this.draft);
        this.minutes = total > 0 ? total : null;
        const el = this.$refs.total;
        el.value = total > 0 ? total : "";
        el.dispatchEvent(new Event("input", { bubbles: true }));
        this.editing = false;
      },
      cancel() {
        this.editing = false;
      }
    }));
    Alpine.data("sigtaToasts", () => ({
      toasts: [],
      counter: 0,
      add(detail = {}) {
        const id = ++this.counter;
        this.toasts = pushToast(this.toasts, detail, id);
        const duration2 = toastDuration(detail);
        if (duration2 > 0) setTimeout(() => this.remove(id), duration2);
      },
      remove(id) {
        this.toasts = leaveToast(this.toasts, id);
        setTimeout(() => {
          this.toasts = dropToast(this.toasts, id);
        }, TOAST_LEAVE_MS);
      },
      act(toast2) {
        runToastAction(toast2.action, (event, detail) => {
          if (window.Livewire?.dispatch) window.Livewire.dispatch(event, detail);
          else window.dispatchEvent(new CustomEvent(event, { detail }));
        });
        this.remove(toast2.id);
      }
    }));
    Alpine.data("sigtaCount", () => ({
      stop: null,
      init() {
        this.stop = observeCount(this.$root);
      },
      destroy() {
        this.stop?.();
      }
    }));
    Alpine.data("sigtaSaveState", () => ({
      state: "idle",
      name: null,
      timer: null,
      component: null,
      onEvent: null,
      onInput: null,
      stopFollowing: null,
      init() {
        this.state = this.$root.dataset.state || "idle";
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
        this.component?.removeEventListener("input", this.onInput);
        clearTimeout(this.timer);
      },
      get mark() {
        return saveMark(this.state);
      },
      set(state) {
        if (!isSaveState(state)) return;
        clearTimeout(this.timer);
        this.state = state;
        if (state === "saved") this.timer = setTimeout(() => {
          this.state = "idle";
        }, SAVED_HOLD);
      },
      followLivewire() {
        const targets = (this.$root.dataset.target || "").split(",").map((t) => t.trim()).filter(Boolean);
        this.component = this.$root.closest("[wire\\:id]");
        const id = this.component?.getAttribute("wire:id");
        if (!targets.length || !id || !window.Livewire?.interceptMessage) return;
        const watched = (path) => targets.some((t) => path === t || path.startsWith(`${t}.`));
        const touches = (message) => (message.calls ?? []).some((call) => targets.includes(call.method)) || Object.keys(message.updates ?? {}).some(watched);
        this.onInput = (event) => {
          const model = event.target.getAttributeNames?.().find((attribute) => attribute.startsWith("wire:model"));
          if (model && watched(event.target.getAttribute(model)) && this.state !== "saving") this.set("dirty");
        };
        this.component.addEventListener("input", this.onInput);
        this.stopFollowing = window.Livewire.interceptMessage(({ message, onSuccess, onError, onFailure, onCancel }) => {
          if (message.component?.id !== id || !touches(message)) return;
          this.set("saving");
          onSuccess(({ payload } = {}) => {
            let snapshot = payload?.snapshot ?? {};
            try {
              if (typeof snapshot === "string") snapshot = JSON.parse(snapshot);
            } catch (e) {
              snapshot = {};
            }
            const errors = snapshot?.memo?.errors ?? {};
            this.set(Object.keys(errors).length ? "failed" : "saved");
          });
          onError(() => this.set("failed"));
          onFailure(() => this.set("failed"));
          onCancel(() => this.set("idle"));
        });
      }
    }));
    Alpine.data("sigtaJob", () => ({
      stop: null,
      init() {
        this.stop = observeJob(this.$root);
      },
      destroy() {
        this.stop?.();
      }
    }));
    const calendarGrids = /* @__PURE__ */ new Map();
    const calendarGrid = (year, month, bounds) => {
      const key = [year, month, bounds.selected, bounds.min, bounds.max, bounds.today].join("|");
      if (!calendarGrids.has(key)) {
        if (calendarGrids.size > 64) calendarGrids.clear();
        calendarGrids.set(key, monthGrid(year, month, bounds));
      }
      return calendarGrids.get(key);
    };
    Alpine.data("sigtaCalendar", (config = {}) => ({
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
          el.value = this.selected ?? "";
          el.dispatchEvent(new Event("input", { bubbles: true }));
          el.dispatchEvent(new Event("change", { bubbles: true }));
        }
        this.$dispatch("calendar-change", { value: this.selected, label: formatDate(this.selected) });
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
      }
    }));
    Alpine.data("sigtaDatePicker", (config = {}) => ({
      value: config.value ?? null,
      display: formatDate(config.value),
      min: config.min ?? null,
      max: config.max ?? null,
      commit() {
        const hidden = this.$refs.hidden;
        hidden.value = this.value ?? "";
        hidden.dispatchEvent(new Event("input", { bubbles: true }));
        hidden.dispatchEvent(new Event("change", { bubbles: true }));
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
        if (iso === null || iso && isOutOfRange(iso, this.min, this.max)) {
          this.display = formatDate(this.value);
        } else {
          this.value = iso || null;
          this.display = formatDate(this.value);
          const calendar = this.$root.querySelector('[data-sigta="calendar"]');
          if (calendar) Alpine.$data(calendar).setValue(this.value);
        }
        this.commit();
      }
    }));
    Alpine.data("sigtaTimePicker", (config = {}) => ({
      open: false,
      value: config.value ?? null,
      display: formatTime(config.value, config.format),
      format: String(config.format ?? "24"),
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
        this.$nextTick(() => this.$refs.list?.children[this.activeIndex]?.scrollIntoView({ block: "nearest" }));
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
        hidden.value = this.value ?? "";
        hidden.dispatchEvent(new Event("input", { bubbles: true }));
        hidden.dispatchEvent(new Event("change", { bubbles: true }));
      },
      // The text field changed: a time in range is taken, an emptied field clears, anything else reverts.
      typed() {
        const value = parseTime(this.display);
        if (value === null || value && isTimeOutOfRange(value, this.min, this.max)) {
          this.display = formatTime(this.value, this.format);
          return;
        }
        this.value = value || null;
        this.display = formatTime(this.value, this.format);
        this.commit();
      }
    }));
    Alpine.data("sigtaKanban", () => ({
      draggedId: null,
      fromColumn: null,
      overColumn: null,
      draggedColumn: null,
      draggedEl() {
        return cardElement(this.$root, this.draggedId);
      },
      start(id, event) {
        this.draggedId = id;
        const body = event.target.closest("[data-kanban-body]");
        this.fromColumn = body ? body.dataset.columnId : null;
        if (event.dataTransfer) {
          event.dataTransfer.effectAllowed = "move";
          try {
            event.dataTransfer.setData("text/plain", id);
          } catch (e) {
          }
        }
      },
      end() {
        this.draggedId = null;
        this.fromColumn = null;
        this.overColumn = null;
      },
      over(event, columnId) {
        if (!this.draggedId) return;
        if (event.dataTransfer) event.dataTransfer.dropEffect = "move";
        this.overColumn = columnId;
        const dragged = this.draggedEl();
        if (dragged) placeCard(event.currentTarget, dragged, event.clientY);
      },
      drop(event, columnId) {
        const body = event.currentTarget;
        const dragged = this.draggedEl();
        this.overColumn = null;
        if (!dragged) return;
        this.$dispatch(KANBAN_MOVE_EVENT, {
          card: this.draggedId,
          from: this.fromColumn,
          to: columnId,
          position: cardPosition(body, dragged)
        });
        this.end();
      },
      columnEl() {
        return columnElement(this.$root, this.draggedColumn);
      },
      columnStart(id, event) {
        this.draggedColumn = id;
        if (event.dataTransfer) {
          event.dataTransfer.effectAllowed = "move";
          try {
            event.dataTransfer.setData("text/plain", id);
          } catch (e) {
          }
          const col = this.columnEl();
          if (col) {
            try {
              event.dataTransfer.setDragImage(col, 24, 24);
            } catch (e) {
            }
          }
        }
        event.stopPropagation();
      },
      columnOver(event) {
        if (!this.draggedColumn) return;
        event.preventDefault();
        if (event.dataTransfer) event.dataTransfer.dropEffect = "move";
        const col = this.columnEl();
        if (col) placeColumn(this.$root, columnUnit(this.$root, col), event.clientX);
      },
      columnDrop(event) {
        if (!this.draggedColumn) return;
        event.preventDefault();
        const id = this.draggedColumn;
        const col = this.columnEl();
        const position = columnPosition(this.$root, col ? columnUnit(this.$root, col) : null);
        this.columnEnd();
        this.$dispatch(KANBAN_COLUMN_MOVE_EVENT, { column: id, position });
      },
      columnEnd() {
        this.draggedColumn = null;
      }
    }));
    observeLoaders();
    observeBusy();
    let ready = false;
    document.addEventListener("alpine:initialized", () => requestAnimationFrame(() => {
      ready = true;
    }));
    observeMotionLists(document.documentElement, () => ready);
    if (window.Livewire?.hook) {
      const keyOf = (el) => el?.getAttribute?.("wire:key") || el?.id || null;
      const lookup = (root, key) => root.querySelector(`[wire\\:key="${CSS.escape(key)}"]`) ?? root.querySelector(`[id="${CSS.escape(key)}"]`);
      const held = /* @__PURE__ */ new WeakSet();
      const leaving = /* @__PURE__ */ new Set();
      const undone = /* @__PURE__ */ new Set();
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
      window.Livewire.hook("morph", ({ el, toEl }) => {
        const owner = el.closest("[wire\\:id]");
        const rows = /* @__PURE__ */ new Set([
          ...el.querySelectorAll("[data-sigta-motion] > *"),
          ...el.querySelectorAll("[data-sigta-completing]")
        ]);
        Array.from(rows).filter((row) => row.parentElement?.closest("[wire\\:id]") === owner).sort((a, b) => a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1).forEach((row) => {
          const key = keyOf(row);
          if (!key) return;
          const twin = lookup(toEl, key);
          if (twin) {
            dropped.delete(row);
            if (row.hasAttribute("data-sigta-leaving")) revive(row);
            if (row.hasAttribute("data-sigta-completing")) {
              twin.setAttribute("data-sigta-done", "");
              twin.setAttribute("data-sigta-completing", "");
            } else if (row.hasAttribute("data-sigta-done") && !twin.hasAttribute("data-sigta-done")) {
              undone.add(row);
            }
            return;
          }
          dropped.add(row);
          if (!rehome(row, toEl)) held.add(row);
          leaving.add(row);
        });
      });
      window.Livewire.hook("morph.removing", ({ el, skip }) => {
        if (held.has(el)) skip();
      });
      window.Livewire.hook("morph.updating", ({ el, toEl }) => {
        if (el.hasAttribute?.("data-landing")) toEl.setAttribute("data-landing", "");
      });
      window.Livewire.hook("morphed", () => {
        undone.forEach((row) => row.querySelectorAll('input[type="checkbox"]').forEach((box) => {
          box.checked = box.hasAttribute("checked");
        }));
        undone.clear();
        leaving.forEach((row) => {
          if (row.isConnected && !row.hasAttribute("data-sigta-completing") && !row.hasAttribute("data-sigta-leaving")) leave(row);
        });
        leaving.clear();
      });
    }
    installGlobals(api);
    Alpine.magic("sigta", () => api);
  });
})();
