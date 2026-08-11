{{-- Determinate or indeterminate progress bar. Attributes forward onto the
     progressbar element itself; `label` adds a mono header row with the percentage. --}}
@props([
    'value' => 0,
    'max' => 100,
    'label' => null,
    'indeterminate' => false,
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);

$max = max((float) $max, 1e-9);
$percent = min(100, max(0, ((float) $value / $max) * 100));

$track = 'relative h-1.5 w-full overflow-hidden rounded-full bg-line';

$trackAttributes = [
    'id' => $id,
    'class' => $track,
    'role' => 'progressbar',
    'aria-valuemin' => '0',
    'aria-valuemax' => (string) $max,
];

if (! $indeterminate) {
    $trackAttributes['aria-valuenow'] = (string) $value;
}

if ($label) {
    $trackAttributes['aria-labelledby'] = $id.'-label';
}
@endphp

@once
<style>@keyframes sigta-progress-sweep { from { transform: translateX(-100%); } to { transform: translateX(250%); } }</style>
@endonce

@if ($label)
    <div class="flex w-full flex-col gap-[0.45rem]">
        <div class="flex items-baseline justify-between gap-4 font-mono text-[0.68rem] tracking-[0.08em] uppercase">
            <span id="{{ $id }}-label" class="text-graphite">{{ $label }}</span>
            @unless ($indeterminate)
                <span class="text-ink tabular-nums">{{ round($percent) }}%</span>
            @endunless
        </div>
        <div data-sigta="progress" {{ $attributes->merge($trackAttributes) }}>
            @if ($indeterminate)
                <div class="h-full w-2/5 rounded-full bg-ink animate-[sigta-progress-sweep_1.4s_ease-in-out_infinite]"></div>
            @else
                <div class="h-full rounded-full bg-ink transition-[width] duration-300 ease-out" style="width: {{ $percent }}%"></div>
            @endif
        </div>
    </div>
@else
    <div data-sigta="progress" {{ $attributes->merge($trackAttributes) }}>
        @if ($indeterminate)
            <div class="h-full w-2/5 rounded-full bg-ink animate-[sigta-progress-sweep_1.4s_ease-in-out_infinite]"></div>
        @else
            <div class="h-full rounded-full bg-ink transition-[width] duration-300 ease-out" style="width: {{ $percent }}%"></div>
        @endif
    </div>
@endif
