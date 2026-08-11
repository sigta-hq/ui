@props([
    'name',
    'size' => 'base',
])

@php
$sizeClass = match ($size) {
    'xs' => 'size-3.5',
    'sm' => 'size-4',
    'lg' => 'size-6',
    default => 'size-5',
};

$classes = trim($sizeClass.' '.$attributes->get('class'));

$svgAttributes = array_merge(
    ['stroke-width' => '1.75', 'aria-hidden' => 'true'],
    $attributes->except('class')->getAttributes(),
);
@endphp

{{ svg('lucide-'.$name, $classes, $svgAttributes) }}
