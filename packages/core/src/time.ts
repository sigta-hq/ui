/* ---------------------------------------------------------------------------
   Time — the logic behind the time picker in both runtimes: HH:MM (24h)
   values, the list of times every `step` minutes between min and max, 12- or
   24-hour labels, typed-time parsing and the listbox's wrap-around moves.
   Each function reproduces what the Blade view computes in PHP, so both
   runtimes list the same times. Pure and SSR-safe.
--------------------------------------------------------------------------- */

export type TimeFormat = '24' | '12';

export interface TimeOption {
    /** HH:MM, 24h — what the model holds. */
    value: string;
    /** What the list shows: 13:30, or 1:30 PM. */
    label: string;
}

const pad2 = (n: number): string => String(n).padStart(2, '0');

/** PHP's (int) cast of a string's leading digits: '09' → 9, '' → 0. */
const int = (text: string | undefined): number => {
    const n = Number.parseInt(String(text ?? ''), 10);
    return Number.isFinite(n) ? n : 0;
};

/** Minutes since midnight of an "H:MM" value (as PHP's explode + int cast reads it). */
export function timeToMinutes(value: string): number {
    const [h, m] = String(value).split(':');
    return int(h) * 60 + int(m);
}

/** "HH:MM" of minutes since midnight. */
export function minutesToTime(minutes: number): string {
    return `${pad2(Math.floor(minutes / 60))}:${pad2(minutes % 60)}`;
}

/**
 * A model value as HH:MM: "9:30" and "09:30:00" read as 09:30. Anything that
 * is not a time of day is null.
 */
export function normalizeTime(value: string | null | undefined): string | null {
    const match = /^(\d{1,2}):(\d{2})(?::\d{2}(?:\.\d+)?)?$/.exec(String(value ?? '').trim());
    if (!match) return null;
    const [h, m] = [Number(match[1]), Number(match[2])];
    return h < 24 && m < 60 ? `${pad2(h)}:${pad2(m)}` : null;
}

/** An HH:MM value as the list shows it: "13:30", or "1:30 PM" in the 12-hour format. Nothing for no value. */
export function formatTime(value: string | null | undefined, format: TimeFormat | string = '24'): string {
    if (!value) return '';
    const [h, m] = String(value).split(':').map(int);
    if (String(format) === '12') return `${h % 12 || 12}:${pad2(m)} ${h < 12 ? 'AM' : 'PM'}`;
    return `${pad2(h)}:${pad2(m)}`;
}

/**
 * A typed time as HH:MM: "14:45", "9:05", "2:45 pm", "12:00 AM". '' for an
 * empty field, null when it is not a time of day.
 */
export function parseTime(text: string | null | undefined): string | '' | null {
    const typed = String(text ?? '').trim();
    if (!typed) return '';
    const match = /^(\d{1,2}):(\d{2})\s*(am|pm)?$/i.exec(typed);
    if (!match) return null;
    let h = Number(match[1]);
    const m = Number(match[2]);
    const meridiem = match[3]?.toLowerCase();
    if (meridiem) {
        if (h === 12) h = 0;
        if (meridiem === 'pm') h += 12;
    }
    return h < 24 && m < 60 ? `${pad2(h)}:${pad2(m)}` : null;
}

/** Whether an HH:MM value falls before min or after max. */
export function isTimeOutOfRange(value: string, min?: string | null, max?: string | null): boolean {
    const minutes = timeToMinutes(value);
    return (!!min && minutes < timeToMinutes(min)) || (!!max && minutes > timeToMinutes(max));
}

export interface TimeOptionsConfig {
    min?: string | null;
    max?: string | null;
    /** Minutes between options; below 1 counts as 1. */
    step?: number | string | null;
    format?: TimeFormat | string | null;
}

/** The step in whole minutes, at least 1 (PHP's max(1, (int) $step)). */
export function timeStep(step: number | string | null | undefined): number {
    return Math.max(1, Math.trunc(Number(step ?? 30)) || 0);
}

/** The times listed: every `step` minutes from min (00:00) to max (23:59), both included. */
export function timeOptions({ min = null, max = null, step = 30, format = '24' }: TimeOptionsConfig = {}): TimeOption[] {
    const every = timeStep(step);
    const start = min ? timeToMinutes(min) : 0;
    const end = max ? timeToMinutes(max) : 23 * 60 + 59;
    const options: TimeOption[] = [];
    for (let minutes = start; minutes <= end; minutes += every) {
        const value = minutesToTime(minutes);
        options.push({ value, label: formatTime(value, format ?? '24') });
    }
    return options;
}

/**
 * The listbox's active option after a move: the first when nothing is
 * active yet, else `delta` steps on, wrapping round. -1 for an empty list.
 */
export function moveActive(index: number, delta: number, count: number): number {
    if (count <= 0) return -1;
    if (index < 0) return 0;
    return (((index + delta) % count) + count) % count;
}

/** The option to make active when the list opens: the chosen one, else the first. */
export function activeFor(options: readonly TimeOption[], value: string | null | undefined): number {
    if (!options.length) return -1;
    const index = options.findIndex((option) => option.value === value);
    return index >= 0 ? index : 0;
}
