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

$buttonClasses = 'w-full flex items-center justify-between gap-2 text-left font-body text-[0.98rem] text-ink bg-surface border-[1.5px] rounded-[5px] px-[0.8em] py-[0.62em] cursor-pointer transition duration-150 ease-out focus:outline-none focus:border-ink focus:shadow-[0_0_0_3px_color-mix(in_srgb,var(--sigta-fg)_8%,transparent)] disabled:opacity-40 disabled:pointer-events-none';
$buttonClasses .= $error ? ' border-danger' : ' border-line';

$buttonAttributes = ['id' => $id, 'class' => $buttonClasses];

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
        {{ $attributes->only('class')->merge(['class' => 'relative']) }}
        data-sigta="listbox"
    >
        <button
            type="button"
            x-ref="anchor"
            role="combobox"
            aria-haspopup="listbox"
            aria-controls="{{ $listboxId }}"
            x-bind:aria-expanded="open"
            x-bind:aria-activedescendant="activeId"
            x-on:click="open ? closeList() : openList()"
            {{ (new \Illuminate\View\ComponentAttributeBag)->merge($buttonAttributes) }}
        >
            <span
                x-text="value ? labelFor(value) : {{ \Illuminate\Support\Js::from($placeholder) }}"
                x-bind:class="value ? 'text-ink' : 'font-mono text-[0.78rem] text-graphite'"
            >{{ $placeholder }}</span>
            <span aria-hidden="true" class="shrink-0 text-graphite transition-transform duration-150" x-bind:class="open ? 'rotate-180' : ''">
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
            class="fixed z-50 max-h-64 overflow-y-auto bg-surface-raised border border-line rounded-panel shadow-paper py-1.5"
        >
            {{ $slot }}
        </div>
    </div>
</x-sigta::field>
