// Builds the docs site (docs/dist): one static page per kit page and guide,
// in two editions — /livewire/… and /svelte/… — with a switch between them.
//
// Every demo is the real thing in its runtime: Livewire pages carry the Blade
// render with Alpine and the Livewire runtime; Svelte pages carry the SSR
// render, hydrated. Only one runtime loads per page. The chrome is rendered
// on the server from Sigta's own Svelte components.
//
//   node docs/build.js            (needs PHP for the Blade renders)
import { build } from 'vite';
import { execFileSync } from 'node:child_process';
import { cpSync, existsSync, mkdirSync, readdirSync, readFileSync, rmSync, writeFileSync } from 'node:fs';
import { dirname, join, relative } from 'node:path';
import { fileURLToPath } from 'node:url';
import { marked } from 'marked';
import { createSigtaVite, sourceAliases, sveltePlugin } from '../scripts/sigta-vite.js';
import { manifests, registry } from '../packages/contract/src/manifests/index.ts';
import { toBlade, toSvelte, svelteImports } from '../packages/contract/src/examples.ts';
import { edition, expandTags } from '../packages/contract/scripts/stubs.ts';

const docs = dirname(fileURLToPath(import.meta.url));
const root = join(docs, '..');
const work = join(docs, '.build');
const dist = join(docs, 'dist');
const RUNTIMES = ['livewire', 'svelte'];

rmSync(work, { recursive: true, force: true });
rmSync(dist, { recursive: true, force: true });
mkdirSync(join(work, 'examples'), { recursive: true });
mkdirSync(join(dist, 'assets'), { recursive: true });

/* -------- pages ------------------------------------------------------------ */
const catalog = JSON.parse(execFileSync('php', ['-r', `echo json_encode(require '${join(root, 'resources/catalog.php')}');`]).toString());
const titles = { 'tile-grid': 'Tile grid', 'section-header': 'Section header', 'save-state': 'Save state', 'floating-bar': 'Floating bar' };
const titleOf = (slug) => titles[slug] ?? slug.replace(/(^|-)(.)/g, (_, dash, ch) => (dash ? ' ' : '') + ch.toUpperCase());
const byPage = new Map();
for (const manifest of manifests) {
    if (!byPage.has(manifest.page)) byPage.set(manifest.page, []);
    byPage.get(manifest.page).push(manifest);
}
const groups = Object.entries(catalog)
    .filter(([key]) => key !== 'docs')
    .map(([, group]) => ({ label: group.label, pages: group.items.filter((slug) => byPage.has(slug)) }))
    .filter((group) => group.pages.length);
// A page the catalog does not list joins the group of its tier.
for (const slug of byPage.keys()) {
    if (groups.some((g) => g.pages.includes(slug))) continue;
    const tier = byPage.get(slug)[0].tier;
    const label = catalog[tier]?.label ?? tier;
    let group = groups.find((g) => g.label === label);
    if (!group) groups.push(group = { label, pages: [] });
    group.pages.push(slug);
}
const pageOrder = groups.flatMap((g) => g.pages);

const guides = readdirSync(join(docs, 'guides')).filter((f) => f.endsWith('.md')).map((file) => {
    const text = readFileSync(join(docs, 'guides', file), 'utf8');
    const [, front, body] = text.match(/^---\n([\s\S]*?)\n---\n([\s\S]*)$/);
    const meta = Object.fromEntries(front.split('\n').map((line) => line.split(/:\s*/).map((s) => s.trim())));
    return { slug: file.replace(/\.md$/, ''), title: meta.title, order: Number(meta.order ?? 99), runtime: meta.runtime ?? null, body };
}).sort((a, b) => a.order - b.order);

