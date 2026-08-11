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
    {{ $attributes->only('class')->merge(['class' => 'relative']) }}
    data-sigta="pillbox"
>
    @if ($label)
        <label for="{{ $id }}" class="block font-mono text-[0.68rem] tracking-[0.08em] uppercase text-graphite mb-1.5">{{ $label }}</label>
    @endif

    <div x-ref="anchor" class="flex flex-wrap items-center gap-1.5 bg-surface border-[1.5px] rounded-[5px] px-2 py-1.5 focus-within:border-ink transition-colors {{ $error ? 'border-danger' : 'border-line' }}">
        <template x-for="v in value" :key="v">
            <span class="inline-flex items-center gap-1.5 font-mono text-[0.68rem] tracking-[0.05em] px-2.5 py-1 rounded-full border-[1.5px] border-ink text-ink">
                <span x-text="labelFor(v)"></span>
                <button type="button" x-on:click="deselect(v)" x-bind:aria-label="'Remove ' + labelFor(v)" class="cursor-pointer opacity-60 hover:opacity-100">
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
            x-bind:aria-expanded="open"
            x-bind:aria-activedescendant="activeId"
            x-model="query"
            x-on:input="filter()"
            x-on:click="openList()"
            x-on:keydown.down.prevent="move(1)"
            x-on:keydown.up.prevent="move(-1)"
            x-on:keydown.enter.prevent="chooseActive()"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            class="flex-1 min-w-24 font-body text-[0.95rem] text-ink bg-transparent px-1 py-0.5 focus:outline-none placeholder:text-graphite"
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
