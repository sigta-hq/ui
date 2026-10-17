{{-- Save state — the Sigta compass stands in for "Saving…". It draws while a
     save is in flight, lands with a handwritten "Saved" (the complete mark),
     and shakes red when the save fails, validation errors included.

     In a Livewire component it follows the actions and/or properties named in
     `target` on that component by itself:

       <sigta:save-state target="save" />
       <sigta:save-state target="form.title, form.body" idle="All changes saved" />

     Anywhere else, drive it by name: $sigta.save(promise, 'profile'), or
     $sigta.saveState('dirty', 'profile') for the "unsaved changes" hint.

     It never moves: all its labels share one grid cell, so it keeps the width
     of the longest whatever it is saying. At the right edge of a bar, give it
     align="end" so the compass sits flush right and the words grow leftward. --}}
@props([
    'target' => null,
    'name' => null,
    'state' => 'idle',
    'align' => 'start',
    'idle' => null,
    'dirty' => 'Unsaved changes',
    'saving' => 'Saving…',
    'saved' => 'Saved',
    'failed' => 'Not saved',
])

@php
$state = in_array($state, ['idle', 'dirty', 'saving', 'saved', 'failed'], true) ? $state : 'idle';

$mark = match ($state) {
    'dirty' => 'queued',
    'saving' => 'working',
    'saved' => 'done',
    'failed' => 'failed',
    default => 'idle',
};
@endphp

<span
    data-sigta="save-state"
    x-data="sigtaSaveState"
    data-state="{{ $state }}"
    x-bind:data-state="state"
    @if ($target) data-target="{{ $target }}" @endif
    @if ($name) data-name="{{ $name }}" @endif
    {{ $attributes->merge(['data-align' => $align]) }}
>
    <x-sigta::loader size="sm" decorative :state="$mark" x-bind:data-state="mark" data-scope="save-state" data-part="loader" />
    {{-- One cell, every label: only the current one is visible. The compass
         is the visible cue while saving; its words are for assistive tech,
         and for anyone who has asked for less motion. --}}
    <span role="status" aria-live="polite" data-scope="save-state" data-part="labels">
        <span data-scope="save-state" data-part="label" data-when="dirty">{{ $dirty }}</span>
        <span data-scope="save-state" data-part="label" data-when="saving">{{ $saving }}</span>
        <span data-scope="save-state" data-part="label" data-when="saved">{{ $saved }}</span>
        <span data-scope="save-state" data-part="label" data-when="failed">{{ $failed }}</span>
        @if ($idle)
            <span data-scope="save-state" data-part="label" data-when="idle">{{ $idle }}</span>
        @endif
    </span>
</span>