/* -------- examples ---------------------------------------------------------- */
const examples = [];
for (const slug of pageOrder) {
    for (const manifest of byPage.get(slug)) {
        manifest.examples.forEach((example, index) => {
            const id = `${manifest.name}--${index}`.replace(/[^a-zA-Z0-9-]/g, '_');
            if (example.tree) {
                const imports = svelteImports(example.tree, registry);
                examples.push({
                    id, page: slug, component: manifest.name, label: example.label, frame: example.frame,
                    bladeRender: toBlade(example.tree, registry, { prefix: 'x-sigta::' }),
                    bladeSource: toBlade(example.tree, registry, { prefix: 'sigta:' }),
                    svelteFile: `<script>\n${imports}\n</script>\n\n${toSvelte(example.tree, registry)}\n`,
                    svelteSource: `${imports ? `<script>\n    ${imports}\n</script>\n\n` : ''}${toSvelte(example.tree, registry)}`,
                });
            } else if (example.blade && example.svelte) {
                examples.push({
                    id, page: slug, component: manifest.name, label: example.label, frame: example.frame,
                    bladeRender: example.blade,
                    bladeSource: example.blade.replace(/<(\/?)x-sigta::/g, '<$1sigta:'),
                    svelteFile: example.svelte,
                    svelteSource: example.svelte,
                });
            }
        });
    }
}

// Blade renders, all at once through the package booted in Testbench.
writeFileSync(join(work, 'blade.json'), JSON.stringify(Object.fromEntries(examples.map((e) => [e.id, e.bladeRender]))));
const bladeHtml = JSON.parse(execFileSync('php', [join(root, 'scripts/render-blade.php'), join(work, 'blade.json')], { maxBuffer: 256 * 1024 * 1024 }).toString());

// Svelte renders: each example is a component, rendered on the server here
// and hydrated in the page by a per-page bundle.
for (const example of examples) writeFileSync(join(work, 'examples', `${example.id}.svelte`), example.svelteFile);
const ssr = await createSigtaVite({ root: work });
const { render } = await ssr.ssrLoadModule('svelte/server');
const svelteHtml = {};
for (const [index, example] of examples.entries()) {
    try {
        // Every demo is its own root, so $props.id() would start at s1 in
        // each one: a per-demo prefix keeps ids unique on the page (hydrate
        // reads them back from the server render).
        svelteHtml[example.id] = render((await ssr.ssrLoadModule(join(work, 'examples', `${example.id}.svelte`))).default, { idPrefix: `d${index}` }).body;
    } catch (error) {
        svelteHtml[example.id] = `<p>Svelte render failed: ${error.message}</p>`;
    }
}

/* -------- pages: chrome rendered by Sigta's Svelte components ---------------- */
const Layout = (await ssr.ssrLoadModule(join(docs, 'src/Layout.svelte'))).default;
const ComponentPage = (await ssr.ssrLoadModule(join(docs, 'src/ComponentPage.svelte'))).default;
const GuidePage = (await ssr.ssrLoadModule(join(docs, 'src/GuidePage.svelte'))).default;
const { createRawSnippet } = await ssr.ssrLoadModule('svelte');

const kebab = (name) => name.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase();
const show = (value) => (value === undefined || value === null ? '—' : typeof value === 'string' ? value : JSON.stringify(value));

function apiOf(manifest, runtime) {
    const props = Object.entries(manifest.props).flatMap(([name, spec]) => {
        const override = runtime === 'livewire' ? spec.blade : spec.svelte;
        if (override === false) return [];
        const shown = runtime === 'livewire' ? (override?.name ?? kebab(name)) : (override?.name ?? name);
        const type = spec.type === 'enum' ? spec.values.join(' | ') : spec.type;
        return [{
            name: spec.bindable && runtime === 'svelte' ? `bind:${shown}` : shown,
            type,
            default: spec.required ? 'required' : show(spec.default),
            description: override?.note ? `${spec.description} ${override.note}` : spec.description,
        }];
    });
    const slots = Object.entries(manifest.slots ?? {})
        .filter(([, spec]) => spec[runtime === 'livewire' ? 'blade' : 'svelte'] !== false)
        .map(([name, spec]) => ({ name: runtime === 'svelte' && name === 'default' ? 'children' : name, description: spec.description }));
    const events = Object.values(manifest.events ?? {}).map((spec) => ({ name: runtime === 'livewire' ? spec.blade : spec.svelte, description: spec.description })).filter((e) => e.name);
    const notes = [...(manifest.notes?.shared ?? []), ...(manifest.notes?.[runtime] ?? [])].map((note) => expandTags(note, runtime, registry));
    return { props, slots, events, notes };
}

