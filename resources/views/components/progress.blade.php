{{-- Determinate or indeterminate progress bar. Attributes forward onto the
     progressbar element itself; `label` adds a mono header row with the percentage.
     The fill width is --sigta-progress (0–100), the same value the Svelte twin
     computes with progressPercent() in packages/core/src/format.ts. --}}
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

$trackAttributes = [
    'id' => $id,
    'role' => 'progressbar',
    'aria-valuemin' => '0',
    'aria-valuemax' => (string) $max,
];

if ($indeterminate) {
    $trackAttributes['data-indeterminate'] = '';
} else {
    $trackAttributes['aria-valuenow'] = (string) $value;
}

if ($label) {
    $trackAttributes['aria-labelledby'] = $id.'-label';
}
@endphp

@if ($label)
    <div data-scope="progress" data-part="field">
        <div data-scope="progress" data-part="header">
            <span id="{{ $id }}-label" data-scope="progress" data-part="label">{{ $label }}</span>
            @unless ($indeterminate)
                <span data-scope="progress" data-part="value">{{ round($percent) }}%</span>
            @endunless
        </div>
        <div data-sigta="progress" {{ $attributes->merge($trackAttributes) }}>
            @if ($indeterminate)
                <div data-scope="progress" data-part="fill"></div>
            @else
                <div data-scope="progress" data-part="fill" style="--sigta-progress: {{ $percent }}"></div>
            @endif
        </div>
    </div>
@else
    <div data-sigta="progress" {{ $attributes->merge($trackAttributes) }}>
        @if ($indeterminate)
            <div data-scope="progress" data-part="fill"></div>
        @else
            <div data-scope="progress" data-part="fill" style="--sigta-progress: {{ $percent }}"></div>
        @endif
    </div>
@endif
