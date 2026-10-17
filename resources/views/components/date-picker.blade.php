{{-- Date picker: brand text input + calendar-icon button that opens the shared
     <x-sigta::calendar> in an anchored popover. Open/close reuses sigtaDropdown
     (escape / click-outside / focus return); the sigtaDatePicker scope around it
     (resources/js/sigta.js) holds the display text and the Y-m-d model.
     Selecting a day fills both; the input also accepts typed dates within
     min/max, which the calendar then shows. --}}
@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'name' => null,
    'value' => null,       // Y-m-d
    'min' => null,         // Y-m-d
    'max' => null,         // Y-m-d
    'placeholder' => 'Select a date',
    'today' => null,       // Y-m-d; null = the current date
])

@php
use Illuminate\Support\Carbon;

$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);
$panelId = $id.'-panel';
$iso = fn ($date) => ($date === null || $date === '') ? null : Carbon::parse($date)->format('Y-m-d');
$valueIso = $iso($value);
$displayValue = $valueIso ? Carbon::parse($valueIso)->format('j M Y') : '';
$describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''));
$disabled = $attributes->has('disabled');
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <div
        data-sigta="date-picker"
        x-data="sigtaDatePicker(@js(['value' => $valueIso, 'min' => $iso($min), 'max' => $iso($max)]))"
        {{ $attributes->only('class') }}
    >
        <div
            x-data="sigtaDropdown"
            x-on:keydown.escape.prevent.stop="close(true)"
            x-on:focusout="if (! $el.contains($event.relatedTarget)) close()"
            x-on:calendar-change="pick($event.detail); close(true)"
            data-scope="date-picker"
            data-part="anchor"
        >
            <div x-ref="trigger" data-scope="date-picker" data-part="trigger">
                <input
                    type="text"
                    id="{{ $id }}"
                    role="combobox"
                    aria-haspopup="dialog"
                    aria-controls="{{ $panelId }}"
                    aria-expanded="false"
                    x-bind:aria-expanded="open"
                    x-model="display"
                    x-on:change="typed()"
                    x-on:keydown.enter.prevent="$event.target.blur()"
                    x-on:keydown.down.prevent="open ? null : toggle()"
                    placeholder="{{ $placeholder }}"
                    autocomplete="off"
                    value="{{ $displayValue }}"
                    data-scope="date-picker"
                    data-part="input"
                    @if ($disabled) disabled @endif
                    @if ($error) aria-invalid="true" @endif
                    @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
                />
                <button type="button" tabindex="-1" x-on:click="toggle()" aria-label="Open calendar" data-scope="date-picker" data-part="toggle" @if ($disabled) disabled @endif>
                    <x-sigta::icon name="calendar-days" size="sm" />
                </button>
            </div>

            <input type="hidden" x-ref="hidden" value="{{ $valueIso }}" @if ($name) name="{{ $name }}" @endif {{ $attributes->except(['class', 'id']) }} />

            <div
                x-cloak
                x-show="open"
                x-on:click.outside="close()"
                id="{{ $panelId }}"
                role="dialog"
                aria-label="Choose date"
                data-scope="date-picker"
                data-part="panel"
            >
                <x-sigta::calendar :value="$valueIso" :min="$min" :max="$max" :today="$today" />
            </div>
        </div>
    </div>
</x-sigta::field>
