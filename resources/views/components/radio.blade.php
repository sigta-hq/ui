@props([
    'label' => null,
])

@php
$classes = "relative size-[18px] shrink-0 cursor-pointer appearance-none rounded-full border-[1.5px] border-graphite bg-surface transition duration-150 ease-out checked:bg-ink checked:border-ink checked:after:content-[''] after:absolute after:inset-[4px] after:rounded-full after:bg-surface";
$classes .= $label ? '' : ' disabled:opacity-40 disabled:pointer-events-none';
@endphp

@if ($label)
    <label data-sigta="radio" {{ $attributes->only('class')->merge(['class' => 'inline-flex cursor-pointer items-center gap-[0.6rem] font-mono text-[0.82rem] text-ink has-[:disabled]:opacity-40 has-[:disabled]:pointer-events-none']) }}>
        <input type="radio" {{ $attributes->except('class')->merge(['class' => $classes]) }} />
        <span>{{ $label }}</span>
    </label>
@else
    <input data-sigta="radio" type="radio" {{ $attributes->merge(['class' => $classes]) }} />
@endif
