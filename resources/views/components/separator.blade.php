@props([
    'variant' => 'solid',
])

<hr {{ $attributes->merge(['class' => 'border-0 border-t border-line'.($variant === 'dashed' ? ' border-dashed' : '')]) }}>
