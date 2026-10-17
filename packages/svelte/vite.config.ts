import { defineConfig } from 'vitest/config';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import { fileURLToPath } from 'node:url';

const source = (path: string) => fileURLToPath(new URL(path, import.meta.url));

export default defineConfig({
    plugins: [svelte()],
    // Tests run against the sibling packages' sources, no build needed.
    resolve: {
        conditions: ['browser'],
        alias: [
            { find: /^@sigta-hq\/core$/, replacement: source('../core/src/index.ts') },
            { find: /^@sigta-hq\/contract\/icons$/, replacement: source('../contract/src/icons.ts') },
        ],
    },
    test: {
        include: ['test/**/*.test.ts'],
        environment: 'jsdom',
    },
});
