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

$fieldClasses = 'flex items-stretch bg-surface border-[1.5px] border-line rounded-[5px] transition duration-150 ease-out focus-within:border-ink focus-within:shadow-[0_0_0_3px_color-mix(in_srgb,var(--sigta-fg)_8%,transparent)]';
@endphp

<x-sigta::field :label="$label" {{ $attributes->whereDoesntStartWith('wire:model') }}>
    <div
        data-sigta="money-input"
        class="{{ $fieldClasses }}"
        x-data="{
            major: @js($initialMajor),
            currency: @js((string) $currency),
            open: false,
            currencies: @js($currencyOptions),
            sync() {
                const n = parseFloat(String(this.major).replace(',', '.'));
                const minor = Number.isFinite(n) ? Math.round(n * 100) : '';
                const el = this.$refs.total;
                el.value = minor;
                el.dispatchEvent(new Event('input', { bubbles: true }));
            },
            selectCurrency(code) {
                this.currency = code;
                const el = this.$refs.currency;
                el.value = code;
                el.dispatchEvent(new Event('input', { bubbles: true }));
                this.open = false;
            },
        }"
        x-init="sync()"
        x-on:keydown.escape="open = false"
    >
        <input type="hidden" x-ref="total" value="{{ $initialMinor }}" {{ $attributes->whereStartsWith('wire:model') }} />

        {{-- wire:model binds only when currencyModel is set. This is a plain <input>,
             so bind it with a Blade @if (a colon :wire:model would only evaluate on a
             component tag, not here). --}}
        <input type="hidden" x-ref="currency" name="{{ $currencyName }}" value="{{ $currency }}" @if ($currencyModel) wire:model="{{ $currencyModel }}" @endif />

        <input
            type="text"
            inputmode="decimal"
            x-model="major"
            x-on:input="sync()"
            placeholder="{{ $placeholderText }}"
            class="min-w-0 flex-1 border-0 bg-transparent px-[0.8em] py-[0.62em] font-body text-[0.98rem] text-ink outline-none placeholder:text-graphite/70"
            aria-label="{{ trim(($label ? $label.' ' : '').__('amount')) }}"
            data-test="money-amount"
        />

        <div class="relative shrink-0">
            <button
                type="button"
                x-on:click="open = ! open"
                :aria-expanded="open"
                aria-haspopup="listbox"
                aria-label="{{ __('Currency') }}"
                class="flex h-full items-center gap-1 border-l border-line px-2.5 font-mono text-[0.72rem] tracking-[0.08em] text-graphite transition-colors hover:text-ink"
                data-test="money-currency"
            >
                <span x-text="currency"></span>
                <x-sigta::icon name="chevron-down" size="xs" />
            </button>

            <div
                x-show="open"
                x-cloak
                x-transition.opacity.duration.100ms
                x-on:click.outside="open = false"
                role="listbox"
                class="absolute right-0 top-full z-30 mt-1 min-w-[5rem] rounded-panel border border-line bg-surface-raised p-1 shadow-paper"
                data-test="money-currency-menu"
            >
                <template x-for="(currencyLabel, code) in currencies" :key="code">
                    <button
                        type="button"
                        x-on:click="selectCurrency(code)"
                        role="option"
                        :aria-selected="code === currency"
                        class="flex w-full items-center gap-2 rounded-base px-2.5 py-1.5 text-left font-mono text-[0.72rem] hover:bg-ink/5"
                        :class="code === currency ? 'text-ink' : 'text-graphite'"
                    >
                        <span x-text="code"></span>
                        <span class="text-graphite/70" x-show="currencyLabel !== code" x-text="currencyLabel"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>
</x-sigta::field>
