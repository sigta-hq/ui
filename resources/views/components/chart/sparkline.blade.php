{{-- Sparkline: `:values` → smooth hand-drawn line in a 200×40 viewBox.
     Inherits currentColor — recolor with text utilities on a parent.
     Decorative by default; pass `label` for a meaningful chart.
     The geometry is mirrored in @sigta-hq/core (chart.ts) for the Svelte
     runtime: change both together, the conformance check compares them. --}}
@props([
    'values' => [],
    'label' => null,
])

@php
$values = array_values(array_map('floatval', (array) $values));
$w = 200;
$h = 40;
$pad = 4;
$n = count($values);

$min = $n ? min($values) : 0.0;
$max = $n ? max($values) : 0.0;
$range = $max - $min;

$points = [];
foreach ($values as $i => $v) {
    $points[] = [
        round($n > 1 ? $pad + ($w - 2 * $pad) * $i / ($n - 1) : $w / 2, 2),
        round($range > 0 ? ($h - $pad) - ($h - 2 * $pad) * ($v - $min) / $range : $h / 2, 2),
    ];
}

// Catmull-Rom → cubic bézier: gentle curves between points, never a hard elbow.
$path = '';
if ($n > 1) {
    $path = 'M'.$points[0][0].' '.$points[0][1];
    for ($i = 0; $i < $n - 1; $i++) {
        $p0 = $points[max($i - 1, 0)];
        $p1 = $points[$i];
        $p2 = $points[$i + 1];
        $p3 = $points[min($i + 2, $n - 1)];
        $path .= sprintf(
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

$end = $n ? $points[$n - 1] : null;
@endphp

<svg
    viewBox="0 0 {{ $w }} {{ $h }}"
    preserveAspectRatio="none"
    xmlns="http://www.w3.org/2000/svg"
    @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" @endif
    data-sigta="chart.sparkline"
    {{ $attributes }}
>
    @if ($path !== '')
        <path d="{{ $path }}" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
    @endif
    @if ($end)
        <circle cx="{{ $end[0] }}" cy="{{ $end[1] }}" r="3.5" fill="currentColor" />
    @endif
</svg>
