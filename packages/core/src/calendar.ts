/* ---------------------------------------------------------------------------
   Calendar — the date logic behind the calendar and the date picker in both
   runtimes: ISO dates (YYYY-MM-DD) in and out, the 6×7 Monday-first month
   grid, min / max bounds, the English labels, typed-date parsing and the
   WAI-ARIA date-grid keyboard map. Each function reproduces what the Blade
   view computes in PHP (Carbon), so both runtimes render the same grid.

   Pure and SSR-safe. Dates are calendar days, not instants: the math runs in
   UTC so no time zone or DST can shift a day. "Today" is the one input that
   depends on where the code runs; every function that needs it takes it as
   an argument (todayIso() reads the local date of an instant, Date.now() by
   default, so a test or an example can pin it).
--------------------------------------------------------------------------- */

export const MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as const;
export const SHORT_MONTH_NAMES = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as const;
/** The grid's column headers, Monday first: the abbreviation shown and the name announced. */
export const WEEKDAYS = [
    { abbr: 'Mo', name: 'Monday' },
    { abbr: 'Tu', name: 'Tuesday' },
    { abbr: 'We', name: 'Wednesday' },
    { abbr: 'Th', name: 'Thursday' },
    { abbr: 'Fr', name: 'Friday' },
    { abbr: 'Sa', name: 'Saturday' },
    { abbr: 'Su', name: 'Sunday' },
] as const;

export interface DateParts {
    year: number;
    /** 1–12. */
    month: number;
    day: number;
}

export interface MonthView {
    year: number;
    /** 1–12. */
    month: number;
}

/** A date bound or value: an ISO date, or nothing. */
export type IsoDate = string | null | undefined;

const pad2 = (n: number): string => String(n).padStart(2, '0');

/** A UTC Date for a calendar day; month and day overflow into the next ones (32 Jan → 1 Feb). */
function utc(year: number, month: number, day: number): Date {
    // setUTCFullYear, unlike Date.UTC, takes years below 100 as written.
    const date = new Date(0);
    date.setUTCFullYear(year, month - 1, day);
    return date;
}

const fromUtc = (date: Date): string => `${String(date.getUTCFullYear()).padStart(4, '0')}-${pad2(date.getUTCMonth() + 1)}-${pad2(date.getUTCDate())}`;

/** The ISO date of a year, month (1–12) and day, normalizing overflow: isoDate(2026, 13, 1) is 2027-01-01. */
export function isoDate(year: number, month: number, day: number): string {
    return fromUtc(utc(year, month, day));
}

/** The parts of a strict ISO date (YYYY-MM-DD that exists), else null. */
export function parseIso(iso: IsoDate): DateParts | null {
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(iso ?? ''));
    if (!match) return null;
    const [year, month, day] = [Number(match[1]), Number(match[2]), Number(match[3])];
    return isoDate(year, month, day) === iso ? { year, month, day } : null;
}

/** The local calendar date of an instant (now by default), as an ISO date. */
export function todayIso(now: number | Date = Date.now()): string {
    const date = now instanceof Date ? now : new Date(now);
    return `${String(date.getFullYear()).padStart(4, '0')}-${pad2(date.getMonth() + 1)}-${pad2(date.getDate())}`;
}

/** The days in a month (1–12). */
export function daysInMonth(year: number, month: number): number {
    return utc(year, month + 1, 0).getUTCDate();
}

/** The date `days` after (or before) an ISO date. */
export function addDays(iso: string, days: number): string {
    const p = parseIso(iso);
    return p ? isoDate(p.year, p.month, p.day + days) : iso;
}

/** The same day `months` later (or earlier), kept inside the target month: 31 Jan + 1 month is 28 Feb. */
export function addMonths(iso: string, months: number): string {
    const p = parseIso(iso);
    if (!p) return iso;
    const target = shiftMonth(p, months);
    return isoDate(target.year, target.month, Math.min(p.day, daysInMonth(target.year, target.month)));
}

/** The month `delta` months from a month view. */
export function shiftMonth(view: MonthView, delta: number): MonthView {
    const index = view.year * 12 + (view.month - 1) + delta;
    return { year: Math.floor(index / 12), month: (((index % 12) + 12) % 12) + 1 };
}

/** The column of a date in a Monday-first week: 0 = Monday … 6 = Sunday. */
export function weekdayIndex(iso: string): number {
    const p = parseIso(iso);
    return p ? (utc(p.year, p.month, p.day).getUTCDay() + 6) % 7 : 0;
}

/** Whether a date falls before min or after max (ISO dates compare as strings). */
export function isOutOfRange(iso: string, min?: IsoDate, max?: IsoDate): boolean {
    return (!!min && iso < min) || (!!max && iso > max);
}

/** The month a view opens on: the selected date's, else today's. */
export function monthOf(iso: string): MonthView {
    const p = parseIso(iso);
    return p ? { year: p.year, month: p.month } : { year: 1970, month: 1 };
}

