{{-- Draggable kanban card. `draggable="true"` + native dragstart/dragend wire
     into the board's x-data. Keyboard users get aria-roledescription="draggable"
     and tabindex=0 (a full keyboard-move menu is intentionally out of scope). --}}
@props([
    'id' => null,
])

@php
$id ??= 'card-'.\Illuminate\Support\Str::random(6);
@endphp

<div
    data-sigta="kanban.card"
    data-card-id="{{ $id }}"
    role="listitem"
    draggable="true"
    tabindex="0"
    aria-roledescription="draggable"
    x-bind:data-dragging="draggedId === '{{ $id }}' ? '' : null"
    x-on:dragstart="start('{{ $id }}', $event)"
    {{ $attributes->merge(['class' => 'group cursor-grab active:cursor-grabbing select-none rounded-base border border-line bg-surface-raised px-3 py-2.5 font-body text-[0.9rem] leading-snug text-ink transition duration-150 ease-out hover:-translate-y-[2px] hover:shadow-paper focus-visible:-translate-y-[2px] data-[dragging]:opacity-40 data-[dragging]:cursor-grabbing']) }}
>
    {{ $slot }}
</div>
