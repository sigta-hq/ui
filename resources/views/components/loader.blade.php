{{-- The Sigta compass — a loader drawn from the brand mark. While working, the
     pencil leg swings and the arc follows its tip; done completes the mark with
     a small stamp; failed shakes it in danger. A `value` (0–100) turns the frame
     into the progress. State lives in data attributes, so a Livewire morph or an
     Alpine binding drives it without a script of its own.

       <sigta:loader />
       <sigta:loader size="lg" :value="$import->progress" :state="$import->state" />

     `decorative` hides it from assistive tech when a parent already announces
     the work (the save state, a busy button). --}}
@props([
    'size' => 'base',
    'variant' => 'compass',
    'state' => 'working',
    'value' => null,
    'label' => null,
    'decorative' => false,
])

@php
$box = match ($size) {
    'xs' => 'size-4',
    'sm' => 'size-5',
    'lg' => 'size-11',
    'xl' => 'size-[4.5rem]',
    default => 'size-7',
};

// Hairlines vanish at icon sizes, so the small sizes draw a heavier pen.
[$frameWidth, $penWidth] = match ($size) {
    'xs' => [3, 3.3],
    'sm' => [2.6, 2.9],
    default => [2.2, 2.4],
};

$state = in_array($state, ['idle', 'queued', 'working', 'done', 'failed'], true) ? $state : 'working';
$variant = $variant === 'sketch' ? 'sketch' : 'compass';

$percent = $value === null ? null : min(100, max(0, (float) $value));
if ($percent !== null && $state === 'done') {
    $percent = 100;
}

$label ??= match ($state) {
    'queued' => 'Queued',
    'done' => 'Done',
    'failed' => 'Failed',
    'idle' => 'Ready',
    default => 'Working',
};

// The glyph's rounded frame as one path from top centre, clockwise, so it can
// be drawn as progress.
$frame = 'M20 3H31A6 6 0 0 1 37 9V31A6 6 0 0 1 31 37H9A6 6 0 0 1 3 31V9A6 6 0 0 1 9 3Z';

$rootAttributes = [
    'class' => 'inline-block shrink-0 align-middle '.$box,
    'data-state' => $state,
    'data-variant' => $variant,
];

if ($percent !== null) {
    $rootAttributes['style'] = '--sigta-progress: '.round($percent, 2);
}

if ($decorative) {
    $rootAttributes['aria-hidden'] = 'true';
} else {
    $rootAttributes['role'] = 'progressbar';
    $rootAttributes['aria-label'] = $label;

    if ($percent !== null) {
        $rootAttributes['aria-valuemin'] = '0';
        $rootAttributes['aria-valuemax'] = '100';
        $rootAttributes['aria-valuenow'] = (string) round($percent);
    }
}
@endphp

<span data-sigta="loader" {{ $attributes->merge($rootAttributes) }}>
    <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-linecap="round" aria-hidden="true">
        @if ($percent !== null)
            <path class="sigta-loader-track" d="{{ $frame }}" stroke-width="{{ $frameWidth }}" />
            <path class="sigta-loader-progress" pathLength="100" d="{{ $frame }}" stroke-width="{{ $frameWidth }}" />
        @else
            <path class="sigta-loader-frame" pathLength="100" d="{{ $frame }}" stroke-width="{{ $frameWidth }}" />
        @endif
        <path class="sigta-loader-leg" pathLength="100" d="M20 12L11 29" stroke-width="{{ $penWidth }}" />
        <path class="sigta-loader-leg sigta-loader-pencil" pathLength="100" d="M20 12L29 29" stroke-width="{{ $penWidth }}" />
        <path class="sigta-loader-arc" pathLength="100" d="M12.75 25.7A15.5 15.5 0 0 0 27.25 25.7" stroke-width="{{ $penWidth }}" />
        <circle class="sigta-loader-pivot" cx="20" cy="12" r="2.6" fill="currentColor" stroke="none" />
    </svg>
</span>
