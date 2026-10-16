// The two-runtime harness: serves each fixture as a real page in both
// runtimes, so a browser can compare what a visitor actually sees.
//
//   /blade/<id>   the Blade render, with Alpine and the Livewire runtime (dist/sigta.js)
//   /svelte/<id>  the Svelte SSR render, hydrated by the Svelte runtime
//
// Both pages share one stylesheet: the contract's drop-in CSS plus the
// Tailwind utilities the examples' own wrappers use.
import { createServer as createHttpServer } from 'node:http';
import { readFileSync, writeFileSync, mkdirSync, rmSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import { execFileSync } from 'node:child_process';
import { createSigtaVite } from './sigta-vite.js';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const work = join(root, 'harness/svelte/.conformance', `harness-${process.pid}`);

function buildCss() {
    mkdirSync(work, { recursive: true });
    const entry = join(work, 'harness.css');
    writeFileSync(entry, [
        '@import "tailwindcss";',
        `@import "${join(root, 'packages/contract/css/tailwind.css')}";`,
        `@import "${join(root, 'packages/contract/dist/css/sigta.css')}";`,
        `@source "${work}";`,
        'body { padding: 24px; }',
        '',
    ].join('\n'));
    const out = join(work, 'harness.out.css');
    execFileSync(process.execPath, [join(root, 'node_modules/@tailwindcss/cli/dist/index.mjs'), '-i', entry, '-o', out], { stdio: ['ignore', 'ignore', 'inherit'], cwd: root });
    // The fonts resolve relative to the contract's dist/css.
    return readFileSync(out, 'utf8').replaceAll('url("../../fonts/', 'url("/fonts/').replaceAll("url('../../fonts/", "url('/fonts/");
}

const page = (head, body) => `<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="data:,"><link rel="stylesheet" href="/harness.css">${head}</head>
<body>${body}</body></html>`;

/**
 * Starts the harness for the given fixtures ({ id, svelte, imports } plus
 * the Blade renders by id). Returns { url, close }.
 */
export async function startHarness(fixtures, bladeHtml, svelteHtml) {
    // Fixture files must exist before the stylesheet scans them for utilities.
    mkdirSync(work, { recursive: true });
    for (const fixture of fixtures) {
        writeFileSync(join(work, `${fileId(fixture.id)}.svelte`), `<script>\n${fixture.imports}\n</script>\n\n${fixture.svelte}\n`);
        writeFileSync(join(work, `${fileId(fixture.id)}.blade.html`), typeof bladeHtml[fixture.id] === 'string' ? bladeHtml[fixture.id] : '');
    }
    const css = buildCss();
    const vite = await createSigtaVite();
    const alpine = readFileSync(join(root, 'node_modules/alpinejs/dist/cdn.min.js'), 'utf8');
    const focus = readFileSync(join(root, 'node_modules/@alpinejs/focus/dist/cdn.min.js'), 'utf8');
    const byId = Object.fromEntries(fixtures.map((f) => [f.id, f]));

    const server = createHttpServer(async (req, res) => {
        const url = new URL(req.url, 'http://localhost');
        const send = (body, type = 'text/html') => { res.writeHead(200, { 'content-type': `${type}; charset=utf-8` }); res.end(body); };
        try {
            if (url.pathname === '/harness.css') return send(css, 'text/css');
            if (url.pathname.startsWith('/fonts/')) {
                res.writeHead(200, { 'content-type': 'font/woff2' });
                return res.end(readFileSync(join(root, 'packages/contract/fonts', url.pathname.slice(7))));
            }
            if (url.pathname === '/sigta.js') return send(readFileSync(join(root, 'dist/sigta.js'), 'utf8'), 'text/javascript');
            if (url.pathname === '/alpine.js') return send(focus + '\n' + alpine, 'text/javascript');
            const [, runtime, id] = url.pathname.split('/');
            const fixture = byId[decodeURIComponent(id ?? '')];
            if (runtime === 'blade' && fixture) {
                // The runtime registers on alpine:init, so it loads before Alpine starts.
                return send(page('<script src="/sigta.js"></script><script defer src="/alpine.js"></script>', `<main id="fixture">${bladeHtml[fixture.id]}</main>`));
            }
            if (runtime === 'svelte' && fixture) {
                const module = `/.conformance/harness-${process.pid}/${fileId(fixture.id)}.svelte`;
                const html = page('', `<main id="fixture">${svelteHtml[fixture.id]}</main>
<script type="module">import Fixture from '${module}'; import { hydrate } from 'svelte'; hydrate(Fixture, { target: document.getElementById('fixture') }); window.__hydrated = true;</script>`);
                return send(await vite.transformIndexHtml(url.pathname, html));
            }
            vite.middlewares(req, res, () => { res.writeHead(404); res.end('not found'); });
        } catch (error) {
            res.writeHead(500);
            res.end(String(error?.stack ?? error));
        }
    });
    await new Promise((resolve) => server.listen(0, '127.0.0.1', resolve));
    const { port } = server.address();
    return {
        url: `http://127.0.0.1:${port}`,
        close: async () => { server.close(); await vite.close(); rmSync(work, { recursive: true, force: true }); },
    };
}

export const fileId = (id) => id.replace(/[^a-zA-Z0-9-]/g, '_');
