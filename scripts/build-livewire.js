// Bundles the Livewire runtime (resources/js/sigta.js + @sigta-hq/core) into
// dist/sigta.js, the IIFE @sigtaScripts serves. The banner carries a hash of
// every source file, so a test can tell a stale build from a fresh one.
import { build } from 'esbuild';
import { createHash } from 'node:crypto';
import { readFileSync, readdirSync } from 'node:fs';
import { join, dirname, relative } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');

export function runtimeSources() {
    const core = readdirSync(join(root, 'packages/core/src')).filter((f) => f.endsWith('.ts')).sort()
        .map((f) => join(root, 'packages/core/src', f));
    return [join(root, 'resources/js/sigta.js'), ...core];
}

export function runtimeHash() {
    const hash = createHash('sha1');
    for (const file of runtimeSources()) {
        hash.update(relative(root, file).replaceAll('\\', '/'));
        hash.update('\0');
        hash.update(readFileSync(file, 'utf8').replace(/\r\n/g, '\n'));
        hash.update('\0');
    }
    return hash.digest('hex');
}

await build({
    entryPoints: [join(root, 'resources/js/sigta.js')],
    outfile: join(root, 'dist/sigta.js'),
    bundle: true,
    format: 'iife',
    target: 'es2020',
    legalComments: 'none',
    banner: { js: `/* Sigta UI — Livewire runtime. Built by scripts/build-livewire.js from resources/js/sigta.js and packages/core/src; do not edit. sources:${runtimeHash()} */` },
    logLevel: 'warning',
});
console.log('livewire: dist/sigta.js');
