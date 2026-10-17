// The chart geometry: every expectation is what the Blade views print in
// PHP, so the Svelte charts draw byte-identical paths.
import { describe, expect, it } from 'vitest';
import { barChart, chartValues, lineChart, phpNumber, phpRound, smoothPath, sparkline } from '../src/chart.ts';

describe('phpRound', () => {
    it('rounds half away from zero', () => {
        expect(phpRound(2.5)).toBe(3);
        expect(phpRound(-0.5)).toBe(-1);
        expect(phpRound(-2.5, 0)).toBe(-3);
        expect(phpRound(62.4)).toBe(62);
    });

    it('corrects values a hair below the edge, as PHP does', () => {
        expect(phpRound(1.005, 2)).toBe(1.01);
        expect(phpRound(0.285, 2)).toBe(0.29);
        expect(phpRound(1.955, 2)).toBe(1.96);
        expect(phpRound(2.675, 2)).toBe(2.68);
    });

    it('keeps the sign of a value that rounds to zero', () => {
        expect(Object.is(phpRound(-0.001, 2), -0)).toBe(true);
        expect(Object.is(phpRound(0.001, 2), 0)).toBe(true);
    });
});

describe('phpNumber', () => {
    it('prints floats with PHP\'s precision of 14', () => {
        expect(phpNumber(4)).toBe('4');
        expect(phpNumber(0.1 + 0.2)).toBe('0.3');
        expect(phpNumber(123456.789)).toBe('123456.789');
        expect(phpNumber(23.439999999999998)).toBe('23.44');
        expect(phpNumber(-0)).toBe('-0');
        expect(phpNumber(0.0001)).toBe('0.0001');
    });

    it('switches to exponent notation where PHP does', () => {
        expect(phpNumber(0.00001)).toBe('1.0E-5');
        expect(phpNumber(1e14)).toBe('1.0E+14');
        expect(phpNumber(1e13)).toBe('10000000000000');
    });

    it('breaks a tie on the last digit to even, as PHP does', () => {
        expect(phpNumber(2.87994384765625)).toBe('2.8799438476562');
        expect(phpNumber(-4.21173095703125)).toBe('-4.2117309570312');
    });
});

describe('chartValues', () => {
    it('reads values like PHP\'s floatval over (array)', () => {
        expect(chartValues([1, '2.5', '3 apples', 'x', null, true])).toEqual([1, 2.5, 3, 0, 0, 1]);
        expect(chartValues(null)).toEqual([]);
        expect(chartValues(7)).toEqual([7]);
        expect(chartValues({ a: 1, b: 2 })).toEqual([1, 2]);
    });
});

describe('smoothPath', () => {
    it('needs two points', () => {
        expect(smoothPath([])).toBe('');
        expect(smoothPath([[1, 2]])).toBe('');
        expect(smoothPath([[0, 0], [10, 10]])).toBe('M0 0 C1.67 1.67, 8.33 8.33, 10 10');
    });
});

describe('sparkline', () => {
    it('draws the kit sparkline', () => {
        const chart = sparkline([12, 14, 13, 17, 16, 20, 19, 24, 23, 28]);
        expect(chart.path).toBe('M4 36 C7.56 35.33, 18.22 32.33, 25.33 32 C32.44 31.67, 39.56 35, 46.67 34 C53.78 33, 60.89 27, 68 26 C75.11 25, 82.22 29, 89.33 28 C96.44 27, 103.56 21, 110.67 20 C117.78 19, 124.89 23.33, 132 22 C139.11 20.67, 146.22 13.33, 153.33 12 C160.44 10.67, 167.56 15.33, 174.67 14 C181.78 12.67, 192.45 5.67, 196 4');
        expect(chart.end).toEqual({ x: '196', y: '4' });
        expect(sparkline([3.2, 3.6, 3.1, 3.5]).path).toBe('M4 29.6 C14.67 25.33, 46.67 2.93, 68 4 C89.33 5.07, 110.67 34.93, 132 36 C153.33 37.07, 185.33 14.67, 196 10.4');
    });

    it('survives degenerate data', () => {
        expect(sparkline([])).toEqual({ width: 200, height: 40, path: '', end: null });
        expect(sparkline([42])).toEqual({ width: 200, height: 40, path: '', end: { x: '100', y: '20' } });
        expect(sparkline([5, 5, 5, 5, 5]).path).toBe('M4 20 C12 20, 36 20, 52 20 C68 20, 84 20, 100 20 C116 20, 132 20, 148 20 C164 20, 188 20, 196 20');
    });
});

