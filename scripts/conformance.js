// Conformance: every manifest example is rendered by both runtimes — Blade
// through the package booted in Testbench, Svelte through server-side
// rendering — and the two outputs must agree once normalized: the same
// elements, contract attributes, ARIA and text.
//
//   node scripts/conformance.js [component…] [--verbose] [--write <dir>]
//
// --write keeps each fixture's two renders (for the visual comparison).
import { execFileSync } from 'node:child_process';
import { existsSync, mkdirSync, readdirSync, readFileSync, rmSync, writeFileSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import { JSDOM } from 'jsdom';
import { createSigtaVite } from './sigta-vite.js';
import { startHarness } from './harness.js';
import { manifests, registry } from '../packages/contract/src/manifests/index.ts';
import { toBlade, toSvelte, svelteImports } from '../packages/contract/src/examples.ts';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const args = process.argv.slice(2);
const verbose = args.includes('--verbose');
const browserMode = args.includes('--browser');
const writeAt = args.includes('--write') ? args[args.indexOf('--write') + 1] : null;
const only = args.filter((arg, i) => !arg.startsWith('--') && args[i - 1] !== '--write');

/* -------- fixtures ---------------------------------------------------------- */
export function collectFixtures(filter = []) {
    const fixtures = [];
    for (const manifest of manifests) {
        if (filter.length && !filter.includes(manifest.name)) continue;
        manifest.examples.forEach((example, index) => {
            if (example.conformance === false) return;
            const base = {
                browserOnly: example.conformance === 'browser',
                interactions: example.interactions ?? [],
                id: `${manifest.name}--${index}`,
                component: manifest.name,
                label: example.label,
            };
            if (example.tree) {
                fixtures.push({
                    ...base,
                    blade: toBlade(example.tree, registry, { prefix: 'x-sigta::' }),
                    svelte: toSvelte(example.tree, registry),
                    imports: svelteImports(example.tree, registry),
                });
            } else if (example.blade && example.svelte && example.conformance === 'browser') {
                // Per-runtime snippets (bindings, scripts): compared by what they render.
                fixtures.push({ ...base, blade: example.blade, svelte: example.svelte, imports: null });
            }
        });
    }
    // Acceptance pages: whole pages built from many components, in both
    // runtimes (harness/acceptance/<page>.blade.php beside <page>.svelte),
    // compared full-page at a desk and on a phone.
    const acceptance = join(root, 'harness/acceptance');
    if (existsSync(acceptance) && (!filter.length || filter.includes('acceptance'))) {
        for (const file of readdirSync(acceptance).filter((f) => f.endsWith('.blade.php')).sort()) {
            const name = file.slice(0, -'.blade.php'.length);
            const svelteFile = join(acceptance, `${name}.svelte`);
            if (!existsSync(svelteFile)) continue;
            fixtures.push({
                id: `acceptance--${name}`, component: 'acceptance', label: `${name} page`, browserOnly: true, interactions: [],
                viewports: [{ width: 1280, height: 900 }, { width: 390, height: 844 }],
                blade: readFileSync(join(acceptance, file), 'utf8'), svelte: readFileSync(svelteFile, 'utf8'), imports: null,
            });
        }
    }
    return fixtures;
}

// Each run works in its own directory, so runs can overlap.
export const workDir = join(root, 'harness/svelte/.conformance', String(process.pid));

export function renderBlade(fixtures) {
    const work = workDir;
    mkdirSync(work, { recursive: true });
    const input = join(work, 'blade-fixtures.json');
    writeFileSync(input, JSON.stringify(Object.fromEntries(fixtures.map((f) => [f.id, f.blade]))));
    const out = execFileSync('php', [join(root, 'scripts/render-blade.php'), input], { maxBuffer: 256 * 1024 * 1024 });
    return JSON.parse(out.toString());
}

export async function renderSvelte(fixtures) {
    const work = workDir;
    mkdirSync(work, { recursive: true });
    const vite = await createSigtaVite();
    const { render } = await vite.ssrLoadModule('svelte/server');
    const out = {};
    try {
        for (const fixture of fixtures) {
            const file = join(work, `${fixture.id.replace(/[^a-zA-Z0-9-]/g, '_')}.svelte`);
            writeFileSync(file, fixture.imports === null ? fixture.svelte : `<script>\n${fixture.imports}\n</script>\n\n${fixture.svelte}\n`);
            try {
                const component = (await vite.ssrLoadModule(file)).default;
                out[fixture.id] = render(component).body;
            } catch (error) {
                out[fixture.id] = { error: error.message };
            }
        }
    } finally {
        await vite.close();
    }
    return out;
}

/* -------- normalization ----------------------------------------------------- */
const ignoredStates = new Set(manifests.flatMap((m) => Object.values(m.states ?? {}).filter((s) => s.conformance === false).map((s) => s.attribute)));
const BOOLEAN = new Set(['disabled', 'checked', 'selected', 'readonly', 'required', 'multiple', 'hidden', 'open', 'autofocus', 'novalidate', 'inert', 'defer', 'async', 'controls', 'loop', 'muted', 'playsinline', 'reversed', 'ismap', 'default', 'formnovalidate', 'nomodule', 'allowfullscreen']);
const ID_REFS = new Set(['id', 'for', 'aria-controls', 'aria-labelledby', 'aria-describedby', 'aria-activedescendant', 'aria-owns', 'list', 'form', 'popovertarget', 'anchor']);

// In the browser the live value of a control is a property; whether the
// attribute is still there after hydration says nothing about what shows.
const LIVE = new Set(['value', 'checked', 'selected']);

function ignoredAttribute(name) {
    if (browserMode && LIVE.has(name)) return true;
    return name.startsWith('x-') || name.startsWith('@') || name.startsWith(':') || name.startsWith('wire:') || ignoredStates.has(name);
}

function normalizeStyle(value) {
    return value.split(';').map((d) => d.trim().replace(/\s*:\s*/, ': ')).filter(Boolean).sort().join('; ');
}

export function normalize(html) {
    const dom = new JSDOM(`<!doctype html><body><template>${html}</template></body>`);
    const content = dom.window.document.querySelector('template').content;
    const ids = new Map();
    content.querySelectorAll('[id]').forEach((el) => {
        if (!ids.has(el.id)) ids.set(el.id, `#id${ids.size + 1}`);
    });
    const mapIds = (value) => value.split(/\s+/).map((token) => ids.get(token) ?? token).join(' ');
    const lines = [];
    const walk = (node, depth) => {
        for (const child of node.childNodes) {
            if (child.nodeType === 3) {
                // A <textarea>'s text is its default value; live, the value is a
                // property (Svelte's hydration drops the text node).
                if (browserMode && node.localName === 'textarea') continue;
                const text = child.nodeValue.replace(/\s+/g, ' ').trim();
                if (text) lines.push(`${'  '.repeat(depth)}"${text}"`);
            } else if (child.nodeType === 1 && child.localName === 'template') {
                // Alpine's x-for / x-if scaffolding; what it renders sits beside it.
                continue;
            } else if (child.nodeType === 1) {
                // Hidden is hidden, however a runtime says it: the hidden
                // attribute, x-cloak before Alpine starts, or x-show's inline display:none.
                const style = child.getAttribute('style') ?? '';
                const hidden = child.hasAttribute('hidden') || child.hasAttribute('x-cloak') || /display:\s*none/.test(style);
                const attrs = Array.from(child.attributes)
                    .filter((a) => !ignoredAttribute(a.name) && a.name !== 'hidden')
                    // An empty value is no value, an empty class no class.
                    .filter((a) => !(a.name === 'value' && a.value === '' && ['input', 'option'].includes(child.localName)))
                    .filter((a) => !(a.name === 'class' && a.value.trim() === ''))
                    // Svelte's server render records an <img>'s load/error for hydration to replay.
                    .filter((a) => !(['onload', 'onerror'].includes(a.name) && a.value === 'this.__e=event'))
                    .filter((a) => a.name !== 'style' || normalizeStyle(a.value.replace(/display:\s*none;?/g, '')) !== '')
                    .map((a) => {
                        let value = a.value.replace(/\s+/g, ' ').trim();
                        if (BOOLEAN.has(a.name)) value = '';
                        if (a.name === 'class') value = value.split(' ').filter(Boolean).sort().join(' ');
                        if (a.name === 'style') value = normalizeStyle(value.replace(/display:\s*none;?/g, ''));
                        if (ID_REFS.has(a.name)) value = mapIds(value);
                        // An SVG paint or clip reference: fill="url(#gradient)".
                        value = value.replace(/url\(#([^)\s]+)\)/g, (ref, id) => (ids.has(id) ? `url(${ids.get(id)})` : ref));
                        return `${a.name}="${value}"`;
                    })
                    .sort();
                if (hidden) attrs.unshift('(hidden)');
                lines.push(`${'  '.repeat(depth)}<${child.localName}${attrs.length ? ' ' + attrs.join(' ') : ''}>`);
                walk(child.localName === 'template' ? child.content : child, depth + 1);
            }
        }
    };
    walk(content, 0);
    return lines.join('\n');
}

function diff(a, b) {
    const left = a.split('\n');
    const right = b.split('\n');
    const out = [];
    const max = Math.max(left.length, right.length);
    for (let i = 0; i < max; i++) {
        if (left[i] !== right[i]) {
            out.push(`  line ${i + 1}:`, `    blade : ${left[i] ?? '∅'}`, `    svelte: ${right[i] ?? '∅'}`);
            if (out.length > 30) break;
        }
    }
    return out.join('\n');
}

/* -------- browser mode ------------------------------------------------------ */
// Chromium lays each text node out on a 1/64px grid, so the same words split
// into two nodes (Svelte renders slot text as its own node, Blade often glues
// it to the template's) can shift a glyph by a fraction of a pixel. Before a
// screenshot, both runtimes' text is put back into whole nodes.
async function settleText(page) {
    await page.evaluate(() => {
        const root = document.getElementById('fixture');
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_COMMENT);
        const comments = [];
        while (walker.nextNode()) comments.push(walker.currentNode);
        comments.forEach((node) => node.remove());
        document.body.normalize();
        return root;
    });
}

// Loads each fixture in both runtimes with their scripts running, then
// compares the live DOM and a screenshot: what a visitor actually sees.
async function inBrowser(fixtures, blade, svelte) {
    const { chromium } = await import('@playwright/test');
    const { PNG } = await import('pngjs');
    const pixelmatch = (await import('pixelmatch')).default;
    const { existsSync } = await import('node:fs');
    const harness = await startHarness(fixtures, blade, svelte);
    const executablePath = ['/opt/pw-browsers/chromium-1194/chrome-linux/chrome'].find(existsSync);
    const browser = await chromium.launch(executablePath ? { executablePath } : {});
    const context = await browser.newContext({ viewport: { width: 1100, height: 800 }, reducedMotion: 'reduce' });
    const results = {};
    try {
        for (const fixture of fixtures) {
            if (fixture.viewports) {
                results[fixture.id] = await wholePage(context, harness, fixture, PNG, pixelmatch);
                continue;
            }
            const shots = {};
            const doms = {};
            const errors = [];
            for (const runtime of ['blade', 'svelte']) {
                const page = await context.newPage();
                page.on('pageerror', (e) => errors.push(`${runtime}: ${e.message}`));
                page.on('console', (m) => { if (m.type() === 'error') errors.push(`${runtime}: ${m.text()}`); });
                await page.goto(`${harness.url}/${runtime}/${encodeURIComponent(fixture.id)}`, { waitUntil: 'networkidle' });
                if (runtime === 'svelte') await page.waitForFunction(() => window.__hydrated === true, null, { timeout: 10000 }).catch(() => errors.push('svelte: never hydrated'));
                await page.evaluate(() => document.fonts.ready);
                await page.waitForTimeout(250);
                if (fixture.interactions.length) {
                    for (const step of fixture.interactions) try {
                        if (step.focus) await page.focus(step.focus);
                        if (step.hover) await page.hover(step.hover);
                        if (step.click) await page.click(step.click);
                        if (step.fill) await page.fill(step.fill[0], step.fill[1]);
                        if (step.script) await page.evaluate(step.script);
                        for (const key of [step.press ?? []].flat()) await page.keyboard.press(key);
                        await page.waitForTimeout(step.wait ?? 350);
                    } catch (error) {
                        errors.push(`${runtime}: interaction ${JSON.stringify(step)} failed — ${error.message.split('\n')[0]}`);
                    }
                    // Open dialogs and floating panels leave the fixture's box.
                    doms[runtime] = await page.locator('#fixture').innerHTML();
                    await settleText(page);
                    shots[runtime] = PNG.sync.read(await page.screenshot());
                } else {
                    doms[runtime] = await page.locator('#fixture').innerHTML();
                    await settleText(page);
                    shots[runtime] = PNG.sync.read(await page.locator('#fixture').screenshot());
                }
                await page.close();
            }
            let pixels = 0;
            const [a, b] = [shots.blade, shots.svelte];
            if (a.width !== b.width || a.height !== b.height) pixels = -1;
            else pixels = pixelmatch(a.data, b.data, null, a.width, a.height, { threshold: 0.1 });
            results[fixture.id] = { dom: { blade: doms.blade, svelte: doms.svelte }, pixels, size: [`${a.width}x${a.height}`, `${b.width}x${b.height}`], errors, shots };
        }
    } finally {
        await browser.close();
        await harness.close();
    }
    return results;
}

// An acceptance page at each viewport: full-page screenshots, compared.
async function wholePage(context, harness, fixture, PNG, pixelmatch) {
    const errors = [];
    const doms = {};
    const shots = {};
    let pixels = 0;
    const sizes = [];
    for (const viewport of fixture.viewports) {
        const taken = {};
        for (const runtime of ['blade', 'svelte']) {
            const page = await context.newPage();
            await page.setViewportSize(viewport);
            page.on('pageerror', (e) => errors.push(`${runtime}@${viewport.width}: ${e.message}`));
            page.on('console', (m) => { if (m.type() === 'error') errors.push(`${runtime}@${viewport.width}: ${m.text()}`); });
            await page.goto(`${harness.url}/${runtime}/${encodeURIComponent(fixture.id)}`, { waitUntil: 'networkidle' });
            if (runtime === 'svelte') await page.waitForFunction(() => window.__hydrated === true, null, { timeout: 10000 }).catch(() => errors.push('svelte: never hydrated'));
            await page.evaluate(() => document.fonts.ready);
            await page.waitForTimeout(400);
            doms[runtime] ??= await page.locator('#fixture').innerHTML();
            await settleText(page);
            taken[runtime] = PNG.sync.read(await page.screenshot({ fullPage: true }));
            await page.close();
        }
        const [a, b] = [taken.blade, taken.svelte];
        sizes.push(`${viewport.width}: ${a.width}x${a.height} vs ${b.width}x${b.height}`);
        if (a.width !== b.width || a.height !== b.height) pixels = -1;
        else if (pixels !== -1) pixels += pixelmatch(a.data, b.data, null, a.width, a.height, { threshold: 0.1 });
        shots.blade ??= a;
        shots.svelte ??= b;
    }
    return { dom: doms, pixels, size: sizes, errors, shots };
}

/* -------- run --------------------------------------------------------------- */
if (process.argv[1] === fileURLToPath(import.meta.url)) {
    const fixtures = collectFixtures(only);
    const bladeServer = renderBlade(fixtures);
    const svelteServer = await renderSvelte(fixtures);
    let blade = bladeServer;
    let svelte = svelteServer;
    let browser = null;
    if (browserMode) {
        browser = await inBrowser(fixtures, bladeServer, svelteServer);
        blade = Object.fromEntries(Object.entries(browser).map(([id, r]) => [id, r.dom.blade]));
        svelte = Object.fromEntries(Object.entries(browser).map(([id, r]) => [id, r.dom.svelte]));
    }
    let failed = 0;
    if (writeAt) mkdirSync(writeAt, { recursive: true });
    for (const fixture of fixtures) {
        const b = blade[fixture.id];
        const s = svelte[fixture.id];
        if (writeAt) writeFileSync(join(writeAt, `${fixture.id}.json`), JSON.stringify({ ...fixture, bladeHtml: b, svelteHtml: s }, null, 1));
        if (typeof b !== 'string' || typeof s !== 'string') {
            failed++;
            console.log(`✗ ${fixture.id} (${fixture.label}) — render error\n  blade : ${b?.error ?? 'ok'}\n  svelte: ${s?.error ?? 'ok'}`);
            continue;
        }
        if (fixture.browserOnly && !browserMode) {
            if (verbose) console.log(`· ${fixture.id} (${fixture.label}) — compared in the browser only`);
            continue;
        }
        const nb = normalize(b);
        const ns = normalize(s);
        const visual = browser?.[fixture.id];
        if (visual && writeAt) {
            const { PNG } = await import('pngjs');
            writeFileSync(join(writeAt, `${fixture.id}.blade.png`), PNG.sync.write(visual.shots.blade));
            writeFileSync(join(writeAt, `${fixture.id}.svelte.png`), PNG.sync.write(visual.shots.svelte));
        }
        if (visual && (visual.pixels !== 0 || visual.errors.length)) {
            failed++;
            const why = visual.pixels === -1 ? `size ${visual.size.join(' vs ')}` : visual.pixels ? `${visual.pixels} px differ` : '';
            console.log(`✗ ${fixture.id} (${fixture.label}) — ${[why, ...visual.errors].filter(Boolean).join('; ')}`);
            if (nb !== ns) console.log(diff(nb, ns));
            continue;
        }
        if (nb === ns) {
            if (verbose) console.log(`✓ ${fixture.id} (${fixture.label})`);
        } else {
            failed++;
            console.log(`✗ ${fixture.id} (${fixture.label})\n${diff(nb, ns)}`);
            if (verbose) console.log(`--- blade source\n${fixture.blade}\n--- svelte source\n${fixture.svelte}`);
        }
    }
    rmSync(workDir, { recursive: true, force: true });
    console.log(`conformance: ${fixtures.length - failed}/${fixtures.length} examples agree`);
    process.exitCode = failed ? 1 : 0;
}
