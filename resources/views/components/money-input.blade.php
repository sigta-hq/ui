@props([
    'label' => null,
    'value' => null,          // initial amount in MINOR units
    'placeholder' => null,
    'currency' => 'DKK',      // selected currency code
    'currencies' => null,     // code => label map for the toggle
    'currencyName' => null,   // <input name> for the currency radio group
    'currencyModel' => null,  // wire:model for the currency toggle (optional)
])

{{--
    Amount field plus a currency toggle. The amount is entered in MAJOR units and
    emitted in MINOR units onto the forwarded wire:model via a hidden input
    (×100, rounded); the currency segmented control binds separately via
    currencyModel. App logic (the currency enum, minor↔major rules for exotic
    currencies) stays in your app — this composes <sigta:input>'s look and
    <sigta:segmented>.

        <sigta:money-input label="Hourly rate" :value="$rate->amount_minor"
            wire:model="amountMinor" :currency="$currency" currency-model="currency" />
--}}
@php
$initialMinor = is_numeric($value) ? (int) $value : null;
// Plain major string for the text field (no grouping): 123450 → "1234.5".
$initialMajor = $initialMinor !== null
    ? rtrim(rtrim(number_format($initialMinor / 100, 2, '.', ''), '0'), '.')
    : '';

$placeholderText = $placeholder ?? '0';
$currencyOptions = $currencies ?: ['DKK' => 'DKK', 'EUR' => 'EUR', 'USD' => 'USD'];
$currencyName ??= ($currencyModel ?? 'currency');

$controlClasses = 'w-full font-body text-[0.98rem] text-ink bg-surface placeholder:text-graphite/70 border-[1.5px] border-line rounded-[5px] px-[0.8em] py-[0.62em] transition duration-150 ease-out focus:outline-none focus:border-ink focus:shadow-[0_0_0_3px_color-mix(in_srgb,var(--sigta-fg)_8%,transparent)]';
@endphp

<x-sigta::field :label="$label" {{ $attributes->whereDoesntStartWith('wire:model') }}>
    <div
        data-sigta="money-input"
        class="flex items-center gap-2"
        x-data="{
            major: @js($initialMajor),
            sync() {
                const n = parseFloat(String(this.major).replace(',', '.'));
                const minor = Number.isFinite(n) ? Math.round(n * 100) : '';
                const el = this.$refs.total;
                el.value = minor;
                el.dispatchEvent(new Event('input', { bubbles: true }));
            },
        }"
        x-init="sync()"
    >
        <input type="hidden" x-ref="total" value="{{ $initialMinor }}" {{ $attributes->whereStartsWith('wire:model') }} />

        <input
            type="text"
            inputmode="decimal"
            x-model="major"
            x-on:input="sync()"
            placeholder="{{ $placeholderText }}"
            class="{{ $controlClasses }} flex-1"
            aria-label="{{ trim(($label ? $label.' ' : '').__('amount')) }}"
            data-test="money-amount"
        />

        {{-- :wire:model binds only when currencyModel is set (null → attribute omitted).
             Keep the tag free of {{ }}/@directives — they break component-tag parsing. --}}
        <x-sigta::segmented
            :name="$currencyName"
            :options="$currencyOptions"
            :checked="$currency"
            :label="__('Currency')"
            :wire:model="$currencyModel"
            data-test="money-currency"
        />
    </div>
</x-sigta::field>
