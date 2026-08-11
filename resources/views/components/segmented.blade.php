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

<fieldset {{ $attributes->only('class')->merge(['class' => 'inline-block']) }} data-sigta="segmented">
    @if ($label)<legend class="sr-only">{{ $label }}</legend>@endif
    <div class="inline-flex border border-line rounded-[5px] overflow-hidden font-mono text-[0.72rem]">
        @foreach ($options as $value => $optionLabel)
            <label class="cursor-pointer">
                <input
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $value }}"
                    @checked($checked !== null && (string) $checked === (string) $value)
                    {{ $attributes->except('class') }}
                    class="sr-only peer"
                >
                <span class="inline-block px-3 py-1.5 text-graphite peer-checked:bg-ink peer-checked:text-surface hover:text-ink peer-checked:hover:text-surface peer-focus-visible:outline-[2.5px] peer-focus-visible:outline-ink peer-focus-visible:-outline-offset-2 transition-colors">{{ $optionLabel }}</span>
            </label>
        @endforeach
    </div>
</fieldset>
