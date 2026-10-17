@props([
    'src' => null,
    'name' => null,
    'alt' => null,
    'shape' => 'circle',
    'size' => 'base',
    'tone' => 'ink',
])

@php
$contract = ['data-size' => $size, 'data-shape' => $shape, 'data-tone' => $tone];

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
        ...$contract,
    ]) }}>
@else
    <span data-sigta="avatar"
        @unless ($attributes->has('aria-hidden'))
            @if ($alt ?? $name) role="img" aria-label="{{ $alt ?? $name }}" @else aria-hidden="true" @endif
        @endunless
        {{ $attributes->merge($contract) }}>{{ $initials }}</span>
@endif
