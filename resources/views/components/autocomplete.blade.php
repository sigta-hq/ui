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
    {{ $attributes->only('class')->merge(['class' => 'relative']) }}
    data-sigta="autocomplete"
>
    @if ($label)
        <label for="{{ $id }}" class="block font-mono text-[0.68rem] tracking-[0.08em] uppercase text-graphite mb-1.5">{{ $label }}</label>
    @endif

    <input
        type="text"
        id="{{ $id }}"
        x-ref="anchor"
        role="combobox"
        aria-autocomplete="list"
        aria-controls="{{ $listboxId }}"
        x-bind:aria-expanded="open"
        x-bind:aria-activedescendant="activeId"
        x-model="query"
        x-on:input="filter()"
        x-on:click="openList()"
        x-on:keydown.down.prevent="move(1)"
        x-on:keydown.up.prevent="move(-1)"
        x-on:keydown.enter.prevent="chooseActive()"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        autocomplete="off"
        class="w-full font-body text-[0.98rem] text-ink bg-surface border-[1.5px] rounded-[5px] px-3 py-2 transition-colors focus:outline-none focus:border-ink focus:shadow-[0_0_0_3px_var(--sigta-line)] {{ $error ? 'border-danger' : 'border-line' }}"
    >

    <input type="hidden" x-ref="value" @if ($name) name="{{ $name }}" @endif {{ $attributes->except(['class', 'id']) }}>

    <div
        x-cloak
        x-show="open"
        x-ref="panel"
        id="{{ $listboxId }}"
        role="listbox"
        class="fixed z-50 max-h-64 overflow-y-auto bg-surface-raised border border-line rounded-panel shadow-paper py-1.5"
    >
        {{ $slot }}
        <div x-show="visible().length === 0" class="px-3.5 py-2 font-mono text-[0.72rem] text-graphite">No matches.</div>
    </div>

    @if ($description && ! $error)
        <p class="mt-1.5 text-[0.85rem] text-graphite">{{ $description }}</p>
    @endif
    @if ($error)
        <p class="mt-1.5 font-mono text-[0.72rem] text-danger">{{ $error }}</p>
    @endif
</div>
