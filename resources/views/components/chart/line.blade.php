{{-- Line/area chart (brand .chart-wrap language): smooth ink line, faded
     currentColor area, three hairline gridlines, latest point highlighted
     with an accent halo + dashed drop line to the axis.
     Usage: <x-sigta::chart.line :values="[…]" :labels="['W1','W13','W26']" label="…" /> --}}
@props([
    'values' => [],
    'labels' => null,
    'height' => 200,
    'label' => null,
])

@php
$values = array_values(array_map('floatval', (array) $values));
$w = 720;
$h = max((int) $height, 40);
$top = 12;
$bottom = 14;
$n = count($values);

$min = $n ? min($values) : 0.0;
$max = $n ? max($values) : 0.0;
$range = $max - $min;

$points = [];
foreach ($values as $i => $v) {
    $points[] = [
        round($n > 1 ? $w * $i / ($n - 1) : $w / 2, 2),
        round($range > 0 ? ($h - $bottom) - ($h - $top - $bottom) * ($v - $min) / $range : $h / 2, 2),
    ];
}

// Catmull-Rom → cubic bézier: the hand keeps the pen moving between points.
$linePath = '';
if ($n > 1) {
    $linePath = 'M'.$points[0][0].' '.$points[0][1];
    for ($i = 0; $i < $n - 1; $i++) {
        $p0 = $points[max($i - 1, 0)];
        $p1 = $points[$i];
        $p2 = $points[$i + 1];
        $p3 = $points[min($i + 2, $n - 1)];
        $linePath .= sprintf(
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

$areaPath = $linePath !== ''
    ? $linePath.' L'.$points[$n - 1][0].' '.$h.' L'.$points[0][0].' '.$h.' Z'
    : '';

$end = $n ? $points[$n - 1] : null;
$gradientId = 'sigta-chart-'.\Illuminate\Support\Str::random(6);
@endphp

<div data-sigta="chart.line" {{ $attributes->merge(['class' => 'w-full text-ink']) }}>
    <svg
        viewBox="0 0 {{ $w }} {{ $h }}"
        preserveAspectRatio="none"
        xmlns="http://www.w3.org/2000/svg"
        @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" @endif
        class="block w-full h-auto"
    >
        <defs>
            <linearGradient id="{{ $gradientId }}" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="currentColor" stop-opacity=".14" />
                <stop offset="1" stop-color="currentColor" stop-opacity="0" />
            </linearGradient>
        </defs>

        <g stroke="var(--sigta-line)" stroke-width="1">
            <path d="M0 {{ round($h * 0.25, 1) }} H{{ $w }}" />
            <path d="M0 {{ round($h * 0.5, 1) }} H{{ $w }}" />
            <path d="M0 {{ round($h * 0.75, 1) }} H{{ $w }}" />
        </g>

        @if ($areaPath !== '')
            <path d="{{ $areaPath }}" fill="url(#{{ $gradientId }})" />
        @endif
        @if ($linePath !== '')
            <path d="{{ $linePath }}" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
        @endif

        @if ($end)
            <line x1="{{ $end[0] }}" y1="{{ $end[1] }}" x2="{{ $end[0] }}" y2="{{ $h }}" stroke="var(--sigta-line)" stroke-dasharray="3 4" />
            <circle cx="{{ $end[0] }}" cy="{{ $end[1] }}" r="9" fill="var(--sigta-accent)" />
            <circle cx="{{ $end[0] }}" cy="{{ $end[1] }}" r="4" fill="currentColor" />
        @endif
    </svg>

    @if ($labels)
        <div class="flex justify-between gap-2 mt-1.5 font-mono text-[0.62rem] text-graphite" aria-hidden="true">
            @foreach ($labels as $tick)
                <span>{{ $tick }}</span>
            @endforeach
        </div>
    @endif
</div>
