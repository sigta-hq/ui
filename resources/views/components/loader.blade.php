{{-- The Sigta compass: the brand mark as a loader, in use. While it works the
     handle turns, the legs swing round each other and the pencil draws a
     circle on the paper, then lifts it again. With a `value` (0–100) the
     circle is drawn as far round as the work has come. When the work is done
     the mark stands complete with its arc and gives a small stamp; failed
     shakes it red. With the Sigta script on the page a finished loader first
     closes its circle and raises it into the arc. State lives in data
     attributes, so a Livewire morph or an Alpine binding drives it.

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
$size = in_array($size, ['xs', 'sm', 'base', 'lg', 'xl'], true) ? $size : 'base';

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

$rootAttributes = [
    'data-state' => $state,
    'data-variant' => $variant,
    'data-size' => $size,
];

if ($percent !== null) {
    $rootAttributes['data-progress'] = '';
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

{{-- Drawn back to front: the far half of the paper circle, the legs (straight
     down from the hinge; CSS turns them onto their feet), the near half, the
     mark's arc, the hinge. The frame is one path from top centre so the
     sketch variant can draw it. --}}
<span data-sigta="loader" {{ $attributes->merge($rootAttributes) }}>
    <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path class="sigta-loader-frame" pathLength="100" d="M20 3H31A6 6 0 0 1 37 9V31A6 6 0 0 1 31 37H9A6 6 0 0 1 3 31V9A6 6 0 0 1 9 3Z" />
        <path class="sigta-loader-ring sigta-loader-ring-back" pathLength="100" d="M11 29A9 2.8 0 0 1 29 29" />
        <path class="sigta-loader-leg sigta-loader-needle" pathLength="100" d="M20 12V29" />
        <path class="sigta-loader-leg sigta-loader-pencil" pathLength="100" d="M20 12V29" />
        <path class="sigta-loader-ring sigta-loader-ring-front" pathLength="100" d="M29 29A9 2.8 0 0 1 11 29" />
        <path class="sigta-loader-arc" pathLength="100" d="M12.75 25.7A15.5 15.5 0 0 0 27.25 25.7" />
        <circle class="sigta-loader-pivot" cx="20" cy="12" r="2.6" fill="currentColor" stroke="none" />
    </svg>
</span>
