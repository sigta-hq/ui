// The Svelte runtime as a consumer gets it: packs @sigta-hq/contract, core and
// svelte (build them first), installs the tarballs into a copy of
// harness/consumer in a temp directory — outside the workspace, so nothing
// resolves to the monorepo's sources — then builds it, renders it on the
// server, drives it in Chromium and runs the `sigta install` CLI. Catches what
// the in-repo checks cannot: a file missing from a package, a broken export
// map, an import that only resolves inside the workspace.
//   node scripts/consumer-smoke.js [--keep]
// Needs network access to the npm registry for svelte, vite and tailwind.
// Chromium wants a short temp path: run it with the default TMPDIR.
import { execFileSync } from 'node:child_process';
import { cpSync, existsSync, mkdirSync, mkdtempSync, readdirSync, readFileSync, rmSync } from 'node:fs';
import { createServer } from 'node:http';
import { tmpdir } from 'node:os';
import { dirname, extname, join } from 'node:path';
import { fileURLToPath, pathToFileURL } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const keep = process.argv.includes('--keep');
const work = mkdtempSync(join(tmpdir(), 'sigta-consumer-'));
const app = join(work, 'app');
const run = (cmd, args, cwd) => execFileSync(cmd, args, { cwd, stdio: ['ignore', 'pipe', 'pipe'] }).toString();

// The versions a new app would pick today; the package's peer range is the contract.
const toolchain = ['svelte@^5.29', 'vite@^7', '@sveltejs/vite-plugin-svelte@^6', 'tailwindcss@^4', '@tailwindcss/vite@^4'];

const checks = [];
const problems = [];
let server = null;
const check = (name, ok, detail = '') => checks.push(`${ok ? 'ok  ' : 'FAIL'} ${name}${detail ? ` — ${detail}` : ''}`);

try {
    cpSync(join(root, 'harness/consumer'), app, { recursive: true });
    const tarballs = join(work, 'tarballs');
    mkdirSync(tarballs);
    for (const pkg of ['contract', 'core', 'svelte']) {
        run('npm', ['pack', '--pack-destination', tarballs], join(root, 'packages', pkg));
    }
    const packed = readdirSync(tarballs).map((file) => join(tarballs, file));
    run('npm', ['install', '--no-audit', '--no-fund', ...packed, ...toolchain], app);
    run('npx', ['vite', 'build'], app);
    run('npx', ['vite', 'build', '--ssr', 'src/ssr.js', '--outDir', 'dist-ssr'], app);

    const { html } = await import(pathToFileURL(join(app, 'dist-ssr/ssr.js')).href);
    const body = html();
    check('server render', body.includes('data-sigta="button"') && body.includes('data-sigta="section.faq"'));
    const page = readFileSync(join(app, 'dist/index.html'), 'utf8').replace('<!--ssr-->', body);
    const types = { '.js': 'text/javascript', '.css': 'text/css', '.woff2': 'font/woff2' };
    server = createServer((req, res) => {
        const path = decodeURIComponent(new URL(req.url, 'http://x').pathname);
        if (path === '/') { res.setHeader('content-type', 'text/html'); return res.end(page); }
        const file = join(app, 'dist', path);
        if (!existsSync(file)) { res.statusCode = 404; return res.end(); }
        res.setHeader('content-type', types[extname(file)] ?? 'application/octet-stream');
        res.end(readFileSync(file));
    }).listen(0);

    const { chromium } = await import('@playwright/test');
    const executablePath = ['/opt/pw-browsers/chromium-1194/chrome-linux/chrome'].find(existsSync);
    const browser = await chromium.launch(executablePath ? { executablePath } : {});
    try {
        const tab = await browser.newPage({ viewport: { width: 1100, height: 900 } });
        tab.on('pageerror', (e) => problems.push(`pageerror: ${e.message}`));
        tab.on('console', (m) => { if (['error', 'warning'].includes(m.type())) problems.push(`console.${m.type()}: ${m.text()}`); });
        tab.on('response', (r) => { if (r.status() >= 400) problems.push(`HTTP ${r.status()} ${r.url()}`); });

        await tab.goto(`http://127.0.0.1:${server.address().port}/`, { waitUntil: 'networkidle' });
        await tab.waitForFunction(() => window.__ready === true);
        await tab.evaluate(() => document.fonts.ready);

        const style = await tab.evaluate(() => {
            const cs = getComputedStyle(document.querySelector('[data-sigta="button"][data-variant="primary"]'));
            return { background: cs.backgroundColor, font: cs.fontFamily };
        });
        check('the contract stylesheet styles a button', style.background !== 'rgba(0, 0, 0, 0)' && /JetBrains/i.test(style.font), JSON.stringify(style));
        check('icons render from the packaged set', await tab.evaluate(() => document.querySelectorAll('[data-sigta="icon"] :is(path, line, polyline, circle)').length > 0));
        const fonts = await tab.evaluate(() => [...document.fonts].filter((f) => f.status === 'loaded').map((f) => f.family));
        check('brand fonts load from the package', fonts.length > 0, [...new Set(fonts)].join(', '));

        await tab.click('[data-sigta="button"][data-variant="primary"]');
        await tab.waitForTimeout(400);
        check('sigta.toast raises a toast', await tab.locator('[data-sigta="toast"]').filter({ hasText: 'Saved' }).count() > 0);

        await tab.click('text=Open modal');
        await tab.waitForTimeout(400);
        check('a modal opens by name', await tab.evaluate(() => document.querySelector('dialog[open]')?.textContent.includes('Modal body') ?? false));
        await tab.keyboard.press('Escape');
        await tab.waitForTimeout(300);
        check('Escape closes it', await tab.evaluate(() => !document.querySelector('dialog[open]')));

        await tab.fill('input[placeholder="Your name"]', 'Ada');
        await tab.click('input[placeholder="Search…"]');
        await tab.fill('input[placeholder="Search…"]', 'gr');
        await tab.keyboard.press('ArrowDown');
        await tab.keyboard.press('Enter');
        await tab.click('text=I agree');
        await tab.waitForTimeout(200);
        const state = await tab.textContent('[data-testid="state"]');
        check('bind:value and bind:checked round-trip', state === 'Ada|grace|true', state);

        check('setMessages translates Sigta\'s own strings', await tab.locator('[aria-label="Valuta"]').count() === 1);

        await tab.click('[data-sigta="section.faq"] summary >> nth=0');
        check('a section renders its default content and opens', await tab.evaluate(() => !!document.querySelector('[data-sigta="section.faq"] details[open]')));
    } finally {
        await browser.close();
    }

    const first = run('npx', ['sigta', 'install'], app);
    const again = run('npx', ['sigta', 'install'], app);
    check('sigta install writes the agent context', first.includes('wrote sigta-ui.md') && existsSync(join(app, 'sigta-components.md')) && readFileSync(join(app, 'AGENTS.md'), 'utf8').includes('sigta-ui-context'));
    check('sigta install is idempotent', again.includes('kept  sigta-ui.md') && again.includes('kept  AGENTS.md'));
} catch (error) {
    problems.push(`${error.message}${error.stderr ? `\n${error.stderr}` : ''}`);
} finally {
    server?.close();
    if (!keep) rmSync(work, { recursive: true, force: true });
}

console.log(checks.join('\n'));
console.log(problems.length ? `problems:\n  ${problems.join('\n  ')}` : 'no errors, warnings or failed requests');
if (keep) console.log(`kept: ${work}`);
process.exitCode = !checks.length || checks.some((c) => c.startsWith('FAIL')) || problems.length ? 1 : 0;
