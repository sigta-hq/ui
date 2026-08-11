{{-- SIGTA lockup: glyph + wordmark. currentColor throughout — recolors with text utilities. --}}
@props([
    'name' => 'SIGTA',
    'glyphOnly' => false,
    'size' => 'base',
])

@php
$glyphSize = match ($size) {
    'sm' => 'size-[22px]',
    'lg' => 'size-[38px]',
    default => 'size-[30px]',
};
$nameSize = match ($size) {
    'sm' => 'text-[1.05rem]',
    'lg' => 'text-[1.6rem]',
    default => 'text-[1.3rem]',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-[0.6em] text-ink']) }} data-sigta="brand">
    <svg class="{{ $glyphSize }} shrink-0" viewBox="0 0 40 40" fill="none" aria-hidden="true">
        <rect x="3" y="3" width="34" height="34" rx="6" stroke="currentColor" stroke-width="2.2"/>
        <path d="M20 12 L11 29 M20 12 L29 29" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
        <path d="M13.5 26 A 13 13 0 0 0 26.5 26" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
        <circle cx="20" cy="12" r="2.6" fill="currentColor"/>
    </svg>
    @unless ($glyphOnly)
        <span class="font-head font-semibold {{ $nameSize }} tracking-[0.02em]">{{ $name }}</span>
    @endunless
</span>
