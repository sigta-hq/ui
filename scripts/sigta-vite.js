// A Vite dev server that compiles the Svelte package straight from source
// (no build step), for server-side rendering in the conformance check, the
// harness and the docs build.
import { createServer } from 'vite';
import { svelte, vitePreprocess } from '@sveltejs/vite-plugin-svelte';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');

export const sourceAliases = [
    { find: /^@sigta-hq\/svelte\/inertia$/, replacement: join(root, 'packages/svelte/src/lib/inertia/index.ts') },
    { find: /^@sigta-hq\/svelte$/, replacement: join(root, 'packages/svelte/src/lib/index.ts') },
    { find: /^@sigta-hq\/core$/, replacement: join(root, 'packages/core/src/index.ts') },
    { find: /^@sigta-hq\/contract\/icons$/, replacement: join(root, 'packages/contract/src/icons.ts') },
    { find: /^@sigta-hq\/contract\/sections$/, replacement: join(root, 'packages/contract/src/sections.ts') },
];

export function sveltePlugin() {
    return svelte({ configFile: false, preprocess: vitePreprocess({ script: true }) });
}

export async function createSigtaVite(options = {}) {
    return createServer({
        configFile: false,
        root: join(root, 'harness/svelte'),
        logLevel: 'error',
        plugins: [sveltePlugin()],
        resolve: { alias: sourceAliases },
        server: { middlewareMode: true, hmr: false, ws: false, watch: null },
        appType: 'custom',
        optimizeDeps: { noDiscovery: true, include: [] },
        ...options,
    });
}
