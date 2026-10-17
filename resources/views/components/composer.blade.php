{{-- Chat composer: auto-growing textarea with a leading (attach) slot and a
     send button. Enter submits, Shift+Enter inserts a newline. Submitting
     dispatches a bubbling `sigta-composer-submit` event with { text } from the
     textarea and clears the field:

         <x-sigta::composer x-on:sigta-composer-submit="$wire.send($event.detail.text)" />

     Attributes (name, wire:model, id, aria-*) forward onto the native textarea.
     The behavior is sigtaComposer in resources/js/sigta.js. --}}
@props([
    'placeholder' => 'Write a message…',
    'name' => null,
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);
@endphp

<div data-sigta="composer" x-data="sigtaComposer">
    <div data-scope="composer" data-part="leading">
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
        data-scope="composer"
        data-part="input"
        {{ $attributes->merge(['id' => $id, 'aria-label' => 'Message']) }}
    ></textarea>

    <div data-scope="composer" data-part="send">
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
