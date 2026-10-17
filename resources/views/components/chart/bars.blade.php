{{-- Bar chart (brand hero language): ink bars, optional highlighter swipe
     behind one bar (`highlight` index) and an optional dashed hand-drawn
     trend line across the bar tops (`:trend="true"`).
     Usage: <x-sigta::chart.bars :values="[…]" :labels="[…]" :highlight="4" trend />
     The geometry is mirrored in @sigta-hq/core (chart.ts) for the Svelte
     runtime: change both together, the conformance check compares them. --}}
@props([
    'values' => [],
    'labels' => null,
    'highlight' => null,
    'trend' => false,
    'height' => 160,
    'label' => null,
])

@php
$values = array_values(array_map('floatval', (array) $values));
$w = 720;
$h = max((int) $height, 40);
$top = 10;
$n = count($values);

$base = $n ? min(0.0, min($values)) : 0.0;
$max = $n ? max($values) : 0.0;
$range = $max - $base;

$bars = [];
foreach ($values as $i => $v) {
    $slot = $w / $n;
    $barWidth = round(min($slot * 0.55, 56), 2);
    $barHeight = round($range > 0 ? ($h - $top) * ($v - $base) / $range : 0, 2);
    $bars[] = [
        'x' => round($slot * ($i + 0.5) - $barWidth / 2, 2),
        'y' => round($h - $barHeight, 2),
        'w' => $barWidth,
        'h' => $barHeight,
        'cx' => round($slot * ($i + 0.5), 2),
    ];
}

$highlight = is_numeric($highlight) ? (int) $highlight : null;
$highlighted = ($highlight !== null && isset($bars[$highlight])) ? $bars[$highlight] : null;

// Dashed trend line across the bar tops — same Catmull-Rom smoothing.
$trendPath = '';
if ($trend && $n > 1) {
    $tops = array_map(fn ($b) => [$b['cx'], max($b['y'] - 6, 4)], $bars);
    $trendPath = 'M'.$tops[0][0].' '.$tops[0][1];
    for ($i = 0; $i < $n - 1; $i++) {
        $p0 = $tops[max($i - 1, 0)];
        $p1 = $tops[$i];
        $p2 = $tops[$i + 1];
        $p3 = $tops[min($i + 2, $n - 1)];
        $trendPath .= sprintf(
            ' C%s %s, %s %s, %s %s',
            round($p1[0] + ($p2[0] - $p0[0]) / 6, 2),
            round($p1[1] + ($p2[1] - $p0[1]) / 6, 2),
            round($p2[0] - ($p3[0] - $p1[0]) / 6, 2),
            round($p2[1] - ($p3[1] - $p1[1]) / 6, 2),
            $p2[0],
            $p2[1],
        );
    }
}
@endphp

<div data-sigta="chart.bars" {{ $attributes }}>
    <svg
        viewBox="0 0 {{ $w }} {{ $h }}"
        preserveAspectRatio="none"
        xmlns="http://www.w3.org/2000/svg"
        @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" @endif
        data-scope="chart.bars"
        data-part="plot"
    >
        @if ($highlighted)
            @php
            $accentY = round(max($highlighted['y'] - 10, 2), 2);
            $accentHeight = round(max(($h - $accentY) * 0.62, 0), 2);
            @endphp
            <rect
                x="{{ round($highlighted['cx'] - $highlighted['w'] * 0.8, 2) }}"
                y="{{ $accentY }}"
                width="{{ round($highlighted['w'] * 1.6, 2) }}"
                height="{{ $accentHeight }}"
                rx="2"
                fill="var(--sigta-accent)"
                opacity=".85"
            />
        @endif

        @foreach ($bars as $i => $bar)
            <rect
                x="{{ $bar['x'] }}"
                y="{{ $bar['y'] }}"
                width="{{ $bar['w'] }}"
                height="{{ $bar['h'] }}"
                rx="2"
                fill="currentColor"
                @if ($highlight === null || $i !== $highlight) opacity=".88" @endif
            />
        @endforeach

        @if ($trendPath !== '')
            <path d="{{ $trendPath }}" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="2 6" opacity=".7" vector-effect="non-scaling-stroke" />
        @endif
    </svg>

    @if ($labels && $n > 0)
        <div data-scope="chart.bars" data-part="labels" style="--sigta-chart-columns: {{ $n }}" aria-hidden="true">
            @foreach ($labels as $tick)
                <span>{{ $tick }}</span>
            @endforeach
        </div>
    @endif
</div>
