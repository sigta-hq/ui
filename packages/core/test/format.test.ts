// The shared formatting: each case is what the Blade view prints in PHP, so
// the Svelte twin of duration, money, elapsed-clock and progress agrees.
import { describe, expect, it } from 'vitest';
import { currencyCode, elapsedStart, formatDuration, formatElapsed, formatMoney, numberFormat, phpFloat, progressPercent } from '../src/format.ts';

describe('formatDuration', () => {
    it('writes whole minutes compactly', () => {
        expect(formatDuration(75)).toBe('1h 15m');
        expect(formatDuration(45)).toBe('45m');
        expect(formatDuration(605)).toBe('10h 5m');
    });

    it('drops the minutes on an exact hour', () => {
        expect(formatDuration(120)).toBe('2h');
    });

    it('reads zero, null and negatives as 0m', () => {
        expect(formatDuration(0)).toBe('0m');
        expect(formatDuration(null)).toBe('0m');
        expect(formatDuration(undefined)).toBe('0m');
        expect(formatDuration(-30)).toBe('0m');
    });

    it('truncates fractions like PHP (int)', () => {
        expect(formatDuration(75.9)).toBe('1h 15m');
        expect(formatDuration('90')).toBe('1h 30m');
    });

    it('writes an HH:MM clock', () => {
        expect(formatDuration(90, 'clock')).toBe('01:30');
        expect(formatDuration(605, 'clock')).toBe('10:05');
        expect(formatDuration(0, 'clock')).toBe('00:00');
        expect(formatDuration(6000, 'clock')).toBe('100:00');
    });

    it('treats an unknown format as compact', () => {
        expect(formatDuration(75, 'nope')).toBe('1h 15m');
    });
});

describe('formatElapsed', () => {
    it('writes HH:MM:SS', () => {
        expect(formatElapsed(0)).toBe('00:00:00');
        expect(formatElapsed(999)).toBe('00:00:00');
        expect(formatElapsed(1000)).toBe('00:00:01');
        expect(formatElapsed((2 * 3600 + 3 * 60 + 4) * 1000)).toBe('02:03:04');
        expect(formatElapsed(100 * 3600 * 1000)).toBe('100:00:00');
    });

    it('clamps a start in the future to zero', () => {
        expect(formatElapsed(-5000)).toBe('00:00:00');
    });

    it('takes the start from an ISO string, a Date, epoch ms, or now', () => {
        const iso = '2026-08-25T09:00:00+00:00';
        expect(elapsedStart(iso, 1)).toBe(Date.parse(iso));
        expect(elapsedStart(new Date(iso), 1)).toBe(Date.parse(iso));
        expect(elapsedStart(42, 1)).toBe(42);
        expect(elapsedStart(null, 7)).toBe(7);
        expect(elapsedStart(undefined, 7)).toBe(7);
    });
});

describe('numberFormat (PHP number_format)', () => {
    it('groups and separates', () => {
        expect(numberFormat(1234.56, 2, ',', '.')).toBe('1.234,56');
        expect(numberFormat(1234567.891, 2)).toBe('1,234,567.89');
        expect(numberFormat(999, 0)).toBe('999');
        expect(numberFormat(1000, 0, '.', ' ')).toBe('1 000');
    });

    it('rounds half away from zero, the way PHP pre-rounds', () => {
        expect(numberFormat(0.5, 0)).toBe('1');
        expect(numberFormat(-0.5, 0)).toBe('-1');
        expect(numberFormat(2.5, 0)).toBe('3');
        expect(numberFormat(0.15, 1)).toBe('0.2');
        expect(numberFormat(-0.15, 1)).toBe('-0.2');
        expect(numberFormat(10.05, 1)).toBe('10.1');
        expect(numberFormat(1234.56, 0)).toBe('1,235');
    });

    it('never prints -0', () => {
        expect(numberFormat(-0.01, 0)).toBe('0');
        expect(numberFormat(-0.49, 0)).toBe('0');
    });

    it('pads extra decimals with zeros', () => {
        expect(numberFormat(1234.56, 3, ',', '.')).toBe('1.234,560');
        expect(numberFormat(0.05, 2)).toBe('0.05');
    });
});

describe('formatMoney', () => {
    it('formats known currencies', () => {
        expect(formatMoney(123456, { currency: 'DKK' })).toBe('1.234,56 kr.');
        expect(formatMoney(123456, { currency: 'EUR' })).toBe('€1.234,56');
        expect(formatMoney(123456, { currency: 'USD' })).toBe('$1,234.56');
        expect(formatMoney(123456, { currency: 'GBP' })).toBe('£1,234.56');
        expect(formatMoney(123456, { currency: 'SEK' })).toBe('1 234,56 kr');
        expect(formatMoney(123456, { currency: 'NOK' })).toBe('1 234,56 kr');
    });

    it('defaults to DKK and reads the code case-insensitively', () => {
        expect(formatMoney(145000)).toBe('1.450,00 kr.');
        expect(formatMoney(100, { currency: 'usd' })).toBe('$1.00');
        expect(currencyCode('usd')).toBe('USD');
    });

    it('supports whole-major display', () => {
        expect(formatMoney(123456, { currency: 'USD', decimals: 0 })).toBe('$1,235');
        expect(formatMoney(145000, { currency: 'DKK', decimals: 0 })).toBe('1.450 kr.');
    });

    it('prints an unknown code as a trailing symbol', () => {
        expect(formatMoney(5000, { currency: 'JPY' })).toBe('50.00 JPY');
    });

    it('reads null as zero', () => {
        expect(formatMoney(null, { currency: 'USD' })).toBe('$0.00');
    });

    it('lets every piece be overridden', () => {
        expect(formatMoney(100000, { currency: 'USD', symbol: 'US$', thousands: ' ' })).toBe('US$1 000.00');
        expect(formatMoney(123456, { currency: 'DKK', position: 'before', symbol: 'DKK ' })).toBe('DKK 1.234,56');
        expect(formatMoney(123456, { currency: 'USD', decimal: ',', thousands: '.' })).toBe('$1.234,56');
    });

    it('keeps the sign of a negative amount', () => {
        expect(formatMoney(-123456, { currency: 'USD' })).toBe('$-1,234.56');
        expect(formatMoney(-123456, { currency: 'DKK' })).toBe('-1.234,56 kr.');
    });
});

describe('progressPercent', () => {
    it('is value over max, clamped to 0–100', () => {
        expect(progressPercent(24)).toBe(24);
        expect(progressPercent(30, 120)).toBe(25);
        expect(progressPercent(150)).toBe(100);
        expect(progressPercent(-5)).toBe(0);
        expect(progressPercent(null)).toBe(0);
    });

    it('survives a max of zero', () => {
        expect(progressPercent(0, 0)).toBe(0);
        expect(progressPercent(1, 0)).toBe(100);
    });

    it('prints floats with PHP precision', () => {
        expect(phpFloat(25)).toBe('25');
        expect(phpFloat(100 / 3)).toBe('33.333333333333');
        expect(phpFloat(12.5)).toBe('12.5');
    });
});
