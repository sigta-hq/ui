import { describe, expect, it } from 'vitest';
import { classString, ownsDisplay, ownsPadding } from '../src/classes.ts';

describe('classString', () => {
    it('flattens the class values Svelte accepts', () => {
        expect(classString('p-4 flex')).toBe('p-4 flex');
        expect(classString(['p-4', false, null, ['grid', { 'gap-2': true, hidden: false }]])).toBe('p-4 grid gap-2');
        expect(classString({ 'p-4': 1, flex: 0 })).toBe('p-4');
        expect(classString(undefined)).toBe('');
    });
});

describe('ownsPadding', () => {
    it('sees any padding utility, under variants and important', () => {
        for (const cls of ['p-4', 'px-6', 'py-2', 'pt-0', 'pr-1', 'pb-3', 'pl-2', 'ps-4', 'pe-4', 'mt-6 sm:!p-5', 'md:hover:p-2', '!p-2', 'h-full p-5', 'p-[clamp(1rem,2vw,2rem)]']) {
            expect(ownsPadding(cls), cls).toBe(true);
        }
    });

    it('ignores classes that only look alike', () => {
        for (const cls of ['', 'pointer-events-none place-items-center', 'mt-6', 'gap-4 top-2', 'shadow-paper', 'pz-4']) {
            expect(ownsPadding(cls), cls).toBe(false);
        }
        expect(ownsPadding(undefined)).toBe(false);
    });
});

describe('ownsDisplay', () => {
    it('sees an unprefixed display utility', () => {
        for (const cls of ['flex items-center justify-between', 'grid gap-1', '!block', 'block!', 'inline-flex', 'inline-grid', 'flow-root', 'gap-2 inline-block']) {
            expect(ownsDisplay(cls), cls).toBe(true);
        }
    });

    it('leaves responsive, direction and look-alike classes to build on the default', () => {
        for (const cls of ['', 'gap-4 sm:flex-row md:grid', 'flex-1 flex-wrap', 'flex-row items-stretch', 'grid-cols-2', 'blocky', 'max-w-sm gap-4']) {
            expect(ownsDisplay(cls), cls).toBe(false);
        }
    });
});
