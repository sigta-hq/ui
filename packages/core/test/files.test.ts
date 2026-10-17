// The file upload's list: names and sizes as both runtimes print them.
import { describe, expect, it } from 'vitest';
import { fileEntries, formatBytes } from '../src/files.ts';

describe('formatBytes', () => {
    it('writes bytes whole and larger units with one decimal below ten', () => {
        expect(formatBytes(0)).toBe('0 B');
        expect(formatBytes(512)).toBe('512 B');
        expect(formatBytes(1536)).toBe('1.5 KB');
        expect(formatBytes(12 * 1024 * 1024)).toBe('12 MB');
        expect(formatBytes(3.25 * 1024 ** 3)).toBe('3.3 GB');
        expect(formatBytes(2048 * 1024 ** 4)).toBe('2048 TB');
    });
});

describe('fileEntries', () => {
    it('lists each file with its formatted size', () => {
        expect(fileEntries([{ name: 'brief.pdf', size: 2048 }, { name: 'logo.png', size: 300 }])).toEqual([
            { name: 'brief.pdf', size: '2.0 KB' },
            { name: 'logo.png', size: '300 B' },
        ]);
        expect(fileEntries(null)).toEqual([]);
    });
});
