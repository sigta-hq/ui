// The time picker's logic: each case is what the time-picker Blade view
// computes in PHP, so the Svelte twin lists the same times.
import { describe, expect, it } from 'vitest';
import {
    activeFor,
    formatTime,
    isTimeOutOfRange,
    minutesToTime,
    moveActive,
    normalizeTime,
    parseTime,
    timeOptions,
    timeStep,
    timeToMinutes,
} from '../src/time.ts';

describe('values', () => {
    it('converts between HH:MM and minutes', () => {
        expect(timeToMinutes('09:30')).toBe(570);
        expect(timeToMinutes('9:05')).toBe(545);
        expect(minutesToTime(570)).toBe('09:30');
        expect(minutesToTime(0)).toBe('00:00');
    });

    it('normalizes a model value to HH:MM', () => {
        expect(normalizeTime('9:30')).toBe('09:30');
        expect(normalizeTime('09:30:00')).toBe('09:30');
        expect(normalizeTime('24:00')).toBeNull();
        expect(normalizeTime('soon')).toBeNull();
        expect(normalizeTime(null)).toBeNull();
    });

    it('checks min and max', () => {
        expect(isTimeOutOfRange('08:45', '09:00', '17:00')).toBe(true);
        expect(isTimeOutOfRange('17:15', '09:00', '17:00')).toBe(true);
        expect(isTimeOutOfRange('17:00', '09:00', '17:00')).toBe(false);
        expect(isTimeOutOfRange('03:00', null, null)).toBe(false);
    });
});

describe('labels', () => {
    it('prints 24-hour and 12-hour labels', () => {
        expect(formatTime('13:30')).toBe('13:30');
        expect(formatTime('13:30', '12')).toBe('1:30 PM');
        expect(formatTime('00:15', '12')).toBe('12:15 AM');
        expect(formatTime('12:00', '12')).toBe('12:00 PM');
        expect(formatTime(null)).toBe('');
    });

    it('parses typed times, with or without am / pm', () => {
        expect(parseTime('14:45')).toBe('14:45');
        expect(parseTime(' 9:05 ')).toBe('09:05');
        expect(parseTime('2:45 pm')).toBe('14:45');
        expect(parseTime('12:00 AM')).toBe('00:00');
        expect(parseTime('12:30pm')).toBe('12:30');
        expect(parseTime('')).toBe('');
        expect(parseTime('25:00')).toBeNull();
        expect(parseTime('13:30 pm')).toBeNull();
        expect(parseTime('noon')).toBeNull();
    });
});

describe('timeOptions', () => {
    it('lists every 30 minutes across the day by default', () => {
        const options = timeOptions();
        expect(options).toHaveLength(48);
        expect(options[0]).toEqual({ value: '00:00', label: '00:00' });
        expect(options[47]).toEqual({ value: '23:30', label: '23:30' });
    });

    it('honours step and the bounds, both included', () => {
        const options = timeOptions({ min: '09:00', max: '17:00', step: 60 });
        expect(options.map((o) => o.value)).toEqual(['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00']);
        expect(timeOptions({ min: '09:00', max: '17:00', step: '15' })).toHaveLength(33);
    });

    it('labels in the 12-hour format without changing the values', () => {
        expect(timeOptions({ min: '13:30', max: '13:30', format: '12' })).toEqual([{ value: '13:30', label: '1:30 PM' }]);
    });

    it('treats a step below one minute as one', () => {
        expect(timeStep(0)).toBe(1);
        expect(timeStep('-5')).toBe(1);
        expect(timeStep(null)).toBe(30);
        expect(timeOptions({ min: '10:00', max: '10:02', step: 0 })).toHaveLength(3);
    });
});

describe('the listbox', () => {
    const options = timeOptions({ min: '09:00', max: '10:00', step: 30 });

    it('activates the chosen option on open, else the first', () => {
        expect(activeFor(options, '09:30')).toBe(1);
        expect(activeFor(options, '09:45')).toBe(0);
        expect(activeFor([], '09:30')).toBe(-1);
    });

    it('moves with wrap-around, starting on the first', () => {
        expect(moveActive(-1, 1, 3)).toBe(0);
        expect(moveActive(-1, -1, 3)).toBe(0);
        expect(moveActive(2, 1, 3)).toBe(0);
        expect(moveActive(0, -1, 3)).toBe(2);
        expect(moveActive(0, 1, 0)).toBe(-1);
    });
});
