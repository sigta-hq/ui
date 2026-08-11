{{-- Chat composer: auto-growing textarea with a leading (attach) slot and a
     send button. Enter submits, Shift+Enter inserts a newline. Submitting
     dispatches a bubbling `sigta-composer-submit` event with { text } and clears
     the field:

         <x-sigta::composer x-on:sigta-composer-submit="$wire.send($event.detail.text)" />

     Attributes (name, wire:model, id, aria-*) forward onto the native textarea. --}}
@props([
    'placeholder' => 'Write a message…',
    'name' => null,
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);

$textareaClasses = 'flex-1 min-w-0 resize-none bg-transparent font-body text-[0.98rem] leading-relaxed text-ink placeholder:text-graphite/70 px-1 py-1.5 focus:outline-none';
@endphp

<div
    data-sigta="composer"
    x-data="{
        text: @js(''),
        maxRows: 6,

        init() {
            this.$nextTick(() => this.resize());
        },

        resize() {
            const el = this.$refs.input;
            el.style.height = 'auto';
            const line = parseFloat(getComputedStyle(el).lineHeight) || 20;
            const max = line * this.maxRows;
            el.style.height = Math.min(el.scrollHeight, max) + 'px';
            el.style.overflowY = el.scrollHeight > max ? 'auto' : 'hidden';
        },

        submit() {
            const value = this.text.trim();
            if (! value) return;
            this.$dispatch('sigta-composer-submit', { text: value });
            this.text = '';
            this.$nextTick(() => this.resize());
        },
    }"
    class="flex items-end gap-2 rounded-panel border-[1.5px] border-line bg-surface px-2.5 py-2 transition-colors duration-150 ease-out focus-within:border-ink"
>
    <div class="shrink-0">
        @isset($leading)
            {{ $leading }}
        @else
            <x-sigta::button variant="ghost" aria-label="Attach file">
                <x-sigta::icon name="paperclip" size="sm" />
            </x-sigta::button>
        @endisset
    </div>

    <textarea
        x-ref="input"
        x-model="text"
        x-on:input="resize()"
        x-on:keydown.enter="if (! $event.shiftKey) { $event.preventDefault(); submit(); }"
        rows="1"
        placeholder="{{ $placeholder }}"
        @if ($name) name="{{ $name }}" @endif
        {{ $attributes->merge(['id' => $id, 'aria-label' => 'Message', 'class' => $textareaClasses]) }}
    ></textarea>

    <div class="shrink-0">
        <x-sigta::button
            variant="primary"
            size="sm"
            aria-label="Send"
            x-on:click="submit()"
            x-bind:disabled="! text.trim()"
        >
            <x-sigta::icon name="send" size="sm" />
        </x-sigta::button>
    </div>
</div>
