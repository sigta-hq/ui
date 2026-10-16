// Conformance: every manifest example is rendered by both runtimes — Blade
// through the package booted in Testbench, Svelte through server-side
// rendering — and the two outputs must agree once normalized: the same
// elements, contract attributes, ARIA and text.
//
//   node scripts/conformance.js [component…] [--verbose] [--write <dir>]
//
// --write keeps each fixture's two renders (for the visual comparison).
import { execFileSync } from 'node:child_process';
import { mkdirSync, rmSync, writeFileSync } from 'node:fs';
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
            if (!example.tree || example.conformance === false) return;
            fixtures.push({
                id: `${manifest.name}--${index}`,
                component: manifest.name,
                label: example.label,
                blade: toBlade(example.tree, registry, { prefix: 'x-sigta::' }),
                svelte: toSvelte(example.tree, registry),
                imports: svelteImports(example.tree, registry),
            });
        });
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
            writeFileSync(file, `<script>\n${fixture.imports}\n</script>\n\n${fixture.svelte}\n`);
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

function ignoredAttribute(name) {
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
                const text = child.nodeValue.replace(/\s+/g, ' ').trim();
                if (text) lines.push(`${'  '.repeat(depth)}"${text}"`);
            } else if (child.nodeType === 1) {
                const attrs = Array.from(child.attributes)
                    .filter((a) => !ignoredAttribute(a.name))
                    .map((a) => {
                        let value = a.value.replace(/\s+/g, ' ').trim();
                        if (BOOLEAN.has(a.name)) value = '';
                        if (a.name === 'class') value = value.split(' ').filter(Boolean).sort().join(' ');
                        if (a.name === 'style') value = normalizeStyle(value);
                        if (ID_REFS.has(a.name)) value = mapIds(value);
                        return `${a.name}="${value}"`;
                    })
                    .sort();
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
                doms[runtime] = await page.locator('#fixture').innerHTML();
                shots[runtime] = PNG.sync.read(await page.locator('#fixture').screenshot());
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
        const nb = normalize(b);
        const ns = normalize(s);
        const visual = browser?.[fixture.id];
        if (visual && (visual.pixels !== 0 || visual.errors.length)) {
            failed++;
            const why = visual.pixels === -1 ? `size ${visual.size.join(' vs ')}` : visual.pixels ? `${visual.pixels} px differ` : '';
            console.log(`✗ ${fixture.id} (${fixture.label}) — ${[why, ...visual.errors].filter(Boolean).join('; ')}`);
            if (writeAt) {
                const { PNG } = await import('pngjs');
                writeFileSync(join(writeAt, `${fixture.id}.blade.png`), PNG.sync.write(visual.shots.blade));
                writeFileSync(join(writeAt, `${fixture.id}.svelte.png`), PNG.sync.write(visual.shots.svelte));
            }
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
