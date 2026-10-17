// Sigta's own strings: Blade translates them with __(), Svelte with the
// translator from setMessages(), keyed by the same English text — so one
// Laravel-style lang/<locale>.json serves both runtimes.
import { afterEach, describe, expect, it } from 'vitest';
import { flushSync, mount, unmount } from 'svelte';
import { existsSync, readFileSync } from 'node:fs';
import { join } from 'node:path';
import Translated from './fixtures/Translated.svelte';

const root = join(__dirname, '../../..');
const { components } = JSON.parse(readFileSync(join(root, 'packages/contract/manifest.json'), 'utf8'));

const bladeFile = (name: string) => {
    const base = join(root, 'resources/views/components', ...name.split('.'));
    return [`${base}.blade.php`, join(base, 'index.blade.php')].find(existsSync);
};
const strings = (source: string, call: RegExp) => [...source.matchAll(call)].map((m) => m[1]).sort();

describe('translatable strings', () => {
    for (const component of components) {
        const blade = bladeFile(component.name);
        if (!blade) continue;
        it(`${component.name}: Blade's __() strings are Svelte's t() strings`, () => {
            const svelte = readFileSync(join(root, 'packages/svelte/src/lib/components', `${component.svelte}.svelte`), 'utf8');
            expect(strings(svelte, /\bt\('([^']+)'\)/g)).toEqual(strings(readFileSync(blade, 'utf8'), /__\('([^']+)'\)/g));
        });
    }
});

describe('setMessages', () => {
    let app: Record<string, unknown> | null = null;
    afterEach(() => {
        if (app) unmount(app);
        app = null;
        document.body.innerHTML = '';
    });
    const page = (messages: Record<string, string>) => {
        const target = document.createElement('div');
        document.body.append(target);
        app = mount(Translated, { target, props: { messages } });
        flushSync();
        return target;
    };

    it('translates the components below it', () => {
        const target = page({ amount: 'beløb', Currency: 'Valuta', 'Search people': 'Søg personer', Unassigned: 'Ingen', 'No people found.': 'Ingen fundet.', Assign: 'Tildel' });
        expect(target.querySelector('[data-part="amount"]')?.getAttribute('aria-label')).toBe('Pris beløb');
        expect(target.querySelector('[aria-label="Valuta"]')).not.toBeNull();
        expect(target.textContent).toContain('Tildel');
        // The people list renders once the picker opens.
        (target.querySelector('[data-sigta="assignee-picker"] button') as HTMLButtonElement).click();
        flushSync();
        expect(document.querySelector('[data-scope="assignee-picker"][data-part="search"]')?.getAttribute('placeholder')).toBe('Søg personer');
        expect(document.querySelector('[data-scope="assignee-picker"][data-part="unassign"]')?.textContent?.trim()).toBe('Ingen');
    });

    it('falls back to the English text', () => {
        const target = page({});
        expect(target.querySelector('[data-part="amount"]')?.getAttribute('aria-label')).toBe('Pris amount');
        expect(target.querySelector('[aria-label="Currency"]')).not.toBeNull();
    });
});
