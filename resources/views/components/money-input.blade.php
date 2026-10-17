@props([
    'label' => null,
    'value' => null,          // initial amount in MINOR units
    'placeholder' => null,
    'currency' => 'DKK',      // selected currency code
    'currencies' => null,     // code => label map for the switcher
    'currencyName' => null,   // <input name> for the currency value
    'currencyModel' => null,  // wire:model for the currency (optional)
])

{{--
    Amount field with an inline currency switcher. The amount is entered in MAJOR
    units and emitted in MINOR units onto the forwarded wire:model via a hidden
    input (×100, rounded). The currency sits inside the same field as a small
    dropdown button (code + chevron) and binds separately via currencyModel
    through a hidden input. App logic (the currency enum, minor/major rules for
    exotic currencies) stays in your app.

        <sigta:money-input label="Hourly rate" :value="$rate->amount_minor"
            wire:model="amountMinor" :currency="$currency" currency-model="currency" />

    The parsing lives in sigtaMoneyInput (resources/js/sigta.js), on the core's
    majorFromMinor / minorFromMajor (packages/core/src/format.ts); the initial
    major text below is the same as majorFromMinor, in PHP.
--}}
@php
$initialMinor = is_numeric($value) ? (int) $value : null;
// Plain major string for the text field (no grouping): 123450 becomes "1234.5".
$initialMajor = $initialMinor !== null
    ? rtrim(rtrim(number_format($initialMinor / 100, 2, '.', ''), '0'), '.')
    : '';

$placeholderText = $placeholder ?? '0';
$currencyOptions = $currencies ?: ['DKK' => 'DKK', 'EUR' => 'EUR', 'USD' => 'USD'];
$currencyName ??= ($currencyModel ?? 'currency');
$amountId = 'sigta-'.\Illuminate\Support\Str::random(8);
@endphp

<x-sigta::field :label="$label" :for="$amountId" {{ $attributes->whereDoesntStartWith('wire:model') }}>
    <div
        data-sigta="money-input"
        x-data="sigtaMoneyInput()"
        x-on:keydown.escape="open = false"
    >
        <input type="hidden" x-ref="total" value="{{ $initialMinor }}" {{ $attributes->whereStartsWith('wire:model') }} />

        {{-- wire:model binds only when currencyModel is set. This is a plain <input>,
             so bind it with a Blade @if (a colon :wire:model would only evaluate on a
             component tag, not here). --}}
        <input type="hidden" x-ref="currency" name="{{ $currencyName }}" value="{{ $currency }}" @if ($currencyModel) wire:model="{{ $currencyModel }}" @endif />

        <input
            type="text"
            id="{{ $amountId }}"
            inputmode="decimal"
            value="{{ $initialMajor }}"
            x-model="major"
            x-on:input="sync()"
            placeholder="{{ $placeholderText }}"
            aria-label="{{ trim(($label ? $label.' ' : '').__('amount')) }}"
            data-test="money-amount"
            data-scope="money-input"
            data-part="amount"
        />

        <div data-scope="money-input" data-part="switcher">
            <button
                type="button"
                x-on:click="open = ! open"
                aria-expanded="false"
                x-bind:aria-expanded="open"
                aria-haspopup="listbox"
                aria-label="{{ __('Currency') }}"
                data-test="money-currency"
                data-scope="money-input"
                data-part="currency"
            >
                <span x-text="currency">{{ $currency }}</span>
                <x-sigta::icon name="chevron-down" size="xs" />
            </button>

            <div
                x-show="open"
                x-cloak
                x-transition.opacity.duration.100ms
                x-on:click.outside="open = false"
                role="listbox"
                data-test="money-currency-menu"
                data-scope="money-input"
                data-part="menu"
            >
                @foreach ($currencyOptions as $code => $currencyLabel)
                    <button
                        type="button"
                        x-on:click="selectCurrency({{ \Illuminate\Support\Js::from((string) $code) }})"
                        role="option"
                        aria-selected="{{ (string) $code === (string) $currency ? 'true' : 'false' }}"
                        x-bind:aria-selected="currency === {{ \Illuminate\Support\Js::from((string) $code) }} ? 'true' : 'false'"
                        data-scope="money-input"
                        data-part="option"
                    >
                        <span>{{ $code }}</span>
                        @if ((string) $currencyLabel !== (string) $code)
                            <span data-scope="money-input" data-part="label">{{ $currencyLabel }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</x-sigta::field>
