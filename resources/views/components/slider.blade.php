{{-- Native range input, brand-styled. All attributes (min/max/step/value/wire:model)
     forward onto the input. `showValue` renders the live value in mono via inline Alpine. --}}
@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'showValue' => false,
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);

$classes = 'w-full appearance-none cursor-pointer bg-transparent disabled:opacity-40 disabled:pointer-events-none'
    // track
    .' [&::-webkit-slider-runnable-track]:h-1 [&::-webkit-slider-runnable-track]:rounded-full [&::-webkit-slider-runnable-track]:bg-line'
    .' [&::-moz-range-track]:h-1 [&::-moz-range-track]:rounded-full [&::-moz-range-track]:bg-line'
    // thumb
    .' [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:size-[18px] [&::-webkit-slider-thumb]:-mt-[7px] [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:border-[1.5px] [&::-webkit-slider-thumb]:border-solid [&::-webkit-slider-thumb]:bg-surface-raised [&::-webkit-slider-thumb]:shadow-[0_1px_2px_rgba(0,0,0,0.25)] [&::-webkit-slider-thumb]:transition [&::-webkit-slider-thumb]:duration-150 [&::-webkit-slider-thumb]:ease-out'
    .' [&::-moz-range-thumb]:size-[18px] [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-[1.5px] [&::-moz-range-thumb]:border-solid [&::-moz-range-thumb]:bg-surface-raised';

$classes .= $error
    ? ' [&::-webkit-slider-thumb]:border-danger [&::-moz-range-thumb]:border-danger'
    : ' [&::-webkit-slider-thumb]:border-ink [&::-moz-range-thumb]:border-ink';

$controlAttributes = ['id' => $id, 'class' => $classes];

if ($error) {
    $controlAttributes['aria-invalid'] = 'true';
}

if ($describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''))) {
    $controlAttributes['aria-describedby'] = $describedBy;
}
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    @if ($showValue)
        <span
            class="flex items-center gap-3"
            x-data="{ value: '' }"
            x-init="value = $el.querySelector('input').value"
        >
            <input
                data-sigta="slider"
                type="range"
                x-on:input="value = $event.target.value"
                {{ $attributes->merge($controlAttributes) }}
            />
            <output for="{{ $id }}" class="min-w-[2.5ch] text-right font-mono text-[0.72rem] text-ink tabular-nums" x-text="value"></output>
        </span>
    @else
        <input data-sigta="slider" type="range" {{ $attributes->merge($controlAttributes) }} />
    @endif
</x-sigta::field>
