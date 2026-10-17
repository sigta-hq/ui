@props([
    'label' => null,
    'value' => null,
    'placeholder' => null,
])

{{--
    A single duration field that reads as a compact "1h59m" label and, on click,
    becomes an editable "HH:MM" input (e.g. "01:59"). On blur or Enter it parses
    the clock value back to whole minutes and emits that total onto the forwarded
    wire:model via a hidden input. The parse is forgiving: "H:MM", "HH:MM", or a
    bare minute count.

    wire:model contract: the forwarded wire:model lands on a hidden input that
    carries whole minutes; every other attribute lands on the field. Give the
    field a value-keyed wire:key (wire:key="est-{{ $minutes }}") so switching
    context or resetting after a save gives Alpine fresh state instead of a stale
    in-place value.

        <sigta:duration-input label="Estimate" :value="$task->estimate_minutes"
            wire:model="estimate" wire:key="est-{{ $task->estimate_minutes }}" />

    The behavior lives in sigtaDurationInput (resources/js/sigta.js), on the
    core's durationLabel / durationDraft / parseDuration
    (packages/core/src/format.ts); the initial label below is durationLabel, in PHP.
--}}
@php
$initial = is_numeric($value) ? max(0, (int) $value) : null;
$placeholderText = $placeholder ?? '0m';

// Compact "1h15m" label for the initial (server-rendered) state — the same as
// the runtime's label, so there's no flash on hydrate. No app helper.
if ($initial) {
    $h = intdiv($initial, 60);
    $m = $initial % 60;
    $initialLabel = $h === 0 ? $m.'m' : ($m === 0 ? $h.'h' : $h.'h'.$m.'m');
} else {
    $initialLabel = $placeholderText;
}

$editorId = 'sigta-'.\Illuminate\Support\Str::random(8);
@endphp

<x-sigta::field :label="$label" :for="$editorId" {{ $attributes->whereDoesntStartWith('wire:model') }}>
    <div
        data-sigta="duration-input"
        x-data="sigtaDurationInput({{ \Illuminate\Support\Js::from($placeholderText) }})"
    >
        <input type="hidden" x-ref="total" value="{{ $initial }}" {{ $attributes->whereStartsWith('wire:model') }} />

        <button
            type="button"
            x-show="! editing"
            x-on:click="edit()"
            x-text="label"
            @unless ($initial) data-empty="" @endunless
            x-bind:data-empty="hasValue ? null : ''"
            data-scope="duration-input"
            data-part="display"
            data-test="duration-display"
        >{{ $initialLabel }}</button>

        <input
            x-show="editing"
            x-cloak
            x-ref="editor"
            id="{{ $editorId }}"
            type="text"
            x-model="draft"
            x-on:blur="commit()"
            x-on:keydown.enter.prevent="commit()"
            x-on:keydown.escape.prevent="cancel()"
            inputmode="numeric"
            placeholder="00:00"
            aria-label="{{ trim(($label ? $label.' ' : '').__('as hours and minutes, HH:MM')) }}"
            data-scope="duration-input"
            data-part="editor"
            data-test="duration-editor"
        />
    </div>
</x-sigta::field>
