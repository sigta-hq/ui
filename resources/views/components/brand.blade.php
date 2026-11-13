{{-- Brand lockup: your logo + wordmark, sized as one unit. Pass the logo as
     the `logo` slot (inline SVG using currentColor recolors with text
     utilities) or as an image URL via the `logo` prop. Ships no logo.

       <sigta:brand name="Acme"><x-slot:logo><svg viewBox="0 0 40 40">…</svg></x-slot:logo></sigta:brand>
       <sigta:brand name="Acme" logo="/images/logo.svg" size="sm" /> --}}
@props([
    'name' => null,
    'logo' => null,
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
    @if ($logo instanceof \Illuminate\View\ComponentSlot)
        <span class="{{ $glyphSize }} shrink-0 [&>svg]:size-full" aria-hidden="true">{{ $logo }}</span>
    @elseif ($logo)
        <img src="{{ $logo }}" alt="" class="{{ $glyphSize }} shrink-0 object-contain">
    @endif
    @if ($name && ! $glyphOnly)
        <span class="font-head font-semibold {{ $nameSize }} tracking-[0.02em]">{{ $name }}</span>
    @endif
</span>
