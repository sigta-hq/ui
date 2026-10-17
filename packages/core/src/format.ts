/* ---------------------------------------------------------------------------
   Formatting both runtimes share: durations, money, elapsed clocks and
   progress. Pure and SSR-safe. Each function reproduces what the Blade view
   computes in PHP, so a Svelte render and a Livewire render of the same data
   print the same text.
--------------------------------------------------------------------------- */

const pad2 = (n: number): string => String(n).padStart(2, '0');

/** PHP's (int) cast for the numbers a prop carries: truncates, NaN → 0. */
function toInt(value: unknown): number {
    const n = Math.trunc(Number(value ?? 0));
    return Number.isFinite(n) ? n : 0;
}

/* -------- duration ---------------------------------------------------------- */

export type DurationFormat = 'compact' | 'clock';

/**
 * Whole minutes as a compact label (`1h 15m`, exact hours drop the minutes:
 * `2h`, zero is `0m`) or an `HH:MM` clock. Negative and missing values are 0.
 */
export function formatDuration(minutes: number | string | null | undefined, format: DurationFormat | string = 'compact'): string {
    const total = Math.max(0, toInt(minutes));
    const h = Math.floor(total / 60);
    const m = total % 60;
    if (format === 'clock') return `${pad2(h)}:${pad2(m)}`;
    return h === 0 ? `${m}m` : m === 0 ? `${h}h` : `${h}h ${m}m`;
}

/* -------- elapsed clock ----------------------------------------------------- */

/** Milliseconds as an `HH:MM:SS` clock; negative spans read 00:00:00. */
export function formatElapsed(ms: number): string {
    const secs = Math.max(0, Math.floor(ms / 1000));
    return `${pad2(Math.floor(secs / 3600))}:${pad2(Math.floor((secs % 3600) / 60))}:${pad2(secs % 60)}`;
}

/** The start instant of an elapsed clock in epoch ms: an ISO string, a Date or epoch ms; missing means `now`. */
export function elapsedStart(since: string | number | Date | null | undefined, now: number): number {
    if (since === null || since === undefined || since === '') return now;
    return since instanceof Date ? since.getTime() : new Date(since).getTime();
}

/* -------- money ------------------------------------------------------------- */

export interface CurrencyFormat {
    symbol: string;
    position: 'before' | 'after';
    thousands: string;
    decimal: string;
}

/** Known currencies: symbol, its side and the separators. */
export const CURRENCIES: Readonly<Record<string, CurrencyFormat>> = {
    DKK: { symbol: 'kr.', position: 'after', thousands: '.', decimal: ',' },
    EUR: { symbol: '€', position: 'before', thousands: '.', decimal: ',' },
    USD: { symbol: '$', position: 'before', thousands: ',', decimal: '.' },
    GBP: { symbol: '£', position: 'before', thousands: ',', decimal: '.' },
    SEK: { symbol: 'kr', position: 'after', thousands: ' ', decimal: ',' },
    NOK: { symbol: 'kr', position: 'after', thousands: ' ', decimal: ',' },
};

export interface MoneyOptions {
    currency?: string | null;
    decimals?: number | string | null;
    symbol?: string | null;
    position?: string | null;
    thousands?: string | null;
    decimal?: string | null;
}

/** The ISO code as the money display prints it in data-currency (ASCII upper case, like PHP's strtoupper). */
export function currencyCode(currency: string | null | undefined): string {
    return String(currency ?? '').replace(/[a-z]/g, (ch) => ch.toUpperCase());
}

/** Moves the decimal point by `places` without binary rounding error. */
function shift(value: number, places: number): number {
    const [mantissa, exponent = '0'] = String(value).split('e');
    return Number(`${mantissa}e${Number(exponent) + places}`);
}

/**
 * PHP's number_format(): rounds half away from zero (after pre-rounding to
 * 15 significant digits, so 0.15 → 0.2 like PHP), groups the integer part.
 */
export function numberFormat(value: number, decimals = 0, decimal = '.', thousands = ','): string {
    const places = Math.max(0, toInt(decimals));
    const abs = Math.abs(Number(value) || 0);
    const pre = Number(abs.toPrecision(15));
    const rounded = Math.round(shift(pre, places));
    const digits = places ? String(rounded).padStart(places + 1, '0') : String(rounded);
    const whole = places ? digits.slice(0, -places) : digits;
    const fraction = places ? digits.slice(-places) : '';
    const grouped = whole.replace(/\B(?=(\d{3})+(?!\d))/g, thousands);
    // A value that rounds to zero is no longer negative (PHP prints "0", never "-0").
    const sign = value < 0 && rounded !== 0 ? '-' : '';
    return sign + grouped + (places ? decimal + fraction : '');
}

