@props([
    'hover' => true,
])

@php
// A consumer padding class on the card replaces the body's own, so the two do
// not stack into double the intended inset.
$bodyPadding = preg_match('/(^|\s)([^\s:]+:)*!?p[xytrblse]?-/', (string) $attributes->get('class'))
    ? ''
    : 'p-[clamp(18px,2vw,26px)]';

$classes = 'flex flex-col bg-surface-raised border border-line rounded-panel overflow-hidden';

if ($hover) {
    $classes .= ' transition duration-200 ease-out hover:-translate-y-[3px] hover:shadow-paper';
}
@endphp

<div data-sigta="card" {{ $attributes->merge(['class' => $classes]) }}>
    @isset($thumb)
        <div {{ $thumb->attributes->merge(['class' => 'relative aspect-video bg-surface border-b border-line overflow-hidden']) }}>{{ $thumb }}</div>
    @endisset
    <div class="{{ $bodyPadding }}">{{ $slot }}</div>
</div>
