{{-- Segmented control (brand .seg) — radio-group semantics, zero JS.
     Usage:
       <x-sigta::segmented name="range" label="Range" :options="['7d' => '7d', '30d' => '30d', 'qtd' => 'QTD']" wire:model="range" />
--}}
@props([
    'name',
    'options' => [],
    'label' => null,
    'checked' => null,
])

<fieldset {{ $attributes->only('class') }} data-sigta="segmented">
    @if ($label)<legend data-scope="segmented" data-part="legend">{{ $label }}</legend>@endif
    <div data-scope="segmented" data-part="group">
        @foreach ($options as $value => $optionLabel)
            <label data-scope="segmented" data-part="option">
                <input
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $value }}"
                    @checked($checked !== null && (string) $checked === (string) $value)
                    {{ $attributes->except('class') }}
                    data-scope="segmented"
                    data-part="input"
                >
                <span data-scope="segmented" data-part="label">{{ $optionLabel }}</span>
            </label>
        @endforeach
    </div>
</fieldset>
