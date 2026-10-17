/* ---------------------------------------------------------------------------
   Hand-drawn charts — the geometry of the sparkline, the line chart and the
   bar chart. Points are joined Catmull-Rom style (as cubic Béziers), so the
   pen keeps moving between them and never makes a hard elbow.

   The Blade views compute the same geometry in PHP; this is a faithful port,
   down to how PHP rounds (round() with its edge-case correction) and how it
   prints a float, so both runtimes emit byte-identical paths. Pure and
   deterministic: no DOM, no randomness.
--------------------------------------------------------------------------- */

export type Point = [number, number];

/** PHP's round($value, $places) as of PHP 8.4: half away from zero, with
    PHP's correction for values a hair below the edge (round(1.005, 2) = 1.01). */
export function phpRound(value: number, places = 0): number {
    if (!Number.isFinite(value) || value === 0) return value;
    const exponent = 10 ** Math.abs(places);
    const scaled = places > 0 ? value * exponent : value / exponent;
    let integral = value >= 0 ? Math.floor(scaled) : Math.ceil(scaled);
    const next = value >= 0 ? integral + 1 : integral - 1;
    if ((places > 0 ? next / exponent : next * exponent) === value) integral = next;
    if (Math.abs(integral) >= 1e16) return value;
    const sign = Object.is(integral, -0) || integral < 0 ? -1 : 1;
    const edge = Math.abs(places > 0 ? (integral + sign * 0.5) / exponent : (integral + sign * 0.5) * exponent);
    if (Math.abs(value) >= edge) integral += sign;
    return places > 0 ? integral / exponent : integral * exponent;
}

/** A float the way PHP prints it (precision 14): 4.0 → "4", -0.0 → "-0",
    0.00001 → "1.0E-5". */
export function phpNumber(value: number): string {
    if (Number.isNaN(value)) return 'NAN';
    if (!Number.isFinite(value)) return value < 0 ? '-INF' : 'INF';
    if (value === 0) return Object.is(value, -0) ? '-0' : '0';
    const sign = value < 0 ? '-' : '';
    // 14 significant digits. JavaScript breaks an exact tie upwards, PHP to
    // the even digit, so a tie on an even digit keeps the digits as they are.
    let [mantissa, exp] = Math.abs(value).toExponential(13).split('e');
    const [exactMantissa, exactExp] = Math.abs(value).toExponential(100).split('e');
    const exact = exactMantissa.replace('.', '');
    if (/^50*$/.test(exact.slice(14)) && Number(exact[13]) % 2 === 0) [mantissa, exp] = [exact.slice(0, 14), exactExp];
    const digits = mantissa.replace('.', '').replace(/0+$/, '') || '0';
    // The decimal point sits `decpt` digits into `digits` (0.d1d2… × 10^decpt).
    const decpt = Number(exp) + 1;
    if (decpt < 0 ? decpt < -3 : decpt > 14) {
        const power = decpt - 1;
        return `${sign}${digits[0]}.${digits.slice(1) || '0'}E${power < 0 ? '-' : '+'}${Math.abs(power)}`;
    }
    if (decpt <= 0) return `${sign}0.${'0'.repeat(-decpt)}${digits}`;
    if (digits.length <= decpt) return sign + digits + '0'.repeat(decpt - digits.length);
    return `${sign}${digits.slice(0, decpt)}.${digits.slice(decpt)}`;
}

/** PHP's floatval for one value. */
function floatval(value: unknown): number {
    if (typeof value === 'number') return value;
    if (typeof value === 'boolean') return value ? 1 : 0;
    if (typeof value === 'string') {
        const match = value.match(/^\s*[+-]?(\d+\.?\d*|\.\d+)([eE][+-]?\d+)?/);
        return match ? parseFloat(match[0]) : 0;
    }
    return 0;
}

/** The values a chart plots: PHP's array_values(array_map('floatval', (array) $values)). */
export function chartValues(values: unknown): number[] {
    if (values === null || values === undefined) return [];
    const list = Array.isArray(values) ? values : typeof values === 'object' ? Object.values(values as object) : [values];
    return list.map(floatval);
}

/** PHP's (int) cast. */
function intval(value: unknown): number {
    const n = floatval(value);
    return Number.isFinite(n) ? Math.trunc(n) : 0;
}

/** PHP's is_numeric. */
function isNumeric(value: unknown): boolean {
    if (typeof value === 'number') return Number.isFinite(value);
    return typeof value === 'string' && /^\s*[+-]?(\d+\.?\d*|\.\d+)([eE][+-]?\d+)?\s*$/.test(value);
}

/** A smooth path through the points (Catmull-Rom → cubic Bézier); '' below two points. */
export function smoothPath(points: Point[]): string {
    const n = points.length;
    if (n < 2) return '';
    let path = `M${phpNumber(points[0][0])} ${phpNumber(points[0][1])}`;
    for (let i = 0; i < n - 1; i++) {
        const p0 = points[Math.max(i - 1, 0)];
        const p1 = points[i];
        const p2 = points[i + 1];
        const p3 = points[Math.min(i + 2, n - 1)];
        path += ` C${[
            phpRound(p1[0] + (p2[0] - p0[0]) / 6, 2),
            phpRound(p1[1] + (p2[1] - p0[1]) / 6, 2),
        ].map(phpNumber).join(' ')}, ${[
            phpRound(p2[0] - (p3[0] - p1[0]) / 6, 2),
            phpRound(p2[1] - (p3[1] - p1[1]) / 6, 2),
        ].map(phpNumber).join(' ')}, ${phpNumber(p2[0])} ${phpNumber(p2[1])}`;
    }
    return path;
}

