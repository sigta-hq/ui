{{-- Autocomplete: filtering combobox over slotted <x-sigta::autocomplete.option>s.
     The hidden input carries name/wire:model; the visible input is the query box. --}}
@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'placeholder' => null,
    'name' => null,
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);
$listboxId = $id.'-listbox';
@endphp

<div
    x-data="sigtaCombobox()"
    x-on:keydown.escape.prevent="closeList()"
    x-on:focusout="if (! $el.contains($event.relatedTarget)) closeList()"
    {{ $attributes->only('class') }}
    data-sigta="autocomplete"
>
    @if ($label)
        <label for="{{ $id }}" data-scope="autocomplete" data-part="label">{{ $label }}</label>
    @endif

    <input
        type="text"
        id="{{ $id }}"
        x-ref="anchor"
        role="combobox"
        aria-autocomplete="list"
        aria-controls="{{ $listboxId }}"
        aria-expanded="false"
        x-bind:aria-expanded="open"
        x-bind:aria-activedescendant="activeId"
        x-model="query"
        x-on:input="filter()"
        x-on:click="openList()"
        x-on:keydown.down.prevent="move(1)"
        x-on:keydown.up.prevent="move(-1)"
        x-on:keydown.enter.prevent="chooseActive()"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($error) aria-invalid="true" @endif
        autocomplete="off"
        data-scope="autocomplete"
        data-part="input"
    >

    <input type="hidden" x-ref="value" @if ($name) name="{{ $name }}" @endif {{ $attributes->except(['class', 'id']) }}>

    <div
        x-cloak
        x-show="open"
        x-ref="panel"
        id="{{ $listboxId }}"
        role="listbox"
        data-scope="autocomplete"
        data-part="listbox"
    >
        {{ $slot }}
        <div x-cloak x-show="matches === 0" data-scope="autocomplete" data-part="empty">No matches.</div>
    </div>

    @if ($description && ! $error)
        <p data-scope="autocomplete" data-part="description">{{ $description }}</p>
    @endif
    @if ($error)
        <p data-scope="autocomplete" data-part="error">{{ $error }}</p>
    @endif
</div>
