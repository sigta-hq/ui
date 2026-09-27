@props([
    'label' => null,
])

@php
// sigta-check (core.css) draws the tick as two pen strokes; pressed, the box
// gives a little and springs back.
$classes = 'sigta-check relative size-[18px] shrink-0 cursor-pointer appearance-none rounded-[4px] border-[1.5px] border-graphite bg-surface transition duration-150 ease-sigta-spring checked:bg-ink checked:border-ink active:scale-90 active:duration-[var(--sigta-dur-press,90ms)] active:ease-out';
$classes .= $label ? '' : ' disabled:opacity-40 disabled:pointer-events-none';
@endphp

@if ($label)
    <label data-sigta="checkbox" {{ $attributes->only('class')->merge(['class' => 'inline-flex cursor-pointer items-center gap-[0.6rem] font-mono text-[0.82rem] text-ink has-[:disabled]:opacity-40 has-[:disabled]:pointer-events-none']) }}>
        <input type="checkbox" {{ $attributes->except('class')->merge(['class' => $classes]) }} />
        <span>{{ $label }}</span>
    </label>
@else
    <input data-sigta="checkbox" type="checkbox" {{ $attributes->merge(['class' => $classes]) }} />
@endif