const assets = (base) => ({ css: `${base}assets/docs.css` });

function page({ runtime, path, title, body, scripts }) {
    const depth = path.split('/').filter(Boolean).length;
    const base = '../'.repeat(depth);
    const otherPath = `${runtime === 'livewire' ? 'svelte' : 'livewire'}${path.slice(runtime.length)}`;
    const nav = [
        { label: 'Guides', items: guides.filter((g) => !g.runtime || g.runtime === runtime).map((g) => ({ href: `${base}${runtime}/${g.slug === 'index' ? '' : `guides/${g.slug}/`}`, title: g.title, current: path === `${runtime}/${g.slug === 'index' ? '' : `guides/${g.slug}/`}` })) },
        ...groups.map((group) => ({ label: group.label, items: group.pages.map((slug) => ({ href: `${base}${runtime}/${slug}/`, title: titleOf(slug), current: path === `${runtime}/${slug}/` })) })),
    ];
    const children = createRawSnippet(() => ({ render: () => `<div>${body}</div>` }));
    const chrome = render(Layout, { props: { title, runtime, base, other: `${base}${otherPath}`, nav, children } }).body;
    return `<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>${title} — Sigta UI (${runtime === 'livewire' ? 'Livewire' : 'Svelte'})</title>
<link rel="icon" href="data:,">
<link rel="stylesheet" href="${assets(base).css}">
<script>try { localStorage.setItem('sigta-docs-runtime', ${JSON.stringify(runtime)}); } catch (e) {}</script>
${scripts(base)}
</head><body>${chrome}</body></html>
`;
}

function write(path, html) {
    const file = join(dist, path, 'index.html');
    mkdirSync(dirname(file), { recursive: true });
    writeFileSync(file, html);
}

// The Svelte bundles: one per kit page, hydrating that page's examples.
const entries = {};
mkdirSync(join(work, 'entries'), { recursive: true });
for (const slug of pageOrder) {
    const own = examples.filter((e) => e.page === slug);
    const file = join(work, 'entries', `${slug}.js`);
    writeFileSync(file, [
        `import { hydrate } from 'svelte';`,
        `import { ensureRuntime } from '@sigta-hq/svelte';`,
        ...own.map((e, i) => `import E${i} from '../examples/${e.id}.svelte';`),
        `const examples = { ${own.map((e, i) => `${JSON.stringify(e.id)}: E${i}`).join(', ')} };`,
        `ensureRuntime();`,
        `document.querySelectorAll('[data-example]').forEach((el) => { const C = examples[el.dataset.example]; if (C) hydrate(C, { target: el }); });`,
        '',
    ].join('\n'));
    entries[slug] = file;
}
await ssr.close();
await build({
    configFile: false,
    root: work,
    logLevel: 'warn',
    plugins: [sveltePlugin()],
    resolve: { alias: sourceAliases },
    build: { outDir: join(dist, 'assets/svelte'), emptyOutDir: true, manifest: true, rollupOptions: { input: entries } },
});
const viteManifest = JSON.parse(readFileSync(join(dist, 'assets/svelte/.vite/manifest.json'), 'utf8'));
const bundleOf = (slug) => viteManifest[relative(work, entries[slug])]?.file;

// The Livewire runtime: Alpine (with its focus plugin) and dist/sigta.js.
cpSync(join(root, 'dist/sigta.js'), join(dist, 'assets/sigta.js'));
writeFileSync(join(dist, 'assets/alpine.js'),
    readFileSync(join(root, 'node_modules/@alpinejs/focus/dist/cdn.min.js'), 'utf8') + '\n' + readFileSync(join(root, 'node_modules/alpinejs/dist/cdn.min.js'), 'utf8'));
