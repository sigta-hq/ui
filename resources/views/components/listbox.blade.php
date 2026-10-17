{{-- Listbox (rich single select): button trigger on sigtaCombobox over
     slotted <x-sigta::autocomplete.option>s. The hidden input carries
     name/wire:model; the button shows the current selection.
     Usage:
       <x-sigta::listbox label="Country" name="country" placeholder="Pick one…">
           <x-sigta::autocomplete.option value="dk">Denmark</x-sigta::autocomplete.option>
       </x-sigta::listbox> --}}
@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'placeholder' => 'Select…',
    'name' => null,
    'value' => null,
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);
$listboxId = $id.'-listbox';

$buttonAttributes = ['id' => $id];

if ($error) {
    $buttonAttributes['aria-invalid'] = 'true';
}

if ($describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''))) {
    $buttonAttributes['aria-describedby'] = $describedBy;
}
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <div
        x-data="sigtaCombobox({ initial: {{ \Illuminate\Support\Js::from($value) }} })"
        x-on:keydown.escape.prevent="closeList()"
        x-on:keydown.down.prevent="move(1)"
        x-on:keydown.up.prevent="move(-1)"
        x-on:keydown.enter.prevent="open ? chooseActive() : openList()"
        x-on:focusout="if (! $el.contains($event.relatedTarget)) closeList()"
        {{ $attributes->only('class') }}
        data-sigta="listbox"
    >
        <button
            type="button"
            x-ref="anchor"
            role="combobox"
            aria-haspopup="listbox"
            aria-controls="{{ $listboxId }}"
            aria-expanded="false"
            x-bind:aria-expanded="open"
            x-bind:aria-activedescendant="activeId"
            x-on:click="open ? closeList() : openList()"
            data-scope="listbox"
            data-part="trigger"
            {{ (new \Illuminate\View\ComponentAttributeBag)->merge($buttonAttributes) }}
        >
            {{-- The placeholder until Alpine starts, then the chosen option's text. --}}
            <span
                x-text="value ? labelFor(value) : {{ \Illuminate\Support\Js::from($placeholder) }}"
                data-empty=""
                x-bind:data-empty="value ? null : ''"
                data-scope="listbox"
                data-part="value"
            >{{ $placeholder }}</span>
            <span aria-hidden="true" data-scope="listbox" data-part="chevron">
                <x-sigta::icon name="chevron-down" size="sm" />
            </span>
        </button>

        <input
            type="hidden"
            x-ref="value"
            @if ($name) name="{{ $name }}" @endif
            @if ($value !== null) value="{{ $value }}" @endif
            {{ $attributes->except(['class', 'id']) }}
        >

        <div
            x-cloak
            x-show="open"
            x-ref="panel"
            x-on:click.outside="closeList()"
            id="{{ $listboxId }}"
            role="listbox"
            data-scope="listbox"
            data-part="listbox"
        >
            {{ $slot }}
        </div>
    </div>
</x-sigta::field>