const extent = (values: number[]): [number, number] => (values.length ? [Math.min(...values), Math.max(...values)] : [0, 0]);

/** A point as the strings both runtimes print. */
export interface ChartPoint {
    x: string;
    y: string;
}

const printed = (point: Point): ChartPoint => ({ x: phpNumber(point[0]), y: phpNumber(point[1]) });

export interface SparklineGeometry {
    width: number;
    height: number;
    /** The line, '' with fewer than two values. */
    path: string;
    /** The last value, where the dot sits; null without values. */
    end: ChartPoint | null;
}

/** The sparkline: a 200×40 viewBox with 4 units of padding. */
export function sparkline(input: unknown): SparklineGeometry {
    const values = chartValues(input);
    const w = 200;
    const h = 40;
    const pad = 4;
    const n = values.length;
    const [min, max] = extent(values);
    const range = max - min;
    const points: Point[] = values.map((v, i) => [
        phpRound(n > 1 ? pad + (w - 2 * pad) * i / (n - 1) : w / 2, 2),
        phpRound(range > 0 ? (h - pad) - (h - 2 * pad) * (v - min) / range : h / 2, 2),
    ]);
    return { width: w, height: h, path: smoothPath(points), end: n ? printed(points[n - 1]) : null };
}

export interface LineGeometry {
    width: number;
    height: number;
    /** The y of the three gridlines (a quarter, half and three quarters down). */
    grid: [string, string, string];
    /** The line, '' with fewer than two values. */
    line: string;
    /** The line closed down to the baseline, for the fill. */
    area: string;
    /** The latest value, highlighted; null without values. */
    end: ChartPoint | null;
}

/** The line chart: a 720-wide viewBox, `height` tall (at least 40). */
export function lineChart(input: unknown, height: unknown = 200): LineGeometry {
    const values = chartValues(input);
    const w = 720;
    const h = Math.max(intval(height), 40);
    const top = 12;
    const bottom = 14;
    const n = values.length;
    const [min, max] = extent(values);
    const range = max - min;
    const points: Point[] = values.map((v, i) => [
        phpRound(n > 1 ? w * i / (n - 1) : w / 2, 2),
        phpRound(range > 0 ? (h - bottom) - (h - top - bottom) * (v - min) / range : h / 2, 2),
    ]);
    const line = smoothPath(points);
    const area = line !== ''
        ? `${line} L${phpNumber(points[n - 1][0])} ${h} L${phpNumber(points[0][0])} ${h} Z`
        : '';
    return {
        width: w,
        height: h,
        grid: [phpNumber(phpRound(h * 0.25, 1)), phpNumber(phpRound(h * 0.5, 1)), phpNumber(phpRound(h * 0.75, 1))],
        line,
        area,
        end: n ? printed(points[n - 1]) : null,
    };
}

export interface Bar extends ChartPoint {
    width: string;
    height: string;
    /** Every bar but the highlighted one is drawn a touch lighter. */
    dimmed: boolean;
}

export interface BarsGeometry {
    width: number;
    height: number;
    bars: Bar[];
    /** The highlighter swipe behind the highlighted bar; null without one. */
    swipe: { x: string; y: string; width: string; height: string } | null;
    /** The dashed trend line across the bar tops; '' without one. */
    trend: string;
}

export interface BarsOptions {
    height?: unknown;
    /** Index of the bar to highlight. */
    highlight?: unknown;
    trend?: unknown;
}

/** The bar chart: a 720-wide viewBox, `height` tall (at least 40). */
export function barChart(input: unknown, { height = 160, highlight = null, trend = false }: BarsOptions = {}): BarsGeometry {
    const values = chartValues(input);
    const w = 720;
    const h = Math.max(intval(height), 40);
    const top = 10;
    const n = values.length;
    const base = n ? Math.min(0, Math.min(...values)) : 0;
    const max = n ? Math.max(...values) : 0;
    const range = max - base;

    const bars = values.map((v, i) => {
        const slot = w / n;
        const barWidth = phpRound(Math.min(slot * 0.55, 56), 2);
        const barHeight = phpRound(range > 0 ? (h - top) * (v - base) / range : 0, 2);
        return {
            x: phpRound(slot * (i + 0.5) - barWidth / 2, 2),
            y: phpRound(h - barHeight, 2),
            w: barWidth,
            h: barHeight,
            cx: phpRound(slot * (i + 0.5), 2),
        };
    });

    const index = isNumeric(highlight) ? intval(highlight) : null;
    const lit = index !== null ? bars[index] ?? null : null;
    let swipe: BarsGeometry['swipe'] = null;
    if (lit) {
        const y = phpRound(Math.max(lit.y - 10, 2), 2);
        swipe = {
            x: phpNumber(phpRound(lit.cx - lit.w * 0.8, 2)),
            y: phpNumber(y),
            width: phpNumber(phpRound(lit.w * 1.6, 2)),
            height: phpNumber(phpRound(Math.max((h - y) * 0.62, 0), 2)),
        };
    }

    return {
        width: w,
        height: h,
        bars: bars.map((bar, i) => ({
            x: phpNumber(bar.x),
            y: phpNumber(bar.y),
            width: phpNumber(bar.w),
            height: phpNumber(bar.h),
            dimmed: index === null || i !== index,
        })),
        swipe,
        trend: trend && n > 1 ? smoothPath(bars.map((bar): Point => [bar.cx, Math.max(bar.y - 6, 4)])) : '',
    };
}