/**
 * A minor-unit amount (øre / cents) for display: `1.234,56 kr.`, `$1,234.56`.
 * Known currencies carry symbol, position and separators; every piece can be
 * overridden, and an unknown code is printed as a trailing symbol.
 */
export function formatMoney(minor: number | string | null | undefined, options: MoneyOptions = {}): string {
    const code = currencyCode(options.currency ?? 'DKK');
    const meta = CURRENCIES[code] ?? { symbol: code, position: 'after', thousands: ',', decimal: '.' };
    const symbol = options.symbol ?? meta.symbol;
    const position = options.position ?? meta.position;
    const formatted = numberFormat(Number(minor ?? 0) / 100, Math.max(0, toInt(options.decimals ?? 2)), options.decimal ?? meta.decimal, options.thousands ?? meta.thousands);
    return position === 'before' ? symbol + formatted : `${formatted} ${symbol}`;
}

/* -------- money input ------------------------------------------------------- */

/** PHP's is_numeric() for the values a prop or an input carries. */
function isNumeric(value: unknown): boolean {
    if (typeof value === 'number') return Number.isFinite(value);
    return typeof value === 'string' && /^\s*[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?\s*$/.test(value);
}

/**
 * A minor-unit amount as the money input's text field shows it: plain major
 * units with no grouping and no trailing zeros (145050 → "1450.5", 145000 →
 * "1450"); empty for a missing or non-numeric value. The Blade view computes
 * the same in PHP.
 */
export function majorFromMinor(minor: number | string | null | undefined): string {
    if (!isNumeric(minor)) return '';
    return numberFormat(toInt(minor) / 100, 2, '.', '').replace(/0+$/, '').replace(/\.$/, '');
}

/**
 * What someone typed into the money input, in minor units: a comma counts as
 * the decimal point and the amount rounds to whole minor units ("1450,5" →
 * 145050); null when it does not start with a number.
 */
export function minorFromMajor(text: string | number | null | undefined): number | null {
    const n = parseFloat(String(text ?? '').replace(',', '.'));
    return Number.isFinite(n) ? Math.round(n * 100) : null;
}

/* -------- duration input ---------------------------------------------------- */

/** Whole minutes from a value a duration input carries: null when it holds none, else 0 or more. */
export function durationMinutes(value: number | string | null | undefined): number | null {
    return isNumeric(value) ? Math.max(0, toInt(value)) : null;
}

/**
 * The duration input's tight label: `1h59m`, `2h`, `45m` — or the placeholder
 * while there is no value (missing, or 0 minutes).
 */
export function durationLabel(minutes: number | string | null | undefined, placeholder = '0m'): string {
    const total = durationMinutes(minutes) ?? 0;
    if (total <= 0) return placeholder;
    const h = Math.floor(total / 60);
    const m = total % 60;
    return h === 0 ? `${m}m` : m === 0 ? `${h}h` : `${h}h${m}m`;
}

/** The `HH:MM` draft the duration input edits; empty while there is no value. */
export function durationDraft(minutes: number | string | null | undefined): string {
    const total = durationMinutes(minutes) ?? 0;
    return total > 0 ? formatDuration(total, 'clock') : '';
}

/**
 * Whole minutes from what someone typed into the duration input, forgivingly:
 * `H:MM` or `HH:MM` (either side may be missing), or a bare minute count.
 * Anything else, and negatives, read as 0.
 */
export function parseDuration(raw: string | null | undefined): number {
    const s = (raw ?? '').trim();
    if (s === '') return 0;
    if (s.includes(':')) {
        const [hours, mins] = s.split(':');
        return Math.max(0, (parseInt(hours, 10) || 0) * 60 + (parseInt(mins, 10) || 0));
    }
    const n = parseInt(s, 10);
    return Number.isNaN(n) ? 0 : Math.max(0, n);
}

/* -------- progress ---------------------------------------------------------- */

/** How far a progress bar is filled, 0–100. A max of 0 or less counts as a hair above 0. */
export function progressPercent(value: number | string | null | undefined, max: number | string | null | undefined = 100): number {
    const top = Math.max(Number(max ?? 0) || 0, 1e-9);
    return Math.min(100, Math.max(0, ((Number(value ?? 0) || 0) / top) * 100));
}

/** A number as PHP prints a float (14 significant digits), so both runtimes write the same CSS value. */
export function phpFloat(value: number): string {
    return String(Number(value.toPrecision(14)));
}
