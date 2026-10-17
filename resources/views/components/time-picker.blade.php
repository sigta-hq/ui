{{-- Time picker: brand text input + clock-icon button that opens a listbox of
     times (every `step` minutes, bounded by min/max). The sigtaTimePicker
     Alpine component (resources/js/sigta.js) mirrors the dropdown open/close
     pattern and adds listbox roving; value forwards through a hidden input as
     "HH:MM". `format` (24|12) controls the visible labels only — the model
     stays 24h. Typed times within min/max are accepted; emptying the field
     clears it. The list matches the core's timeOptions()
     (packages/core/src/time.ts), which the Svelte twin renders with. --}}
@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'name' => null,
    'value' => null,       // HH:MM (24h)
    'min' => null,         // HH:MM
    'max' => null,         // HH:MM
    'step' => 30,          // minutes
    'format' => '24',      // '24' | '12'
    'placeholder' => 'Select a time',
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);
$listId = $id.'-listbox';
$describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''));
$disabled = $attributes->has('disabled');

// HH:MM from "9:30", "09:30:00" or a Carbon/DateTimeInterface; null when it is no time of day.
$time = function ($t) {
    if ($t instanceof \DateTimeInterface) {
        return $t->format('H:i');
    }
    return preg_match('/^(\d{1,2}):(\d{2})(?::\d{2}(?:\.\d+)?)?$/', trim((string) $t), $m) && (int) $m[1] < 24 && (int) $m[2] < 60
        ? sprintf('%02d:%02d', $m[1], $m[2])
        : null;
};
$value  = $time($value);
$min    = $time($min);
$max    = $time($max);
$step   = max(1, (int) $step);
$format = (string) $format;
$toMin  = fn ($t) => (int) explode(':', $t)[0] * 60 + (int) explode(':', $t)[1];
$startM = $min ? $toMin($min) : 0;
$endM   = $max ? $toMin($max) : 23 * 60 + 59;

$fmt = function (int $h, int $m) use ($format) {
    if ($format === '12') {
        $ap = $h < 12 ? 'AM' : 'PM';
        $hh = $h % 12 ?: 12;
        return $hh.':'.sprintf('%02d', $m).' '.$ap;
    }
    return sprintf('%02d:%02d', $h, $m);
};

$options = [];
for ($mm = $startM; $mm <= $endM; $mm += $step) {
    $h = intdiv($mm, 60);
    $m = $mm % 60;
    $val = sprintf('%02d:%02d', $h, $m);
    $options[] = ['value' => $val, 'label' => $fmt($h, $m), 'selected' => $val === $value];
}

$displayValue = $value ? $fmt((int) explode(':', $value)[0], (int) explode(':', $value)[1]) : '';
$config = ['value' => $value, 'min' => $min, 'max' => $max, 'step' => $step, 'format' => $format];
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <div
        data-sigta="time-picker"
        x-data="sigtaTimePicker(@js($config))"
        x-on:keydown.escape.prevent.stop="close(true)"
        x-on:focusout="if (! $el.contains($event.relatedTarget)) close()"
        {{ $attributes->only('class') }}
    >
        <div data-scope="time-picker" data-part="trigger">
            <input
                type="text"
                x-ref="input"
                id="{{ $id }}"
                role="combobox"
                aria-haspopup="listbox"
                aria-controls="{{ $listId }}"
                aria-expanded="false"
                x-bind:aria-expanded="open"
                x-model="display"
                x-on:click="openList()"
                x-on:change="typed()"
                x-on:keydown.down.prevent="move(1)"
                x-on:keydown.up.prevent="move(-1)"
                x-on:keydown.enter.prevent="open ? chooseActive() : openList()"
                placeholder="{{ $placeholder }}"
                autocomplete="off"
                value="{{ $displayValue }}"
                data-scope="time-picker"
                data-part="input"
                @if ($disabled) disabled @endif
                @if ($error) aria-invalid="true" @endif
                @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            />
            <button type="button" tabindex="-1" x-on:click="toggle()" aria-label="Open time list" data-scope="time-picker" data-part="toggle" @if ($disabled) disabled @endif>
                <x-sigta::icon name="clock" size="sm" />
            </button>
        </div>

        <input type="hidden" x-ref="hidden" value="{{ $value }}" @if ($name) name="{{ $name }}" @endif {{ $attributes->except(['class', 'id']) }} />

        <ul
            x-cloak
            x-show="open"
            x-ref="list"
            id="{{ $listId }}"
            role="listbox"
            aria-label="{{ $label ?? 'Times' }}"
            data-scope="time-picker"
            data-part="listbox"
        >
            @foreach ($options as $i => $opt)
                <li role="presentation">
                    <button
                        type="button"
                        role="option"
                        data-value="{{ $opt['value'] }}"
                        aria-selected="{{ $opt['selected'] ? 'true' : 'false' }}"
                        x-bind:aria-selected="value === @js($opt['value']) ? 'true' : 'false'"
                        x-bind:data-active="activeIndex === {{ $i }} ? '' : null"
                        tabindex="-1"
                        x-on:click="choose(@js($opt['value']), @js($opt['label']))"
                        x-on:mousemove="activeIndex = {{ $i }}"
                        data-scope="time-picker"
                        data-part="option"
                    >{{ $opt['label'] }}</button>
                </li>
            @endforeach
        </ul>
    </div>
</x-sigta::field>
