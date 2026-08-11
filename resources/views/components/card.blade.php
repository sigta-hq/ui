@props([
    'hover' => true,
])

@php
$classes = 'flex flex-col bg-surface-raised border border-line rounded-panel overflow-hidden';

if ($hover) {
    $classes .= ' transition duration-200 ease-out hover:-translate-y-[3px] hover:shadow-paper';
}
@endphp

<div data-sigta="card" {{ $attributes->merge(['class' => $classes]) }}>
    @isset($thumb)
        <div {{ $thumb->attributes->merge(['class' => 'relative aspect-video bg-surface border-b border-line overflow-hidden']) }}>{{ $thumb }}</div>
    @endisset
    <div class="p-[clamp(18px,2vw,26px)]">{{ $slot }}</div>
</div>
