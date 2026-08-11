@props([
    'label' => null,
])

@php
$track = "relative h-[26px] w-[46px] shrink-0 rounded-full border-[1.5px] border-line bg-line transition duration-200 ease-out after:content-[''] after:absolute after:left-[2px] after:top-[2px] after:size-[18px] after:rounded-full after:bg-surface-raised after:shadow-[0_1px_2px_rgba(0,0,0,0.25)] after:transition after:duration-200 after:ease-out peer-checked:bg-ink peer-checked:border-ink peer-checked:after:translate-x-[20px] peer-focus-visible:outline-[2.5px] peer-focus-visible:outline-ink peer-focus-visible:outline-offset-[3px]";
@endphp

<label data-sigta="switch" {{ $attributes->only('class')->merge(['class' => 'inline-flex cursor-pointer items-center gap-[0.7rem] font-mono text-[0.8rem] text-ink has-[:disabled]:opacity-40 has-[:disabled]:pointer-events-none']) }}>
    <input type="checkbox" role="switch" {{ $attributes->except('class')->merge(['class' => 'peer sr-only']) }} />
    <span aria-hidden="true" class="{{ $track }}"></span>
    @if ($label)
        <span>{{ $label }}</span>
    @endif
</label>
