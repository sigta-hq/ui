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

$classes = 'font-head text-balance '.match ($size) {
    'display' => 'text-[clamp(2.7rem,7vw,5.1rem)] font-normal tracking-[-0.02em] leading-[1.04]',
    'xl' => 'text-[clamp(1.9rem,3.6vw,2.9rem)] font-normal tracking-[-0.012em] leading-[1.04]',
    'lg' => 'text-[clamp(1.5rem,2.4vw,1.9rem)] font-medium leading-[1.1]',
    'sm' => 'text-[1.05rem] font-medium leading-[1.2]',
    default => 'text-[1.28rem] font-medium leading-[1.15]',
};

$tag = 'h'.min(max((int) $level, 1), 6);
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</{{ $tag }}>
