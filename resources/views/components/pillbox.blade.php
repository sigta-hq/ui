{{-- Pillbox: multi-select combobox with removable chips. The hidden input
     carries a JSON array of selected values. --}}
@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'placeholder' => 'Add…',
    'name' => null,
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);
$listboxId = $id.'-listbox';
@endphp

<div
    x-data="sigtaCombobox({ multiple: true })"
    x-on:keydown.escape.prevent="closeList()"
    x-on:focusout="if (! $el.contains($event.relatedTarget)) closeList()"
    {{ $attributes->only('class') }}
    data-sigta="pillbox"
>
    @if ($label)
        <label for="{{ $id }}" data-scope="pillbox" data-part="label">{{ $label }}</label>
    @endif

    <div x-ref="anchor" data-scope="pillbox" data-part="control">
        <template x-for="v in value" :key="v">
            <span data-scope="pillbox" data-part="chip">
                <span x-text="labelFor(v)"></span>
                <button type="button" x-on:click="deselect(v)" x-bind:aria-label="'Remove ' + labelFor(v)" data-scope="pillbox" data-part="remove">
                    <x-sigta::icon name="x" size="xs" />
                </button>
            </span>
        </template>

        <input
            type="text"
            id="{{ $id }}"
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
            placeholder="{{ $placeholder }}"
            @if ($error) aria-invalid="true" @endif
            autocomplete="off"
            data-scope="pillbox"
            data-part="input"
        >
    </div>

    <input type="hidden" x-ref="value" @if ($name) name="{{ $name }}" @endif {{ $attributes->except(['class', 'id']) }}>

    <div
        x-cloak
        x-show="open"
        x-ref="panel"
        id="{{ $listboxId }}"
        role="listbox"
        aria-multiselectable="true"
        data-scope="pillbox"
        data-part="listbox"
    >
        {{ $slot }}
        <div x-cloak x-show="matches === 0" data-scope="pillbox" data-part="empty">No matches.</div>
    </div>

    @if ($description && ! $error)
        <p data-scope="pillbox" data-part="description">{{ $description }}</p>
    @endif
    @if ($error)
        <p data-scope="pillbox" data-part="error">{{ $error }}</p>
    @endif
</div>
