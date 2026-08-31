{{-- Kanban column: mono header + count badge, and a droppable body (role="list").
     The board (x-data root) drives dragover/drop; `overColumn` highlights the
     active drop target. `id` identifies the column in the sigta-kanban-move
     event; `count` defaults to the number of cards in the slot.

     Header composition: pass a `heading` slot to replace the visible label with
     your own control (e.g. an inline-rename input) while the count and `actions`
     stay put; pass an `actions` slot (a menu, a delete button) to sit beside the
     count. The body's aria-label comes from the dedicated string `aria` prop
     (falling back to `label`), so slotted header markup never leaks into the
     accessible name. --}}
@props([
    'label' => null,
    'id' => null,
    'count' => null,
    'aria' => null,
])

@php
$id ??= 'col-'.\Illuminate\Support\Str::random(6);
$count ??= substr_count((string) $slot, 'data-sigta="kanban.card"');
$aria ??= $label;
@endphp

<div
    data-sigta="kanban.column"
    data-column="{{ $id }}"
    {{ $attributes->merge(['class' => 'flex w-72 shrink-0 flex-col rounded-panel border border-line bg-surface']) }}
>
    <div class="flex items-center justify-between gap-2 px-3.5 py-3 border-b border-line">
        @isset($heading)
            <div class="min-w-0 flex-1">{{ $heading }}</div>
        @else
            <span class="font-mono text-[0.7rem] tracking-[0.14em] uppercase text-graphite">{{ $label }}</span>
        @endisset
        <div class="flex shrink-0 items-center gap-1.5">
            <x-sigta::badge>{{ $count }}</x-sigta::badge>
            @isset($actions)
                {{ $actions }}
            @endisset
        </div>
    </div>

    <div
        role="list"
        aria-label="{{ $aria }}"
        data-kanban-body
        data-column-id="{{ $id }}"
        x-on:dragover.prevent="over($event, '{{ $id }}')"
        x-on:drop.prevent="drop($event, '{{ $id }}')"
        x-bind:class="overColumn === '{{ $id }}' ? 'border-ink bg-ink/5' : 'border-transparent'"
        class="flex flex-1 flex-col gap-2.5 min-h-24 p-2.5 rounded-b-panel border-[1.5px] border-dashed transition-colors duration-150 ease-out"
    >
        {{ $slot }}
    </div>
</div>
