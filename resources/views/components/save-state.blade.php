{{-- Save state — the Sigta compass stands in for "Saving…". It draws while a
     save is in flight, lands with a handwritten "Saved" (the complete mark),
     and shakes red when the save fails, validation errors included.

     In a Livewire component it follows the actions and/or properties named in
     `target` on that component by itself:

       <sigta:save-state target="save" />
       <sigta:save-state target="form.title, form.body" idle="All changes saved" />

     Anywhere else, drive it by name: $sigta.save(promise, 'profile'), or
     $sigta.saveState('dirty', 'profile') for the "unsaved changes" hint. --}}
@props([
    'target' => null,
    'name' => null,
    'state' => 'idle',
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

$hidden = fn (string $when) => $when === $state ? '' : 'style="display: none"';
@endphp

<span
    data-sigta="save-state"
    x-data="sigtaSaveState"
    data-state="{{ $state }}"
    x-bind:data-state="state"
    @if ($target) data-target="{{ $target }}" @endif
    @if ($name) data-name="{{ $name }}" @endif
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 font-mono text-[0.72rem] tracking-[0.06em] text-graphite']) }}
>
    <x-sigta::loader size="sm" decorative :state="$mark" x-bind:data-state="mark" x-bind:class="{ 'text-ink': state === 'saving' || state === 'saved' }" />
    <span role="status" aria-live="polite" class="inline-flex items-center min-h-5">
        <span x-show="state === 'dirty'" {!! $hidden('dirty') !!}>{{ $dirty }}</span>
        {{-- The compass is the visible cue while saving; the words are for
             assistive tech, and for anyone who has asked for less motion. --}}
        <span x-show="state === 'saving'" class="sr-only motion-reduce:not-sr-only" {!! $hidden('saving') !!}>{{ $saving }}</span>
        <span x-show="state === 'saved'" class="sigta-write font-hand font-semibold text-[1.15rem] leading-none tracking-normal text-ink -rotate-3" {!! $hidden('saved') !!}>{{ $saved }}</span>
        <span x-show="state === 'failed'" class="text-danger" {!! $hidden('failed') !!}>{{ $failed }}</span>
        @if ($idle)
            <span x-show="state === 'idle'" {!! $hidden('idle') !!}>{{ $idle }}</span>
        @endif
    </span>
</span>
