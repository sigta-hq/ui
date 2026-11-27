@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'value' => null,        // the number, best with its calling code: "+45 25 04 88 63"
    'country' => null,      // ISO code picked when the value has no calling code
    'preferred' => [],      // ISO codes listed first, e.g. ['DK', 'SE', 'NO', 'DE']
    'countries' => null,    // ISO code => ['calling_code' => '+45', 'name' => 'Danmark']; default: every country
    'placeholder' => null,
    'variant' => 'default', // default | inline (borderless until hover, for detail views)
])

{{--
    Phone number field with its calling code picker inside it: a button showing
    the country and its code ("DK +45") opens a searchable list of countries, and
    a number typed or pasted with its calling code ("+46 70 …", "0046 70 …")
    picks the country by itself. The forwarded wire:model binds the field's
    value through x-modelable: the calling code and the number as typed,
    "+46 70 412 38 95", which the app parses and stores. A number the app saves
    in its own format comes back into the field the same way.

        <sigta:phone-input label="Mobile number" :value="$mobile" :preferred="['DK', 'SE', 'NO']"
            country="DK" wire:model.live.blur="mobile" />
--}}
@php
$models = $attributes->whereStartsWith('wire:model')->getAttributes();
$modelName = $models === [] ? null : (string) reset($models);
// Derived from the binding, so a Livewire morph keeps the same field.
$id = $attributes->get('id') ?? 'sigta-phone-'.($modelName !== null ? \Illuminate\Support\Str::slug($modelName) : \Illuminate\Support\Str::random(8));

$countries ??= \Sigta\Ui\PhoneCountries::all();
$preferred = array_values(array_filter((array) $preferred, fn ($code) => isset($countries[$code])));

// Split a typed calling code from the number, keeping how the number was written.
$typed = trim((string) $value);
$callingCode = null;
$national = $typed;

if (preg_match('/^(\+|00)(.*)$/s', $typed, $match) === 1) {
    $digits = preg_replace('/\D/', '', $match[2]);
    $callingCodes = array_column($countries, 'calling_code');

    foreach ([1, 2, 3] as $length) {
        $candidate = '+'.substr($digits, 0, $length);

        if (strlen($digits) >= $length && in_array($candidate, $callingCodes, true)) {
            $callingCode = $candidate;
            $national = preg_replace('/^[\s\-.\/()]+/', '', preg_replace('/^\D*'.implode('\D*', str_split(substr($digits, 0, $length))).'/', '', $match[2]));
            break;
        }
    }
}

$sharing = fn (string $code) => array_keys(array_filter($countries, fn ($country) => $country['calling_code'] === $code));
$selected = match (true) {
    $callingCode === null => isset($countries[$country]) ? $country : ($preferred[0] ?? array_key_first($countries)),
    isset($countries[$country]) && $countries[$country]['calling_code'] === $callingCode => $country,
    in_array(\Sigta\Ui\PhoneCountries::shownAs($callingCode), $sharing($callingCode), true) => \Sigta\Ui\PhoneCountries::shownAs($callingCode),
    default => $sharing($callingCode)[0],
};
$selectedCallingCode = $countries[$selected]['calling_code'];

$listed = [];
foreach ([...$preferred, ...array_diff(array_keys($countries), $preferred)] as $code) {
    $listed[] = [
        'code' => $code,
        'name' => $countries[$code]['name'],
        'callingCode' => $countries[$code]['calling_code'],
        'main' => \Sigta\Ui\PhoneCountries::shownAs($countries[$code]['calling_code']) === $code,
        'lastPreferred' => $code === end($preferred),
    ];
}

$inline = $variant === 'inline';

// Inline is a value in a detail view: no box until it is hovered or focused, its
// frame in the margin so the number lines up, and the divider waits too.
$frameClasses = match (true) {
    $inline => 'group/phone -mx-[0.8em] flex items-stretch bg-transparent border-[1.5px] rounded-[5px] transition duration-150 ease-out hover:border-line focus-within:border-ink',
    default => 'flex items-stretch bg-surface border-[1.5px] rounded-[5px] transition duration-150 ease-out focus-within:border-ink focus-within:shadow-[0_0_0_3px_color-mix(in_srgb,var(--sigta-fg)_8%,transparent)]',
};
$frameClasses .= $error ? ' border-danger' : ($inline ? ' border-transparent' : ' border-line');
$numberPadding = $inline ? 'py-[0.3em]' : 'py-[0.62em]';
$dividerClasses = $inline ? 'border-transparent group-hover/phone:border-line group-focus-within/phone:border-line' : 'border-line';
$chevronClasses = $inline ? 'opacity-0 transition-opacity group-hover/phone:opacity-100 group-focus-within/phone:opacity-100' : '';

$numberAttributes = ['id' => $id];

if ($error) {
    $numberAttributes['aria-invalid'] = 'true';
}

