@props([
    'label' => null,
])

@php
$classes = "relative size-[18px] shrink-0 cursor-pointer appearance-none rounded-[4px] border-[1.5px] border-graphite bg-surface transition duration-150 ease-out checked:bg-ink checked:border-ink checked:after:content-[''] after:absolute after:left-[5px] after:top-[1px] after:h-[9px] after:w-[5px] after:rotate-45 after:border-r-2 after:border-b-2 after:border-surface";
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
