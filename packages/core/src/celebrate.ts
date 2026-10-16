/* ---------------------------------------------------------------------------
   Flourishes — ink on paper. A burst for the small "yes", a pen ring and a
   handwritten note for a finished step, paper confetti for a milestone.
   Each checks prefers-reduced-motion and quietly skips itself; they only
   ever decorate state the page already shows.
--------------------------------------------------------------------------- */
import { duration, elementOf, pen, reduced, settled, token, type Target } from './env.ts';

export interface FlourishOptions {
    color?: string;
    width?: number;
    gap?: number;
    reach?: number;
    count?: number;
    pad?: number;
    hold?: number;
    size?: string;
    rotate?: number;
    duration?: number;
    text?: string | false;
}

// Flourishes draw on one fixed layer above the page, outside every server-
// rendered region so no morph touches them; inside an open dialog when the
// target is in one, since a modal sits in the top layer above the page.
function overlay(target: Element | null): HTMLElement {
    const host = (target?.closest?.('dialog[open]') as HTMLElement | null) ?? document.body;
    let layer = Array.from(host.children).find((child) => child.hasAttribute('data-sigta-overlay')) as HTMLElement | undefined;
    if (!layer) {
        layer = document.createElement('div');
        layer.setAttribute('data-sigta-overlay', '');
        layer.setAttribute('aria-hidden', 'true');
        layer.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:95;overflow:hidden';
        host.appendChild(layer);
    }
    return layer;
}

// A flourish rides along with its element: placed once on the fixed layer,
// then shifted every frame by however far the element has moved since (a
// scroll, a layout change) until the flourish is gone. It stays where it
// was if the element leaves the page.
function follow(el: Element, node: HTMLElement | SVGElement): void {
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
}

function svgNode(name: string, attributes: Record<string, string | number> = {}): SVGElement {
    const node = document.createElementNS('http://www.w3.org/2000/svg', name);
    Object.entries(attributes).forEach(([key, value]) => node.setAttribute(key, String(value)));
    return node;
}

/* Burst — short ink strokes flick out from an element and lift off: the
   small "yes" for a ticked item, a starred thing, a sent message. They
   start on an ellipse hugging the element, so a wide button gets a burst
   along its edges rather than a far-flung circle. */
export function burst(target: Target, options: FlourishOptions = {}): void {
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
}

/* Ring — a pen circles what just happened, overshooting where it closes
   the way a hand does, holds a beat, and fades. */
export function ring(target: Target, options: FlourishOptions = {}): void {
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
}

/* Note — a handwritten word in the margin ("done!", "saved") written in
   beside an element; it floats up and fades. Decorative, so say the same
   thing in text or ARIA somewhere the page keeps. */
export function note(target: Target, text: string | false = 'nice!', options: FlourishOptions = {}): void {
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
}

/* Confetti — paper, not glitter: slips of paper, ink dots and a few
   highlighter squiggles tumble up and flutter down. For milestones (inbox
   zero, a finished import), never for every click. */
export function confetti(target: Target = null, options: FlourishOptions = {}): void {
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
    if (!ctx) return;
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
    const kinds = ['slip', 'slip', 'slip', 'dot', 'squiggle'] as const;
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
    const frame = (now: number) => {
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
}

export type CelebrateLevel = 'small' | 'medium' | 'big';

/* Celebrate — one call per size of moment:
     small  → a burst (a ticked item, a starred thing)
     medium → a pen ring and a handwritten note (a finished step)
     big    → paper confetti and a note (inbox zero, a finished import) */
export function celebrate(target: Target = null, level: CelebrateLevel = 'small', options: FlourishOptions = {}): void {
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
}
