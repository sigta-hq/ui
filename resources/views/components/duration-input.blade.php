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
--}}
@php
$initial = is_numeric($value) ? max(0, (int) $value) : null;
$placeholderText = $placeholder ?? '0m';

// Compact "1h15m" label for the initial (server-rendered) state — mirrors the
// Alpine `label` getter below so there's no flash on hydrate. No app helper.
if ($initial) {
    $h = intdiv($initial, 60);
    $m = $initial % 60;
    $initialLabel = $h === 0 ? $m.'m' : ($m === 0 ? $h.'h' : $h.'h'.$m.'m');
} else {
    $initialLabel = $placeholderText;
}

$controlClasses = 'w-28 text-left font-body text-[0.98rem] bg-surface border-[1.5px] border-line rounded-[5px] px-[0.8em] py-[0.62em] transition duration-150 ease-out focus:outline-none focus:border-ink focus:shadow-[0_0_0_3px_color-mix(in_srgb,var(--sigta-fg)_8%,transparent)]';
@endphp

<x-sigta::field :label="$label" {{ $attributes->whereDoesntStartWith('wire:model') }}>
    <div
        data-sigta="duration-input"
        x-data="{
            minutes: {{ $initial ?? 'null' }},
            editing: false,
            draft: '',
            placeholder: @js($placeholderText),
            get hasValue() { return this.minutes !== null && this.minutes > 0; },
            get label() {
                if (! this.hasValue) { return this.placeholder; }
                const mins = Math.max(0, this.minutes || 0);
                const h = Math.floor(mins / 60);
                const m = mins % 60;
                if (h === 0) { return m + 'm'; }
                if (m === 0) { return h + 'h'; }
                return h + 'h' + m + 'm';
            },
            edit() {
                const mins = Math.max(0, this.minutes || 0);
                if (mins > 0) {
                    this.draft = String(Math.floor(mins / 60)).padStart(2, '0') + ':' + String(mins % 60).padStart(2, '0');
                } else {
                    this.draft = '';
                }
                this.editing = true;
                this.$nextTick(() => { this.$refs.editor.focus(); this.$refs.editor.select(); });
            },
            commit() {
                const total = this.parse(this.draft);
                this.minutes = total > 0 ? total : null;
                const el = this.$refs.total;
                el.value = total > 0 ? total : '';
                el.dispatchEvent(new Event('input', { bubbles: true }));
                this.editing = false;
            },
            cancel() { this.editing = false; },
            parse(raw) {
                const s = (raw || '').trim();
                if (s === '') { return 0; }
                if (s.includes(':')) {
                    const parts = s.split(':');
                    const h = parseInt(parts[0], 10) || 0;
                    const m = parseInt(parts[1], 10) || 0;
                    return Math.max(0, h * 60 + m);
                }
                const n = parseInt(s, 10);
                return Number.isNaN(n) ? 0 : Math.max(0, n);
            },
        }"
    >
        <input type="hidden" x-ref="total" value="{{ $initial }}" {{ $attributes->whereStartsWith('wire:model') }} />

        <button
            type="button"
            x-show="! editing"
            x-on:click="edit()"
            x-text="label"
            :class="hasValue ? 'text-ink' : 'text-graphite'"
            class="{{ $controlClasses }} cursor-text hover:border-ink"
            data-test="duration-display"
        >{{ $initialLabel }}</button>

        <input
            x-show="editing"
            x-cloak
            x-ref="editor"
            type="text"
            x-model="draft"
            x-on:blur="commit()"
            x-on:keydown.enter.prevent="commit()"
            x-on:keydown.escape.prevent="cancel()"
            inputmode="numeric"
            placeholder="00:00"
            class="{{ $controlClasses }} text-ink placeholder:text-graphite/70"
            aria-label="{{ trim(($label ? $label.' ' : '').__('as hours and minutes, HH:MM')) }}"
            data-test="duration-editor"
        />
    </div>
</x-sigta::field>
