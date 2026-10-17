{{-- Draggable kanban card. `draggable="true"` + native dragstart/dragend wire
     into the board's x-data. Keyboard users get aria-roledescription="draggable"
     and tabindex=0 (a full keyboard-move menu is intentionally out of scope).
     The look is the contract's kanban.css; `group` stays as a hook, so
     group-hover utilities in the card's content react to hovering the card. --}}
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
    {{ $attributes->merge(['class' => 'group']) }}
>
    {{ $slot }}
</div>
