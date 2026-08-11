{{-- One-time-code input: a row of single-character boxes backed by one hidden
     input. name / wire:model / other attributes forward onto the hidden input,
     which re-dispatches `input` whenever the combined value changes. --}}
@props([
    'length' => 6,
    'label' => null,
    'description' => null,
    'error' => null,
    'autofocus' => false,
])

@php
$length = max(1, (int) $length);
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);
$disabled = $attributes->has('disabled');

$boxClasses = 'size-11 rounded-[5px] border-[1.5px] bg-surface text-center font-mono text-lg text-ink tabular-nums transition duration-150 ease-out focus:outline-none focus:border-ink focus:shadow-[0_0_0_3px_color-mix(in_srgb,var(--sigta-fg)_8%,transparent)] disabled:opacity-40 disabled:pointer-events-none';
$boxClasses .= $error ? ' border-danger' : ' border-line';

$describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''));
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id.'-0'">
    <div
        data-sigta="otp"
        x-data="{
            length: {{ $length }},
            digits: Array({{ $length }}).fill(''),
            init() {
                String(this.$refs.hidden.value || '').split('').slice(0, this.length).forEach((ch, i) => {
                    this.digits[i] = ch;
                    this.box(i).value = ch;
                });
            },
            box(i) { return this.$refs['box' + i]; },
            sync() {
                this.$refs.hidden.value = this.digits.join('');
                this.$refs.hidden.dispatchEvent(new Event('input', { bubbles: true }));
            },
            fill(start, text) {
                const chars = String(text).replace(/\s+/g, '').split('').slice(0, this.length - start);
                if (! chars.length) return;
                chars.forEach((ch, offset) => {
                    this.digits[start + offset] = ch;
                    this.box(start + offset).value = ch;
                });
                this.sync();
                this.box(Math.min(start + chars.length, this.length - 1)).focus();
            },
            handleInput(i, event) {
                const value = event.target.value.replace(/\s+/g, '');
                if (value === '') {
                    this.digits[i] = '';
                    event.target.value = '';
                    this.sync();
                    return;
                }
                event.target.value = '';
                this.fill(i, value);
            },
            backspace(i, event) {
                if (event.target.value !== '' || i === 0) return;
                event.preventDefault();
                this.digits[i - 1] = '';
                this.box(i - 1).value = '';
                this.sync();
                this.box(i - 1).focus();
            },
            move(i, step) {
                const target = this.box(Math.min(this.length - 1, Math.max(0, i + step)));
                target.focus();
                target.select();
            },
        }"
        {{ $attributes->only('class')->merge(['class' => 'flex items-center gap-2']) }}
    >
        @for ($i = 0; $i < $length; $i++)
            <input
                type="text"
                inputmode="numeric"
                autocomplete="one-time-code"
                aria-label="Digit {{ $i + 1 }} of {{ $length }}"
                class="{{ $boxClasses }}"
                x-ref="box{{ $i }}"
                @focus="$event.target.select()"
                @input="handleInput({{ $i }}, $event)"
                @keydown.backspace="backspace({{ $i }}, $event)"
                @keydown.left.prevent="move({{ $i }}, -1)"
                @keydown.right.prevent="move({{ $i }}, 1)"
                @paste.prevent="fill({{ $i }}, $event.clipboardData.getData('text'))"
                @if ($i === 0) id="{{ $id }}-0" @endif
                @if ($i === 0 && $autofocus) autofocus @endif
                @if ($disabled) disabled @endif
                @if ($error) aria-invalid="true" @endif
                @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            />
        @endfor

        <input type="hidden" x-ref="hidden" {{ $attributes->except('class') }} />
    </div>
</x-sigta::field>