/* -------- labels -------------------------------------------------------------- */

/** The grid's caption: "July 2026". */
export function monthTitle(year: number, month: number): string {
    return `${MONTH_NAMES[month - 1]} ${year}`;
}

/** What a day announces: "9 July 2026" (PHP's j F Y). */
export function dayLabel(iso: string): string {
    const p = parseIso(iso);
    return p ? `${p.day} ${MONTH_NAMES[p.month - 1]} ${p.year}` : '';
}

/** A date as the date picker shows it: "9 Jul 2026" (PHP's j M Y); nothing for no date. */
export function formatDate(iso: IsoDate): string {
    const p = parseIso(iso);
    return p ? `${p.day} ${SHORT_MONTH_NAMES[p.month - 1]} ${p.year}` : '';
}

/**
 * A typed date as an ISO date: '' for an empty field, null when it is not a
 * date. An ISO date is read as written; anything else goes through the
 * browser's Date.parse ("9 Jul 2026", "July 9, 2026") and keeps its local day.
 */
export function parseDate(text: string | null | undefined): string | '' | null {
    const typed = String(text ?? '').trim();
    if (!typed) return '';
    if (/^\d{4}-\d{2}-\d{2}$/.test(typed)) return parseIso(typed) ? typed : null;
    const ts = Date.parse(typed);
    return Number.isNaN(ts) ? null : todayIso(ts);
}

/* -------- the month grid ---------------------------------------------------- */

export interface CalendarCell {
    date: string;
    day: number;
    /** Spills over from the month before or after. */
    outside: boolean;
    disabled: boolean;
    selected: boolean;
    today: boolean;
    /** "9 July 2026". */
    label: string;
}

export interface CalendarBounds {
    selected?: IsoDate;
    min?: IsoDate;
    max?: IsoDate;
    /** Today's ISO date (todayIso() where the code runs, or a pinned one). */
    today?: IsoDate;
}

/** The 42 days (6 weeks, Monday first) shown for a month, spilling into the months around it. */
export function monthGrid(year: number, month: number, { selected = null, min = null, max = null, today = null }: CalendarBounds = {}): CalendarCell[] {
    const offset = weekdayIndex(isoDate(year, month, 1));
    const cells: CalendarCell[] = [];
    for (let i = 0; i < 42; i++) {
        const date = isoDate(year, month, 1 - offset + i);
        const p = parseIso(date)!;
        cells.push({
            date,
            day: p.day,
            outside: p.month !== month || p.year !== year,
            disabled: isOutOfRange(date, min, max),
            selected: selected === date,
            today: today === date,
            label: dayLabel(date),
        });
    }
    return cells;
}

/** Whether the month before can be shown: its last day is not before min. */
export function canShowPrev(view: MonthView, min?: IsoDate): boolean {
    return !min || isoDate(view.year, view.month, 0) >= min;
}

/** Whether the month after can be shown: its first day is not after max. */
export function canShowNext(view: MonthView, max?: IsoDate): boolean {
    return !max || isoDate(view.year, view.month + 1, 1) <= max;
}

/**
 * The day that takes the tab stop when a month is shown (roving tabindex):
 * the selected day, else today, when in this month and in range; else the
 * month's first day in range; else its first day.
 */
export function initialFocus(view: MonthView, { selected = null, min = null, max = null, today = null }: CalendarBounds = {}): string {
    const inView = (iso: IsoDate): iso is string => {
        const p = parseIso(iso);
        return !!p && p.year === view.year && p.month === view.month && !isOutOfRange(iso!, min, max);
    };
    if (inView(selected)) return selected;
    if (inView(today)) return today;
    for (let day = 1, last = daysInMonth(view.year, view.month); day <= last; day++) {
        const iso = isoDate(view.year, view.month, day);
        if (!isOutOfRange(iso, min, max)) return iso;
    }
    return isoDate(view.year, view.month, 1);
}

/* -------- keyboard -------------------------------------------------------------- */

/** The keys the grid handles (WAI-ARIA date grid): days, weeks, week edges, months. */
export const CALENDAR_KEYS = ['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End', 'PageUp', 'PageDown'] as const;

/**
 * The date a key moves the focus to from `focused`, or null for a key the
 * grid does not handle. The caller refuses a target out of range.
 */
export function keyTarget(focused: string, key: string): string | null {
    switch (key) {
        case 'ArrowLeft': return addDays(focused, -1);
        case 'ArrowRight': return addDays(focused, 1);
        case 'ArrowUp': return addDays(focused, -7);
        case 'ArrowDown': return addDays(focused, 7);
        case 'Home': return addDays(focused, -weekdayIndex(focused));
        case 'End': return addDays(focused, 6 - weekdayIndex(focused));
        case 'PageUp': return addMonths(focused, -1);
        case 'PageDown': return addMonths(focused, 1);
        default: return null;
    }
}
