{{-- Kanban column: mono header + count badge, and a droppable body (role="list").
     The board (x-data root) drives dragover/drop; `overColumn` highlights the
     active drop target. `id` identifies the column in the sigta-kanban-move
     event; `count` defaults to the number of cards in the slot.

     Header composition: pass a `heading` slot to replace the visible label with
     your own control (e.g. an inline-rename input) while the count and `actions`
     stay put; pass an `actions` slot (a menu, a delete button) that sits to the
     left of the count, which stays pinned to the right. The body's aria-label
     comes from the dedicated string `aria` prop
     (falling back to `label`), so slotted header markup never leaks into the
     accessible name.

     Set `sortable` to let the column be dragged into a new position on the board.
     It grows a grip handle at the start of the header; dragging the handle
     dispatches `sigta-kanban-column-move` { column, position } from the board.
     Only the handle starts a column drag, so it never competes with dragging a
     card or clicking the header controls. --}}
@props([
    'label' => null,
    'id' => null,
    'count' => null,
    'aria' => null,
    'sortable' => false,
])

@php
$id ??= 'col-'.\Illuminate\Support\Str::random(6);
$count ??= substr_count((string) $slot, 'data-sigta="kanban.card"');
$aria ??= $label;
@endphp

<div
    data-sigta="kanban.column"
    data-column="{{ $id }}"
    @if ($sortable) x-bind:data-col-dragging="draggedColumn === '{{ $id }}' ? '' : null" @endif
    {{ $attributes->merge(['class' => 'flex w-72 shrink-0 flex-col rounded-panel border border-line bg-surface'.($sortable ? ' data-[col-dragging]:opacity-40' : '')]) }}
>
    <div class="flex items-center justify-between gap-2 px-3.5 py-3 border-b border-line">
        @if ($sortable)
            <button
                type="button"
                draggable="true"
                x-on:dragstart="columnStart('{{ $id }}', $event)"
                class="shrink-0 cursor-grab text-graphite transition-colors hover:text-ink active:cursor-grabbing"
                aria-label="{{ $aria ? 'Reorder '.$aria : 'Reorder column' }}"
                data-sigta="kanban.column-handle"
            >
                <x-sigta::icon name="grip-vertical" size="xs" />
            </button>
        @endif
        @isset($heading)
            <div class="min-w-0 flex-1">{{ $heading }}</div>
        @else
            <span class="min-w-0 flex-1 font-mono text-[0.7rem] tracking-[0.14em] uppercase text-graphite">{{ $label }}</span>
        @endisset
        <div class="flex shrink-0 items-center gap-1.5">
            @isset($actions)
                {{ $actions }}
            @endisset
            <x-sigta::badge>{{ $count }}</x-sigta::badge>
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
