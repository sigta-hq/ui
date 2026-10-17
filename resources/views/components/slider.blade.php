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

$controlAttributes = ['id' => $id];

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
            data-scope="slider"
            data-part="wrap"
            x-data="{ value: '' }"
            x-init="value = $el.querySelector('input').value"
        >
            <input
                data-sigta="slider"
                type="range"
                x-on:input="value = $event.target.value"
                {{ $attributes->merge($controlAttributes) }}
            />
            <output for="{{ $id }}" data-scope="slider" data-part="value" x-text="value"></output>
        </span>
    @else
        <input data-sigta="slider" type="range" {{ $attributes->merge($controlAttributes) }} />
    @endif
</x-sigta::field>
