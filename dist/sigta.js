/* Sigta UI — Livewire runtime. Built by scripts/build-livewire.js from resources/js/sigta.js and packages/core/src; do not edit. sources:68fee6d9c272cc21d29aff0ff9e446a89ea94c6a */
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
        if (options.leave !== false) await fold(row);
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
      heading: detail.heading ?? null,
      text: detail.text ?? "",
      variant: detail.variant ?? "info",
      link: detail.link ?? null,
      action: detail.action ?? null
    });
    return kept;
  }
  var dropToast = (list, id) => list.filter((t) => t.id !== id);
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
        document.documentElement.style.removeProperty("--sigta-floating-space");
      },
      publish() {
        const bottom = parseFloat(getComputedStyle(this.$root).paddingBottom) || 0;
        const space = this.visible ? this.$refs.bar.offsetHeight + bottom * 2 : 0;
        document.documentElement.style.setProperty("--sigta-floating-space", `${space}px`);
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
      }
    }));
    Alpine.data("sigtaCombobox", (config = {}) => ({
      open: false,
      query: "",
      value: config.multiple ? config.initial ?? [] : config.initial ?? null,
      multiple: config.multiple ?? false,
      activeId: null,
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
        input.dispatchEvent(new Event("input", { bubbles: true }));
        input.dispatchEvent(new Event("change", { bubbles: true }));
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
        this.toasts = dropToast(this.toasts, id);
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
