@props([
    'level' => 2,
    'size' => null,
])

@php
$size ??= match ((int) $level) {
    1 => 'xl',
    2 => 'lg',
    3 => 'base',
    default => 'sm',
};

$tag = 'h'.min(max((int) $level, 1), 6);
@endphp

<{{ $tag }} data-sigta="heading" {{ $attributes->merge(['data-size' => $size]) }}>{{ $slot }}</{{ $tag }}>
