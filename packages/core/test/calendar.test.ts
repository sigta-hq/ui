// The calendar's date logic: each case is what the calendar Blade view
// computes in PHP (Carbon), so the Svelte twin renders the same grid.
import { describe, expect, it } from 'vitest';
import {
    addDays,
    addMonths,
    canShowNext,
    canShowPrev,
    dayLabel,
    formatDate,
    initialFocus,
    isOutOfRange,
    isoDate,
    keyTarget,
    monthGrid,
    monthTitle,
    parseDate,
    parseIso,
    shiftMonth,
    todayIso,
    weekdayIndex,
} from '../src/calendar.ts';

describe('ISO dates', () => {
    it('builds and normalizes overflow', () => {
        expect(isoDate(2026, 7, 9)).toBe('2026-07-09');
        expect(isoDate(2026, 13, 1)).toBe('2027-01-01');
        expect(isoDate(2026, 3, 0)).toBe('2026-02-28');
        expect(isoDate(2024, 2, 29)).toBe('2024-02-29');
        expect(isoDate(2026, 1, 400)).toBe('2027-02-04');
    });

    it('parses only dates that exist', () => {
        expect(parseIso('2026-07-09')).toEqual({ year: 2026, month: 7, day: 9 });
        expect(parseIso('2026-02-30')).toBeNull();
        expect(parseIso('2026-7-9')).toBeNull();
        expect(parseIso(null)).toBeNull();
    });

    it('reads today as the local date of an instant', () => {
        expect(todayIso(new Date(2026, 9, 17, 0, 30))).toBe('2026-10-17');
        expect(todayIso(new Date(2026, 9, 17, 23, 59).getTime())).toBe('2026-10-17');
    });

    it('steps days and months, keeping the day inside the target month', () => {
        expect(addDays('2026-07-31', 1)).toBe('2026-08-01');
        expect(addDays('2026-01-01', -1)).toBe('2025-12-31');
        expect(addMonths('2026-01-31', 1)).toBe('2026-02-28');
        expect(addMonths('2024-03-31', -1)).toBe('2024-02-29');
        expect(addMonths('2026-12-15', 1)).toBe('2027-01-15');
        expect(shiftMonth({ year: 2026, month: 1 }, -1)).toEqual({ year: 2025, month: 12 });
        expect(shiftMonth({ year: 2026, month: 12 }, 1)).toEqual({ year: 2027, month: 1 });
    });

    it('counts weekdays from Monday', () => {
        expect(weekdayIndex('2026-07-06')).toBe(0); // a Monday
        expect(weekdayIndex('2026-07-12')).toBe(6); // a Sunday
    });

    it('checks min and max, either of which may be missing', () => {
        expect(isOutOfRange('2026-07-05', '2026-07-06', '2026-07-24')).toBe(true);
        expect(isOutOfRange('2026-07-25', '2026-07-06', '2026-07-24')).toBe(true);
        expect(isOutOfRange('2026-07-06', '2026-07-06', '2026-07-24')).toBe(false);
        expect(isOutOfRange('2026-07-25', null, null)).toBe(false);
    });
});

describe('labels', () => {
    it('prints the English labels the Blade view prints', () => {
        expect(monthTitle(2026, 7)).toBe('July 2026');
        expect(dayLabel('2026-07-09')).toBe('9 July 2026');
        expect(formatDate('2026-07-09')).toBe('9 Jul 2026');
        expect(formatDate(null)).toBe('');
    });

    it('parses typed dates: ISO as written, others through Date.parse, empty as cleared', () => {
        expect(parseDate('2026-07-09')).toBe('2026-07-09');
        expect(parseDate(' 9 Jul 2026 ')).toBe('2026-07-09');
        expect(parseDate('July 9, 2026')).toBe('2026-07-09');
        expect(parseDate('')).toBe('');
        expect(parseDate('not a date')).toBeNull();
        expect(parseDate('2026-02-30')).toBeNull();
    });
});

describe('monthGrid', () => {
    it('lays out 42 days from the Monday on or before the 1st', () => {
        const cells = monthGrid(2026, 7, { selected: '2026-07-09', today: '2026-07-20' });
        expect(cells).toHaveLength(42);
        expect(cells[0]).toMatchObject({ date: '2026-06-29', day: 29, outside: true });
        expect(cells[2]).toMatchObject({ date: '2026-07-01', day: 1, outside: false });
        expect(cells[41]).toMatchObject({ date: '2026-08-09', outside: true });
        expect(cells.filter((c) => c.selected).map((c) => c.date)).toEqual(['2026-07-09']);
        expect(cells.filter((c) => c.today).map((c) => c.date)).toEqual(['2026-07-20']);
        expect(cells[10].label).toBe('9 July 2026');
    });

    it('starts a month that begins on a Monday in the first column', () => {
        expect(monthGrid(2026, 6)[0].date).toBe('2026-06-01');
    });

    it('disables the days outside min and max', () => {
        const cells = monthGrid(2026, 7, { min: '2026-07-06', max: '2026-07-24' });
        expect(cells.find((c) => c.date === '2026-07-05')!.disabled).toBe(true);
        expect(cells.find((c) => c.date === '2026-07-06')!.disabled).toBe(false);
        expect(cells.find((c) => c.date === '2026-07-25')!.disabled).toBe(true);
    });

    it('allows the months around only while they hold a day in range', () => {
        expect(canShowPrev({ year: 2026, month: 7 }, '2026-07-06')).toBe(false);
        expect(canShowPrev({ year: 2026, month: 7 }, '2026-06-30')).toBe(true);
        expect(canShowNext({ year: 2026, month: 7 }, '2026-07-24')).toBe(false);
        expect(canShowNext({ year: 2026, month: 7 }, '2026-08-01')).toBe(true);
        expect(canShowPrev({ year: 2026, month: 1 }, null)).toBe(true);
    });
});

describe('initialFocus', () => {
    const july = { year: 2026, month: 7 };

    it('prefers the selected day, then today, in this month and in range', () => {
        expect(initialFocus(july, { selected: '2026-07-09', today: '2026-07-20' })).toBe('2026-07-09');
        expect(initialFocus(july, { selected: '2026-08-09', today: '2026-07-20' })).toBe('2026-07-20');
        expect(initialFocus(july, { selected: '2026-07-02', min: '2026-07-06', today: '2026-07-20' })).toBe('2026-07-20');
    });

    it('falls back to the first day in range, then the 1st', () => {
        expect(initialFocus(july, { min: '2026-07-06' })).toBe('2026-07-06');
        expect(initialFocus(july, {})).toBe('2026-07-01');
        expect(initialFocus(july, { min: '2026-09-01' })).toBe('2026-07-01');
    });
});

describe('keyTarget', () => {
    it('maps the date-grid keys', () => {
        const from = '2026-07-09'; // a Thursday
        expect(keyTarget(from, 'ArrowLeft')).toBe('2026-07-08');
        expect(keyTarget(from, 'ArrowRight')).toBe('2026-07-10');
        expect(keyTarget(from, 'ArrowUp')).toBe('2026-07-02');
        expect(keyTarget(from, 'ArrowDown')).toBe('2026-07-16');
        expect(keyTarget(from, 'Home')).toBe('2026-07-06');
        expect(keyTarget(from, 'End')).toBe('2026-07-12');
        expect(keyTarget(from, 'PageUp')).toBe('2026-06-09');
        expect(keyTarget(from, 'PageDown')).toBe('2026-08-09');
        expect(keyTarget(from, 'Enter')).toBeNull();
    });
});
