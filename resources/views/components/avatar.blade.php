@props([
    'src' => null,
    'name' => null,
    'alt' => null,
    'shape' => 'circle',
    'size' => 'base',
    'tone' => 'ink',
])

@php
$classes = 'inline-flex items-center justify-center shrink-0 select-none overflow-hidden';

$classes .= ' '.match ($size) {
    'xs' => 'size-6 text-[0.55rem]',
    'sm' => 'size-8 text-[0.62rem]',
    'lg' => 'size-14 text-[0.9rem]',
    default => 'size-10 text-[0.72rem]',
};

$classes .= ' '.($shape === 'square' ? 'rounded-base' : 'rounded-full');

$initials = collect(preg_split('/\s+/', trim($name ?? '')) ?: [])
    ->filter()
    ->take(2)
    ->map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)))
    ->implode('');
@endphp

@if ($src)
    <img data-sigta="avatar" {{ $attributes->merge([
        'src' => $src,
        'alt' => $alt ?? $name ?? '',
        'class' => $classes.' object-cover',
    ]) }}>
@else
    <span data-sigta="avatar"
        @unless ($attributes->has('aria-hidden'))
            @if ($alt ?? $name) role="img" aria-label="{{ $alt ?? $name }}" @else aria-hidden="true" @endif
        @endunless
        {{ $attributes->merge(['class' => $classes.' font-mono '.($tone === 'line' ? 'bg-line text-ink' : 'bg-ink text-surface')]) }}>{{ $initials }}</span>
@endif