if ($describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''))) {
    $numberAttributes['aria-describedby'] = $describedBy;
}
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id" {{ $attributes->only(['class', 'wire:key']) }}>
    <div
        data-sigta="phone-input"
        data-variant="{{ $variant }}"
        data-id="{{ $id }}"
        data-country="{{ $selected }}"
        data-calling-code="{{ $selectedCallingCode }}"
        x-data="sigtaPhoneInput()"
        x-modelable="value"
        {{ $attributes->whereStartsWith('wire:model') }}
        x-ref="frame"
        x-on:click.outside="close()"
        x-on:keydown.escape="close(true)"
        x-on:resize.window="open && place()"
        class="{{ $frameClasses }}"
    >
        <input type="hidden" x-ref="value" x-bind:value="value" value="{{ $national === '' ? '' : $selectedCallingCode.' '.$national }}" @if ($attributes->has('name')) name="{{ $attributes->get('name') }}" @endif />

        <button
            type="button"
            x-ref="button"
            x-on:click="toggle()"
            x-on:keydown.down.prevent="show()"
            x-bind:aria-expanded="open"
            aria-haspopup="listbox"
            aria-controls="{{ $id }}-countries"
            x-bind:aria-label="@js(__('Calling code')).concat(': ', countryName(), ' ', callingCode)"
            aria-label="{{ __('Calling code') }}: {{ $countries[$selected]['name'] }} {{ $selectedCallingCode }}"
            class="block shrink-0 whitespace-nowrap border-r {{ $dividerClasses }} pl-[0.8em] pr-2.5 {{ $numberPadding }} text-left font-body text-[0.98rem] text-graphite transition-colors hover:text-ink"
            data-test="phone-country"
        >
            {{-- The button sets its line in the number's font and padding, so the small code sits on the number's baseline. --}}
            <span class="font-mono text-[0.72rem] tracking-[0.08em]" x-text="country">{{ $selected }}</span>
            <span class="ml-1 font-mono text-[0.72rem] tracking-[0.08em] text-ink" x-text="callingCode">{{ $selectedCallingCode }}</span>
            <x-sigta::icon name="chevron-down" size="xs" :class="'ml-1 inline-block align-middle '.$chevronClasses" />
        </button>

        <input
            type="tel"
            x-ref="number"
            value="{{ $national }}"
            inputmode="tel"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            x-on:input="typed()"
            x-on:blur="sync(true)"
            x-on:keydown.enter="sync(true)"
            class="min-w-0 flex-1 border-0 bg-transparent px-[0.8em] {{ $numberPadding }} font-body text-[0.98rem] text-ink outline-none placeholder:text-graphite/70"
            {{ $attributes->except(['class', 'id', 'name', 'wire:key'])->whereDoesntStartWith('wire:model')->merge(['data-test' => 'phone-number', 'autocomplete' => 'tel-national', ...$numberAttributes]) }}
        />

        {{-- Floats in the top layer, so no card or dialog around the field clips it. --}}
        <div
            x-show="open"
            x-cloak
            x-ref="panel"
            popover="manual"
            x-on:focusout="if (! $refs.panel.contains($event.relatedTarget)) close()"
            wire:ignore
            data-countries="{{ json_encode($listed) }}"
            class="m-0 flex flex-col overflow-hidden rounded-panel border border-line bg-surface-raised p-0 text-ink shadow-paper"
            data-test="phone-countries"
        >
            <div class="border-b border-line p-1.5">
                <input
                    type="search"
                    x-ref="search"
                    x-model="query"
                    x-on:input="active = 0"
                    x-on:keydown.down.prevent="move(1)"
                    x-on:keydown.up.prevent="move(-1)"
                    x-on:keydown.enter.prevent="chooseActive()"
                    role="combobox"
                    aria-autocomplete="list"
                    aria-controls="{{ $id }}-countries"
                    x-bind:aria-activedescendant="optionId(active)"
                    aria-label="{{ __('Search countries') }}"
                    placeholder="{{ __('Search countries') }}"
                    autocomplete="off"
                    class="w-full rounded-base border-0 bg-transparent px-2 py-1.5 font-body text-[0.95rem] text-ink outline-none placeholder:text-graphite/70"
                />
            </div>

            <div id="{{ $id }}-countries" role="listbox" aria-label="{{ __('Calling code') }}" class="max-h-64 overflow-y-auto p-1">
                <template x-for="(option, index) in shown" x-bind:key="option.code">
                    <div
                        x-bind:id="optionId(index)"
                        role="option"
                        x-bind:aria-selected="option.code === country ? 'true' : 'false'"
                        x-bind:data-active="index === active ? 'true' : null"
                        x-bind:class="option.lastPreferred && query.trim() === '' ? 'mb-1 border-b border-line pb-1' : ''"
                        x-on:mousedown.prevent=""
                        x-on:click="choose(option)"
                        x-on:mousemove="active = index"
                        class="flex cursor-pointer items-baseline gap-3 rounded-base px-2.5 py-1.5 data-active:bg-ink/5 aria-selected:font-medium"
                    >
                        <span class="w-6 shrink-0 font-mono text-[0.68rem] tracking-[0.08em] text-graphite" x-text="option.code"></span>
                        <span class="min-w-0 flex-1 truncate text-[0.92rem]" x-text="option.name"></span>
                        <span class="shrink-0 font-mono text-[0.72rem] text-graphite tabular-nums" x-text="option.callingCode"></span>
                    </div>
                </template>
                <div x-show="shown.length === 0" class="px-2.5 py-1.5 font-mono text-[0.72rem] text-graphite">{{ __('No matches.') }}</div>
            </div>
        </div>
    </div>
</x-sigta::field>
