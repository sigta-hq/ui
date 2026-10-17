#!/usr/bin/env node
/* ---------------------------------------------------------------------------
   sigta install — the Svelte twin of `php artisan sigta:install`.

   Idempotent: re-running brings the project in sync with the installed
   package. It writes the LLM context files (sigta-ui.md, sigta-components.md
   — package-owned, always overwritten) to the project root and makes sure
   AGENTS.md carries one line pointing at them. Fonts need no step: they are
   bundled from the package stylesheet by your bundler.

   Keep it in sync on every install by adding to YOUR package.json:
     "scripts": { "postinstall": "sigta install" }
--------------------------------------------------------------------------- */
import { readFileSync, writeFileSync, existsSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const MARKER = 'sigta-ui-context';
const LINE = '- [Sigta UI](./sigta-ui.md) — this project uses the Sigta UI component library for Svelte. Read `sigta-ui.md` (rules) and `sigta-components.md` (API reference) before writing components. Always prefer Sigta components (`@sigta-hq/svelte`) over raw HTML. <!-- sigta-ui-context -->';

const pkg = join(dirname(fileURLToPath(import.meta.url)), '..');
const [command = 'install', ...flags] = process.argv.slice(2);
const project = flags.find((f) => f.startsWith('--cwd='))?.slice(6) ?? process.env.INIT_CWD ?? process.cwd();

function install() {
    for (const file of ['sigta-ui.md', 'sigta-components.md']) {
        const content = readFileSync(join(pkg, 'stubs', file), 'utf8');
        const target = join(project, file);
        const changed = !existsSync(target) || readFileSync(target, 'utf8') !== content;
        if (changed) writeFileSync(target, content);
        console.log(`  ${changed ? 'wrote' : 'kept '} ${file}`);
    }
    const agents = join(project, 'AGENTS.md');
    const current = existsSync(agents) ? readFileSync(agents, 'utf8') : '';
    if (current.includes(MARKER)) {
        console.log('  kept  AGENTS.md (already points at Sigta UI)');
    } else {
        writeFileSync(agents, current ? `${current.replace(/\s*$/, '')}\n\n${LINE}\n` : `# Agents\n\n${LINE}\n`);
        console.log(`  ${current ? 'added a line to' : 'wrote'} AGENTS.md`);
    }
}

if (command === 'install') {
    console.log('Sigta UI — install & sync');
    install();
    console.log('Done. Re-run any time; add "postinstall": "sigta install" to keep it in sync.');
} else {
    console.error(`Unknown command "${command}". Usage: sigta install [--cwd=<project>]`);
    process.exitCode = 1;
}