// The editor's runtime is its own bundle (@sigtaEditorScripts), loaded only on the page that shows it.
cpSync(join(root, 'dist/sigta-editor.js'), join(dist, 'assets/sigta-editor.js'));
const livewireScripts = (base, slug) => `<script src="${base}assets/sigta.js"></script>\n${slug === 'editor' ? `<script src="${base}assets/sigta-editor.js"></script>\n` : ''}<script defer src="${base}assets/alpine.js"></script>`;

for (const runtime of RUNTIMES) {
    for (const slug of pageOrder) {
        const components = byPage.get(slug).map((manifest) => ({
            name: runtime === 'livewire' ? `<sigta:${manifest.name}>` : `<${manifest.svelte}>`,
            description: manifest.description,
            ...apiOf(manifest, runtime),
            examples: examples.filter((e) => e.page === slug && e.component === manifest.name).map((e) => ({
                id: e.id, label: e.label, frame: e.frame,
                html: runtime === 'livewire' ? (typeof bladeHtml[e.id] === 'string' ? bladeHtml[e.id] : `<p>Blade render failed: ${bladeHtml[e.id]?.error}</p>`) : svelteHtml[e.id],
                source: runtime === 'livewire' ? e.bladeSource : e.svelteSource,
                lang: runtime === 'livewire' ? 'blade' : 'svelte',
            })),
        }));
        const body = render(ComponentPage, { props: { components } }).body;
        write(`${runtime}/${slug}/`, page({
            runtime, path: `${runtime}/${slug}/`, title: titleOf(slug), body,
            scripts: (base) => (runtime === 'livewire' ? livewireScripts(base, slug) : `<script type="module" src="${base}assets/svelte/${bundleOf(slug)}"></script>`),
        }));
    }
    for (const guide of guides.filter((g) => !g.runtime || g.runtime === runtime)) {
        const path = guide.slug === 'index' ? `${runtime}/` : `${runtime}/guides/${guide.slug}/`;
        // The guide's own first heading is the page title.
        const text = edition(guide.body, runtime, registry).trimStart();
        const heading = text.match(/^# (.*)\n/);
        const html = marked.parse(heading ? text.slice(heading[0].length) : text);
        const body = render(GuidePage, { props: { html } }).body;
        write(path, page({ runtime, path, title: heading?.[1] ?? guide.title, body, scripts: () => '' }));
    }
}

// The root: back to the runtime the visitor read last, Livewire otherwise.
writeFileSync(join(dist, 'index.html'), `<!doctype html><meta charset="utf-8"><title>Sigta UI</title>
<script>var r = 'livewire'; try { r = localStorage.getItem('sigta-docs-runtime') || r; } catch (e) {} location.replace(r + '/');</script>
<a href="livewire/">Livewire</a> · <a href="svelte/">Svelte</a>
`);

/* -------- stylesheet --------------------------------------------------------- */
writeFileSync(join(work, 'docs.css'), [
    '@import "tailwindcss";',
    `@import "${join(root, 'packages/contract/css/tailwind.css')}";`,
    `@import "${join(root, 'packages/contract/dist/css/sigta.css')}";`,
    `@source "${dist}";`,
    `@source "${join(docs, 'src')}";`,
    '',
].join('\n'));
execFileSync(process.execPath, [join(root, 'node_modules/@tailwindcss/cli/dist/index.mjs'), '-i', join(work, 'docs.css'), '-o', join(work, 'docs.out.css'), '--minify'], { stdio: ['ignore', 'ignore', 'inherit'] });
writeFileSync(join(dist, 'assets/docs.css'), readFileSync(join(work, 'docs.out.css'), 'utf8').replaceAll('../../fonts/', './fonts/'));
cpSync(join(root, 'packages/contract/fonts'), join(dist, 'assets/fonts'), { recursive: true });

console.log(`docs: ${pageOrder.length} kit pages and ${guides.length} guides × ${RUNTIMES.length} runtimes → docs/dist`);