describe('lineChart', () => {
    it('draws line, area, gridlines and the latest point', () => {
        const chart = lineChart([9, 7, 8, 6, 7, 5, 6, 4], 120);
        const line = 'M0 12 C17.14 18.27, 68.57 46.47, 102.86 49.6 C137.15 52.73, 171.43 27.67, 205.71 30.8 C240 33.93, 274.28 65.27, 308.57 68.4 C342.86 71.53, 377.14 46.47, 411.43 49.6 C445.72 52.73, 480.01 84.07, 514.29 87.2 C548.57 90.33, 582.86 65.27, 617.14 68.4 C651.43 71.53, 702.86 99.73, 720 106';
        expect(chart.width).toBe(720);
        expect(chart.height).toBe(120);
        expect(chart.grid).toEqual(['30', '60', '90']);
        expect(chart.line).toBe(line);
        expect(chart.area).toBe(`${line} L720 120 L0 120 Z`);
        expect(chart.end).toEqual({ x: '720', y: '106' });
    });

    it('keeps a single point centred and a minimum height', () => {
        expect(lineChart([7], 120)).toMatchObject({ line: '', area: '', end: { x: '360', y: '60' } });
        expect(lineChart([], 10)).toMatchObject({ height: 40, line: '', end: null });
        expect(lineChart([1, 2], '150').height).toBe(150);
    });
});

describe('barChart', () => {
    it('draws bars, the highlighter swipe and the dashed trend', () => {
        const chart = barChart([54, 80, 72, 110, 96, 132], { highlight: 5, trend: true });
        expect(chart.height).toBe(160);
        expect(chart.swipe).toEqual({ x: '615.2', y: '2', width: '89.6', height: '97.96' });
        expect(chart.bars[0]).toEqual({ x: '32', y: '98.64', width: '56', height: '61.36', dimmed: true });
        expect(chart.bars[5]).toEqual({ x: '632', y: '10', width: '56', height: '150', dimmed: false });
        expect(chart.trend).toBe('M60 92.64 C80 87.72, 140 66.5, 180 63.09 C220 59.68, 260 77.86, 300 72.18 C340 66.5, 380 33.55, 420 29 C460 24.46, 500 49.08, 540 44.91 C580 40.74, 640 10.82, 660 4');
    });

    it('draws plain bars without a highlight or trend', () => {
        const chart = barChart([18, 26, 22, 31], { height: 120 });
        expect(chart.swipe).toBeNull();
        expect(chart.trend).toBe('');
        expect(chart.bars.map((bar) => [bar.x, bar.y, bar.height, bar.dimmed])).toEqual([
            ['62', '56.13', '63.87', true],
            ['242', '27.74', '92.26', true],
            ['422', '41.94', '78.06', true],
            ['602', '10', '110', true],
        ]);
    });

    it('starts negative data from its lowest value and reads a numeric highlight', () => {
        const chart = barChart([-4, 6, 2], { height: 10, highlight: '1', trend: true });
        expect(chart.height).toBe(40);
        expect(chart.swipe).toEqual({ x: '315.2', y: '2', width: '89.6', height: '23.56' });
        expect(chart.bars.map((bar) => bar.height)).toEqual(['0', '30', '18']);
        expect(chart.trend).toBe('M120 34 C160 29, 280 7, 360 4 C440 1, 560 14, 600 16');
    });

    it('ignores a highlight outside the bars', () => {
        const chart = barChart([1, 2, 3], { highlight: 9 });
        expect(chart.swipe).toBeNull();
        expect(chart.bars.every((bar) => bar.dimmed)).toBe(true);
        expect(barChart([], { trend: true })).toEqual({ width: 720, height: 160, bars: [], swipe: null, trend: '' });
    });
});
