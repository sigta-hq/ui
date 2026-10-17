// What the money input and the duration input parse and show. The Blade
// views compute the initial text in PHP and the Livewire runtime runs these
// same functions on the client, so each case is what Livewire emits today.
import { describe, expect, it } from 'vitest';
import { durationDraft, durationLabel, durationMinutes, majorFromMinor, minorFromMajor, parseDuration } from '../src/format.ts';

describe('majorFromMinor', () => {
    it('writes minor units as plain major units without trailing zeros', () => {
        expect(majorFromMinor(145000)).toBe('1450');
        expect(majorFromMinor(145050)).toBe('1450.5');
        expect(majorFromMinor(145055)).toBe('1450.55');
        expect(majorFromMinor(123456789)).toBe('1234567.89');
    });

    it('keeps a zero and a sign', () => {
        expect(majorFromMinor(0)).toBe('0');
        expect(majorFromMinor(5)).toBe('0.05');
        expect(majorFromMinor(-4250)).toBe('-42.5');
    });

    it('reads numeric strings like PHP (int)', () => {
        expect(majorFromMinor('145050')).toBe('1450.5');
        expect(majorFromMinor('99.9')).toBe('0.99');
    });

    it('is empty without a numeric value', () => {
        expect(majorFromMinor(null)).toBe('');
        expect(majorFromMinor(undefined)).toBe('');
        expect(majorFromMinor('')).toBe('');
        expect(majorFromMinor('abc')).toBe('');
    });
});

describe('minorFromMajor', () => {
    it('reads major units into whole minor units', () => {
        expect(minorFromMajor('1450')).toBe(145000);
        expect(minorFromMajor('1450.5')).toBe(145050);
        expect(minorFromMajor('0.05')).toBe(5);
        expect(minorFromMajor(12)).toBe(1200);
    });

    it('takes a comma as the decimal point', () => {
        expect(minorFromMajor('1450,5')).toBe(145050);
        expect(minorFromMajor('99,95')).toBe(9995);
    });

    it('rounds to whole minor units', () => {
        expect(minorFromMajor('10.555')).toBe(1056);
        expect(minorFromMajor('-3.5')).toBe(-350);
    });

    it('reads a leading number like parseFloat', () => {
        expect(minorFromMajor('12 kr')).toBe(1200);
    });

    it('is null for what is not a number', () => {
        expect(minorFromMajor('')).toBeNull();
        expect(minorFromMajor('kr')).toBeNull();
        expect(minorFromMajor(null)).toBeNull();
        expect(minorFromMajor(undefined)).toBeNull();
    });
});

describe('durationMinutes', () => {
    it('reads whole minutes, never below zero', () => {
        expect(durationMinutes(90)).toBe(90);
        expect(durationMinutes('119')).toBe(119);
        expect(durationMinutes(75.9)).toBe(75);
        expect(durationMinutes(-5)).toBe(0);
        expect(durationMinutes(0)).toBe(0);
    });

    it('is null without a value', () => {
        expect(durationMinutes(null)).toBeNull();
        expect(durationMinutes(undefined)).toBeNull();
        expect(durationMinutes('')).toBeNull();
        expect(durationMinutes('soon')).toBeNull();
    });
});

describe('durationLabel', () => {
    it('writes a tight label', () => {
        expect(durationLabel(119)).toBe('1h59m');
        expect(durationLabel(90)).toBe('1h30m');
        expect(durationLabel(120)).toBe('2h');
        expect(durationLabel(45)).toBe('45m');
        expect(durationLabel('605')).toBe('10h5m');
    });

    it('shows the placeholder without a value', () => {
        expect(durationLabel(null)).toBe('0m');
        expect(durationLabel(0)).toBe('0m');
        expect(durationLabel(null, 'Add time')).toBe('Add time');
        expect(durationLabel(-10, '—')).toBe('—');
    });
});

describe('durationDraft', () => {
    it('writes an HH:MM draft', () => {
        expect(durationDraft(119)).toBe('01:59');
        expect(durationDraft(5)).toBe('00:05');
        expect(durationDraft(605)).toBe('10:05');
    });

    it('is empty without a value', () => {
        expect(durationDraft(null)).toBe('');
        expect(durationDraft(0)).toBe('');
    });
});

describe('parseDuration', () => {
    it('reads H:MM and HH:MM', () => {
        expect(parseDuration('1:30')).toBe(90);
        expect(parseDuration('02:05')).toBe(125);
        expect(parseDuration(' 0:45 ')).toBe(45);
    });

    it('forgives a missing side', () => {
        expect(parseDuration('2:')).toBe(120);
        expect(parseDuration(':15')).toBe(15);
    });

    it('reads a bare minute count', () => {
        expect(parseDuration('90')).toBe(90);
        expect(parseDuration('90m')).toBe(90);
    });

    it('reads nonsense, blanks and negatives as 0', () => {
        expect(parseDuration('')).toBe(0);
        expect(parseDuration('   ')).toBe(0);
        expect(parseDuration(null)).toBe(0);
        expect(parseDuration('soon')).toBe(0);
        expect(parseDuration('-30')).toBe(0);
        expect(parseDuration('-1:00')).toBe(0);
    });
});
